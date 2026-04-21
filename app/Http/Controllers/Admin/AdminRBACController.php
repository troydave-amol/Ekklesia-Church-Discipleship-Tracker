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
        return view('admin.rbac.show', [
            'tenant' => $tenant,
            'roles' => Role::all(),
            'permissions' => Permission::all(),
        ]);
    }

    /**
     * Show role management page
     */
    public function editRoles(Tenant $tenant): View
    {
        return view('admin.rbac.edit-roles', [
            'tenant' => $tenant,
            'roles' => Role::with('permissions')->get(),
        ]);
    }

    /**
     * Update role permissions for a tenant
     */
    public function updateRolePermissions(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $permissions = $validated['permissions'] ?? [];

        // Get old permissions for history
        $oldPermissions = $role->permissions->pluck('id')->toArray();

        // Sync permissions
        $role->permissions()->sync($permissions);

        \App\Models\VersionHistory::logChange(
            'role_permissions',
            $role->id,
            auth('admin')->id(),
            'updated',
            "Updated permissions for role: {$role->name} (Tenant: {$tenant->name})",
            ['permissions' => $oldPermissions],
            ['permissions' => $permissions]
        );

        return back()->with('success', "Permissions updated for {$role->name}.");
    }

    /**
     * Show audit log for RBAC changes
     */
    public function auditLog(Tenant $tenant): View
    {
        return view('admin.rbac.audit-log', [
            'tenant' => $tenant,
            'history' => \App\Models\VersionHistory::query()
                ->where('entity_type', 'role_permissions')
                ->orWhere('entity_type', 'permission')
                ->latest()
                ->paginate(30),
        ]);
    }
}
