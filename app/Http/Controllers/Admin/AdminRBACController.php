<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminRBACController extends Controller
{
    /**
     * View RBAC overview for a tenant.
     */
    public function show(Tenant $tenant): View
    {
        return view('admin.rbac.show', $this->tenantRbacViewData($tenant));
    }

    /**
     * Show role management page.
     */
    public function editRoles(Tenant $tenant): View
    {
        return view('admin.rbac.edit-roles', $this->tenantRbacViewData($tenant));
    }

    /**
     * Update role permissions for a specific tenant role.
     */
    public function updateRolePermissions(Request $request, Tenant $tenant, int $role): RedirectResponse
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'distinct'],
        ]);

        $permissionIds = collect($validated['permissions'] ?? [])
            ->map(static fn (mixed $id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        $result = $tenant->run(function () use ($role, $permissionIds) {
            $tenantRole = Role::query()
                ->with('permissions:id,slug,name')
                ->findOrFail($role);

            $availablePermissionIds = Permission::query()->pluck('id');
            $unknownPermissionIds = collect($permissionIds)->diff($availablePermissionIds);

            if ($unknownPermissionIds->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'permissions' => 'One or more selected permissions do not belong to this tenant.',
                ]);
            }

            $oldPermissionSlugs = $tenantRole->permissions
                ->pluck('slug')
                ->sort()
                ->values()
                ->all();

            $tenantRole->permissions()->sync($permissionIds);
            $tenantRole->load('permissions:id,slug,name');

            $newPermissionSlugs = $tenantRole->permissions
                ->pluck('slug')
                ->sort()
                ->values()
                ->all();

            return [
                'role_id' => $tenantRole->id,
                'role_name' => $tenantRole->name,
                'role_slug' => $tenantRole->slug,
                'old_permissions' => $oldPermissionSlugs,
                'new_permissions' => $newPermissionSlugs,
            ];
        });

        AdminAuditLog::logChange(
            'role_permissions',
            $result['role_id'],
            auth('admin')->id(),
            'updated',
            "Updated permissions for {$result['role_name']} in {$tenant->name}.",
            [
                'role_slug' => $result['role_slug'],
                'permissions' => $result['old_permissions'],
            ],
            [
                'role_slug' => $result['role_slug'],
                'permissions' => $result['new_permissions'],
            ],
            (string) $tenant->id,
        );

        return back()->with('success', "Permissions updated for {$result['role_name']}.");
    }

    /**
     * Show audit log for RBAC changes.
     */
    public function auditLog(Tenant $tenant): View
    {
        $history = AdminAuditLog::query()
            ->with('adminUser')
            ->where('tenant_id', (string) $tenant->id)
            ->where('resource_type', 'role_permissions')
            ->latest()
            ->paginate(30);

        return view('admin.rbac.audit-log', [
            'tenant' => $tenant,
            'history' => $history,
        ]);
    }

    /**
     * @return array{tenant: Tenant, roles: \Illuminate\Support\Collection<int, Role>, permissions: \Illuminate\Support\Collection<int, Permission>}
     */
    private function tenantRbacViewData(Tenant $tenant): array
    {
        $data = $tenant->run(function () {
            return [
                'roles' => Role::query()
                    ->with('permissions:id,slug,name')
                    ->orderBy('id')
                    ->get(),
                'permissions' => Permission::query()
                    ->orderBy('name')
                    ->get(),
            ];
        });

        return [
            'tenant' => $tenant,
            'roles' => $data['roles'],
            'permissions' => $data['permissions'],
        ];
    }
}
