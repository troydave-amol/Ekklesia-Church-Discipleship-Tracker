@extends('layouts.central-admin')

@section('title', 'RBAC - Edit Roles - ' . $tenant->name)
@section('topbar-title', 'Edit Roles: ' . $tenant->name)

@section('content')
<div class="ca-page-head">
    <h1 class="ca-page-title">Manage Role Permissions</h1>
    <p class="ca-page-desc">Assign permissions to roles for {{ $tenant->name }}.</p>
</div>

<div class="ca-form-card">
    <form action="{{ route('admin.rbac.update-roles', $tenant) }}" method="post" class="ca-form">
        @csrf
        @method('PUT')

        @forelse($roles as $role)
            <div class="ca-role-section">
                <div class="ca-role-header">
                    <h3>{{ $role->name }}</h3>
                    <p>{{ $role->description ?? 'No description' }}</p>
                </div>

                <div class="ca-permissions-grid">
                    @forelse($permissions as $perm)
                        <label class="ca-permission-checkbox">
                            <input type="checkbox" name="role_permissions[{{ $role->id }}][]" value="{{ $perm->id }}"
                                {{ $role->permissions->contains($perm->id) ? 'checked' : '' }}>
                            <span class="ca-perm-name">{{ $perm->name }}</span>
                            <span class="ca-perm-desc">{{ $perm->description ?? '' }}</span>
                        </label>
                    @empty
                        <p style="color: #9ca3af; grid-column: 1 / -1;">No permissions available.</p>
                    @endforelse
                </div>
            </div>
        @empty
            <p style="color: #9ca3af;">No roles to manage.</p>
        @endforelse

        <div class="ca-form-actions">
            <a href="{{ route('admin.rbac.show', $tenant) }}" class="ca-btn ca-btn-ghost">Cancel</a>
            <button type="submit" class="ca-btn ca-btn-primary">Save Permissions</button>
        </div>
    </form>
</div>

<style>
.ca-form-card { background: white; border-radius: 8px; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.ca-role-section { margin-bottom: 2.5rem; padding-bottom: 2rem; border-bottom: 1px solid #e5e7eb; }
.ca-role-section:last-of-type { border-bottom: none; }
.ca-role-header h3 { margin: 0 0 0.5rem 0; font-size: 16px; font-weight: 600; color: #1f2937; }
.ca-role-header p { margin: 0; color: #6b7280; font-size: 13px; }
.ca-permissions-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; margin-top: 1rem; }
.ca-permission-checkbox { display: flex; flex-direction: column; align-items: flex-start; padding: 0.75rem; background: #f9fafb; border-radius: 6px; cursor: pointer; border: 1px solid #e5e7eb; transition: all 0.2s; }
.ca-permission-checkbox:hover { background: #f3f4f6; border-color: #d1d5db; }
.ca-permission-checkbox input { cursor: pointer; margin-bottom: 0.5rem; }
.ca-permission-checkbox input:checked ~ .ca-perm-name { color: #2563eb; font-weight: 500; }
.ca-perm-name { font-weight: 500; color: #1f2937; font-size: 13px; }
.ca-perm-desc { font-size: 11px; color: #9ca3af; margin-top: 0.25rem; }
.ca-form-actions { display: flex; gap: 1rem; margin-top: 2rem; }
.ca-btn { padding: 0.75rem 1.5rem; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
.ca-btn-primary { background: #2563eb; color: white; }
.ca-btn-primary:hover { background: #1d4ed8; }
.ca-btn-ghost { background: #f3f4f6; color: #374151; }
.ca-btn-ghost:hover { background: #e5e7eb; }
</style>
@endsection
