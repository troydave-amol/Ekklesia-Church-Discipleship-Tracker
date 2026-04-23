@extends('layouts.central-admin')

@section('title', 'RBAC - Edit Roles - ' . $tenant->name)
@section('topbar-title', 'Edit Roles: ' . $tenant->name)

@section('content')
<a href="{{ route('admin.rbac.show', $tenant) }}" class="ca-back">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="19" y1="12" x2="5" y2="12"/>
        <polyline points="12 19 5 12 12 5"/>
    </svg>
    Back to RBAC Overview
</a>

<div class="ca-page-head">
    <h1 class="ca-page-title">Manage Role Permissions</h1>
    <p class="ca-page-desc">Assign permissions to roles for {{ $tenant->name }}. Each role can be saved independently.</p>
</div>

@foreach($roles as $role)
<div class="ca-card" style="margin-bottom: 2rem;">
    <div class="ca-card-head" style="display:flex;align-items:center;justify-content:space-between;">
        <span class="ca-card-title">{{ $role->name }}</span>
        <span class="ca-badge">{{ $role->permissions->count() }} permission{{ $role->permissions->count() !== 1 ? 's' : '' }}</span>
    </div>
    <div class="ca-card-body">
        <form action="{{ route('admin.rbac.update-permissions', [$tenant, $role]) }}" method="POST">
            @csrf
            @method('PATCH')

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                @foreach($permissions as $perm)
                    <label style="display:flex;align-items:flex-start;gap:8px;padding:0.75rem;background:#f9fafb;border-radius:6px;cursor:pointer;border:1px solid #e5e7eb;transition:all 0.2s;">
                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                            {{ $role->permissions->contains('id', $perm->id) ? 'checked' : '' }}
                            style="margin-top:2px;">
                        <div>
                            <div style="font-weight:500;color:#1f2937;font-size:13px;">{{ $perm->name }}</div>
                            <div style="font-size:11px;color:#9ca3af;margin-top:2px;">{{ $perm->slug }}</div>
                        </div>
                    </label>
                @endforeach
            </div>

            <button type="submit" class="ca-cta ca-cta-gold" style="gap:6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Save {{ $role->name }} Permissions
            </button>
        </form>
    </div>
</div>
@endforeach

@if($roles->isEmpty())
    <div class="ca-card">
        <div class="ca-card-body">
            <p style="color:#9ca3af;font-size:13px;text-align:center;">No roles found for this tenant. Run tenant migrations to seed default roles.</p>
        </div>
    </div>
@endif
@endsection

