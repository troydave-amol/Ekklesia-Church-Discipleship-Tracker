<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminRBACController extends Controller
{
    /**
     * View RBAC overview for a tenant
     */
    public function show(Tenant $tenant): View
    {
        $data = $tenant->run(function () {
            return [
                'roles' => Role::with('permissions')->get(),
                'permissions' => Permission::all(),
            ];
        });

        return view('admin.rbac.show', [
            'tenant' => $tenant,
            'roles' => $data['roles'],
            'permissions' => $data['permissions'],
        ]);
    }

    /**
     * Show role management page
     */
    public function editRoles(Tenant $tenant): View
    {
        $data = $tenant->run(function () {
            return [
                'roles' => Role::with('permissions')->get(),
                'permissions' => Permission::all(),
            ];
        });

        return view('admin.rbac.edit-roles', [
            'tenant' => $tenant,
            'roles' => $data['roles'],
            'permissions' => $data['permissions'],
        ]);
    }

    /**
     * Update role permissions for a specific tenant role
     */
    public function updateRolePermissions(Request $request, Tenant $tenant, int $role): RedirectResponse
    {
        $validated = $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'integer',
        ]);

        $permissions = $validated['permissions'] ?? [];

        // Execute inside the tenant's database context
        $result = $tenant->run(function () use ($role, $permissions) {
            $tenantRole = Role::findOrFail($role);
            $oldPermissions = $tenantRole->permissions->pluck('id')->toArray();

            $tenantRole->permissions()->sync($permissions);

            return [
                'roleName' => $tenantRole->name,
                'old' => $oldPermissions,
                'new' => $permissions,
            ];
        });

        // Log on the central database (AdminAuditLog uses the central connection)
        \App\Models\AdminAuditLog::logChange(
            'role_permissions',
            $role,
            auth('admin')->id(),
            'updated',
            "Updated permissions for role: {$result['roleName']} (Tenant: {$tenant->name})",
            ['permissions' => $result['old']],
            ['permissions' => $result['new']]
        );

        return back()->with('success', "Permissions updated for {$result['roleName']}.");
    }

    /**
     * Show audit log for RBAC changes
     */
    public function auditLog(Tenant $tenant): View
    {
        // AdminAuditLog lives on the central DB — filter by this tenant's name
        $history = \App\Models\AdminAuditLog::query()
            ->where(function ($q) {
                $q->where('resource_type', 'role_permissions')
                  ->orWhere('resource_type', 'permission');
            })
            ->latest()
            ->paginate(30);

        return view('admin.rbac.audit-log', [
            'tenant' => $tenant,
            'history' => $history,
        ]);
    }
}

