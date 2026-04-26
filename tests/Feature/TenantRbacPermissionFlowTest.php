<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use Tests\TenantTestCase;

class TenantRbacPermissionFlowTest extends TenantTestCase
{
    public function test_disciple_dashboard_access_respects_dashboard_view_permission(): void
    {
        $disciple = $this->createTenantUser('disciple', [
            'email' => 'disciple@example.com',
        ]);

        $this->actingAs($disciple)
            ->get(route('tenant.dashboard', absolute: false))
            ->assertOk();

        $discipleRole = Role::query()->where('slug', 'disciple')->firstOrFail();
        $discipleRole->permissions()->detach(
            Permission::query()->where('slug', 'dashboard.view')->value('id')
        );

        $this->actingAs($disciple)
            ->getJson(route('tenant.dashboard', absolute: false))
            ->assertForbidden();
    }

    public function test_cell_leader_attendance_and_devotional_pages_respect_role_permissions(): void
    {
        $leader = $this->createTenantUser('cell_leader', [
            'email' => 'cellleader@example.com',
        ]);

        $this->actingAs($leader)
            ->get(route('attendance.index', absolute: false))
            ->assertOk();

        $this->actingAs($leader)
            ->get(route('devotionals.index', absolute: false))
            ->assertOk();

        $cellLeaderRole = Role::query()->where('slug', 'cell_leader')->firstOrFail();
        $remainingPermissionIds = Permission::query()
            ->whereIn('slug', ['dashboard.view', 'lessons.manage'])
            ->pluck('id')
            ->all();

        $cellLeaderRole->permissions()->sync($remainingPermissionIds);

        $this->actingAs($leader)
            ->getJson(route('attendance.index', absolute: false))
            ->assertForbidden();

        $this->actingAs($leader)
            ->getJson(route('devotionals.index', absolute: false))
            ->assertForbidden();
    }
}
