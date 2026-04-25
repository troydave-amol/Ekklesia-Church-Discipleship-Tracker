@extends('layouts.central-admin')

@section('title', 'Admin Users')
@section('topbar-title', 'Admin Users')

@section('content')
<div class="ca-page-head">
    <h1 class="ca-page-title">Admin Staff Management</h1>
    <p class="ca-page-desc">Create and manage administrative users. Super admins only.</p>
    <a href="{{ route('admin.users.create') }}" class="ca-btn ca-btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
        </svg>
        Add Admin
    </a>
</div>

{{-- ═══ TABLE ═══ --}}
<div class="ca-table-card">
    <table class="ca-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Last Login</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($admins as $admin)
                <tr>
                    <td class="ca-table-strong">{{ $admin->name }}</td>
                    <td class="ca-table-email">{{ $admin->email }}</td>
                    <td>
                        <span class="ca-badge">
                            @php
                                $roleLabel = match($admin->role ?? 'admin') {
                                    'super_admin' => 'Super Admin',
                                    'admin' => 'Admin',
                                    'support' => 'Support',
                                    'viewer' => 'Viewer',
                                    default => ucfirst($admin->role ?? 'admin')
                                };
                            @endphp
                            {{ $roleLabel }}
                        </span>
                    </td>
                    <td>
                        @if($admin->is_active)
                            <span class="ca-pill ca-pill-ok">
                                <span class="ca-pill-dot"></span> Active
                            </span>
                        @else
                            <span class="ca-pill ca-pill-no">
                                <span class="ca-pill-dot"></span> Inactive
                            </span>
                        @endif
                    </td>
                    <td class="ca-table-muted">
                        @if($admin->last_login_at)
                            {{ $admin->last_login_at->format('M j, Y H:i') }}
                        @else
                            Never
                        @endif
                    </td>
                    <td>
                        <div class="ca-actions">
                            <a href="{{ route('admin.users.edit', $admin) }}" class="ca-btn ca-btn-link">Edit</a>
                            @if($admin->id !== auth('admin')->id())
                                <form action="{{ route('admin.users.destroy', $admin) }}" method="post" class="ca-inline"
                                      onsubmit="return confirm('Delete this admin user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ca-btn ca-btn-reject" style="font-size: 12px;">Delete</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="ca-table-empty">No admin users.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="ca-pagination">
    {{ $admins->links() }}
</div>

<style>
.ca-actions { display: flex; gap: 0.5rem; }
.ca-btn { padding: 0.5rem 1rem; border-radius: 4px; border: none; cursor: pointer; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem; }
.ca-btn-link { background: none; color: #2563eb; }
.ca-btn-link:hover { text-decoration: underline; }
.ca-btn-reject { background: #fecaca; color: #991b1b; }
.ca-btn-reject:hover { background: #fca5a5; }
.ca-inline { display: inline; }
</style>
@endsection
