@extends('layouts.central-admin')

@section('title', 'RBAC Management - ' . $tenant->name)
@section('topbar-title', 'RBAC: ' . $tenant->name)

@section('content')
<div class="ca-page-head">
    <h1 class="ca-page-title">Role-Based Access Control</h1>
    <p class="ca-page-desc">Manage roles and permissions for {{ $tenant->name }}. Customize access levels for different user groups.</p>
</div>

{{-- ═══ QUICK ACTIONS ═══ --}}
<div class="ca-grid-2" style="margin-bottom: 2rem;">
    <a href="{{ route('admin.rbac.edit-roles', $tenant) }}" class="ca-cta ca-cta-gold">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3m0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22"/>
        </svg>
        Manage Roles
    </a>
    <a href="{{ route('admin.rbac.audit-log', $tenant) }}" class="ca-cta ca-cta-ghost">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        View Audit Log
    </a>
</div>

{{-- ═══ ROLES OVERVIEW ═══ --}}
<div class="ca-table-card">
    <h3 style="margin: 0 0 1rem 0; font-size: 16px; font-weight: 600; color: #1f2937;">Current Roles</h3>
    <table class="ca-table">
        <thead>
            <tr>
                <th>Role Name</th>
                <th>Description</th>
                <th>Permissions Count</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
                <tr>
                    <td class="ca-table-strong">{{ $role->name }}</td>
                    <td>{{ $role->description ?? 'No description' }}</td>
                    <td>
                        <span class="ca-badge">{{ $role->permissions->count() }} permission{{ $role->permissions->count() !== 1 ? 's' : '' }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.rbac.edit-roles', $tenant) }}" class="ca-btn ca-btn-link">Manage →</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="ca-table-empty">No roles defined.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ═══ PERMISSIONS LIST ═══ --}}
<div class="ca-card" style="margin-top: 2rem;">
    <div class="ca-card-head">
        <span class="ca-card-title">Available Permissions</span>
    </div>
    <div class="ca-card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem;">
            @forelse($permissions as $perm)
                <div style="padding: 1rem; background: #f9fafb; border-radius: 6px; border-left: 3px solid #2563eb;">
                    <div style="font-weight: 600; color: #1f2937; font-size: 14px;">{{ $perm->name }}</div>
                    <div style="color: #6b7280; font-size: 12px; margin-top: 0.25rem;">{{ $perm->description ?? 'No description' }}</div>
                </div>
            @empty
                <p style="color: #9ca3af; font-size: 13px;">No permissions defined.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ═══ BACK LINK ═══ --}}
<div style="margin-top: 2rem;">
    <a href="{{ route('admin.tenants.show', $tenant) }}" class="ca-btn ca-btn-ghost">← Back to Tenant</a>
</div>
@endsection
