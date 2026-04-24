@extends('layouts.central-admin')

@section('title', 'Edit Admin: ' . $admin->name)
@section('topbar-title', 'Edit Admin: ' . $admin->name)

@section('content')
<div class="ca-page-head">
    <h1 class="ca-page-title">Edit Admin User</h1>
    <p class="ca-page-desc">Update {{ $admin->name }}'s account details and permissions.</p>
</div>

<div class="ca-form-card">
    <form action="{{ route('admin.users.update', $admin) }}" method="post" class="ca-form">
        @csrf
        @method('PUT')

        <div class="ca-form-group">
            <label class="ca-label">Full Name</label>
            <input type="text" name="name" class="ca-input" value="{{ old('name', $admin->name) }}" required>
            @error('name')
                <span class="ca-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="ca-form-group">
            <label class="ca-label">Email Address</label>
            <input type="email" name="email" class="ca-input" value="{{ old('email', $admin->email) }}" required>
            @error('email')
                <span class="ca-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="ca-form-group">
            <label class="ca-label">Role</label>
            <select name="role" class="ca-select" required>
                <option value="superadmin" {{ old('role', $admin->role) === 'superadmin' ? 'selected' : '' }}>Super Admin (Full Access)</option>
                <option value="manager" {{ old('role', $admin->role) === 'manager' ? 'selected' : '' }}>Manager (Manage Tenants)</option>
                <option value="viewer" {{ old('role', $admin->role) === 'viewer' ? 'selected' : '' }}>Viewer (Read-Only)</option>
            </select>
            @error('role')
                <span class="ca-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="ca-form-group">
            <label class="ca-label">Status</label>
            <select name="is_active" class="ca-select" required>
                <option value="1" {{ old('is_active', $admin->is_active) ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !old('is_active', $admin->is_active) ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('is_active')
                <span class="ca-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="ca-form-group">
            <label class="ca-label">New Password (Optional)</label>
            <input type="password" name="password" class="ca-input" placeholder="Leave blank to keep current password">
            <small class="ca-help">Only fill if you want to change the password</small>
            @error('password')
                <span class="ca-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="ca-form-group">
            <label class="ca-label">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="ca-input" placeholder="Confirm new password">
        </div>

        <div class="ca-info-box">
            <strong>Last Login:</strong> {{ $admin->last_login_at ? $admin->last_login_at->format('M j, Y H:i') : 'Never' }}
        </div>

        <div class="ca-form-actions">
            <a href="{{ route('admin.users.index') }}" class="ca-btn ca-btn-ghost">Cancel</a>
            <button type="submit" class="ca-btn ca-btn-primary">Update Admin</button>
        </div>
    </form>
</div>

<style>
.ca-form-card { background: white; border-radius: 8px; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 600px; }
.ca-form-group { margin-bottom: 1.5rem; }
.ca-label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: #1f2937; font-size: 14px; }
.ca-input, .ca-select { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; font-family: inherit; }
.ca-input:focus, .ca-select:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
.ca-error { color: #dc2626; font-size: 12px; display: block; margin-top: 0.25rem; }
.ca-help { color: #6b7280; font-size: 12px; display: block; margin-top: 0.25rem; }
.ca-info-box { background: #f0f9ff; border-left: 3px solid #2563eb; padding: 1rem; border-radius: 4px; font-size: 13px; color: #1e40af; margin-bottom: 1.5rem; }
.ca-form-actions { display: flex; gap: 1rem; margin-top: 2rem; }
.ca-btn { padding: 0.75rem 1.5rem; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
.ca-btn-primary { background: #2563eb; color: white; }
.ca-btn-primary:hover { background: #1d4ed8; }
.ca-btn-ghost { background: #f3f4f6; color: #374151; }
.ca-btn-ghost:hover { background: #e5e7eb; }
</style>
@endsection
