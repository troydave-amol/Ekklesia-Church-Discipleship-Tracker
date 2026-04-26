<?php

namespace Tests\Feature\Admin;

use App\Models\AdminAuditLog;
use App\Models\AdminUser;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use Tests\TenantTestCase;

class AdminRBACControllerTest extends TenantTestCase
{
    public function test_superadmin_updates_selected_tenant_role_permissions_and_tenant_access_changes_immediately(): void
    {
        $admin = $this->createAdmin();
        $leader = $this->createTenantUser('cell_leader', [
            'email' => 'cell.leader@example.com',
        ]);

        $this->actingAs($leader)
            ->get(route('lessons.create', absolute: false))
            ->assertOk();

        $role = Role::query()->where('slug', 'cell_leader')->firstOrFail();
        $dashboardPermissionId = Permission::query()
            ->where('slug', 'dashboard.view')
            ->value('id');

        $response = $this->actingAs($admin, 'admin')
            ->from(route('admin.rbac.edit-roles', $this->tenant, false))
            ->patch(route('admin.rbac.update-permissions', [$this->tenant, $role->id], false), [
                'permissions' => [$dashboardPermissionId],
            ]);

        $response->assertRedirect(route('admin.rbac.edit-roles', $this->tenant, false));
        $response->assertSessionHas('success');

        $updatedRole = Role::query()
            ->with('permissions')
            ->where('slug', 'cell_leader')
            ->firstOrFail();

        $this->assertSame(
            ['dashboard.view'],
            $updatedRole->permissions->pluck('slug')->sort()->values()->all()
        );

        $this->assertDatabaseHas('admin_audit_logs', [
            'tenant_id' => (string) $this->tenant->id,
            'resource_type' => 'role_permissions',
            'resource_id' => (string) $role->id,
            'action' => 'update',
        ], 'central');

        $this->actingAs($leader)
            ->getJson(route('lessons.create', absolute: false))
            ->assertForbidden();
    }

    public function test_rbac_audit_page_only_shows_entries_for_the_selected_tenant(): void
    {
        $admin = $this->createAdmin();
        $otherTenant = Tenant::create([
            'id' => 'tenant-'.uniqid(),
            'name' => 'Other Tenant',
            'email' => 'other+'.uniqid().'@example.com',
            'subdomain' => 'other-'.uniqid(),
            'db_name' => 'tenant_'.uniqid(),
            'status' => 'active',
            'subscription_plan' => 'basic',
        ]);

        AdminAuditLog::create([
            'admin_user_id' => $admin->id,
            'tenant_id' => (string) $this->tenant->id,
            'action' => 'update',
            'resource_type' => 'role_permissions',
            'resource_id' => '1',
            'changes' => [
                'description' => 'Visible tenant entry',
                'old' => ['permissions' => ['lessons.manage']],
                'new' => ['permissions' => ['dashboard.view']],
            ],
        ]);

        AdminAuditLog::create([
            'admin_user_id' => $admin->id,
            'tenant_id' => (string) $otherTenant->id,
            'action' => 'update',
            'resource_type' => 'role_permissions',
            'resource_id' => '1',
            'changes' => [
                'description' => 'Hidden tenant entry',
                'old' => ['permissions' => ['attendance.manage']],
                'new' => ['permissions' => ['devotionals.manage']],
            ],
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.rbac.audit-log', $this->tenant, false));

        $response->assertOk();
        $response->assertSeeText('Visible tenant entry');
        $response->assertDontSeeText('Hidden tenant entry');
        $response->assertSeeText($admin->name);
    }

    private function createAdmin(string $role = 'superadmin'): AdminUser
    {
        return AdminUser::create([
            'name' => 'Central Admin',
            'email' => 'admin+'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'role' => $role,
            'is_active' => true,
        ]);
    }
}
