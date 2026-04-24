@extends('layouts.central-admin')

@section('title', 'Add Tenant')
@section('topbar-title', 'Add Tenant')

@section('content')
<div class="ca-page-head" style="margin-bottom:24px;">
    <h1 class="ca-page-title">Provision New Tenant</h1>
    <p class="ca-page-desc">Create a new church workspace, run migrations, and set up the church admin account.</p>
</div>

<div class="ca-card" style="max-width:640px;">
    <div class="ca-card-head" style="padding:14px 20px;">
        <span class="ca-card-title">Church Details</span>
    </div>
    <div style="padding:20px;">

        @if($errors->any())
            <div style="background:#fdeaea;border:1px solid #e6c3c3;border-radius:8px;padding:12px 16px;margin-bottom:16px;">
                <ul style="margin:0;padding-left:18px;font-size:13px;color:#c0392b;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.tenants.admin-store') }}" method="POST">
            @csrf

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--ca-ink-3);margin-bottom:6px;">Church Name *</label>
                <input type="text" name="church_name" value="{{ old('church_name') }}"
                       placeholder="e.g. First Baptist Church" class="ca-input" style="width:100%;" required>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--ca-ink-3);margin-bottom:6px;">Subdomain *</label>
                <div style="display:flex;align-items:center;gap:0;">
                    <input type="text" name="subdomain" id="subdomain" value="{{ old('subdomain') }}"
                           placeholder="firstbaptist" class="ca-input" style="width:100%;border-radius:6px 0 0 6px;border-right:none;" required>
                    <span style="display:inline-flex;align-items:center;padding:0 12px;height:38px;background:var(--ca-surface-dim);border:1px solid var(--ca-border);border-radius:0 6px 6px 0;font-size:12.5px;color:var(--ca-ink-4);white-space:nowrap;">.{{ config('app.domain', 'localhost') }}</span>
                </div>
                <div style="font-size:11px;color:var(--ca-ink-4);margin-top:4px;">Lowercase letters, numbers, and hyphens only. Min 3 chars.</div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:var(--ca-ink-3);margin-bottom:6px;">Admin Name *</label>
                    <input type="text" name="admin_name" value="{{ old('admin_name') }}"
                           placeholder="Pastor John Doe" class="ca-input" style="width:100%;" required>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:var(--ca-ink-3);margin-bottom:6px;">Admin Email *</label>
                    <input type="email" name="admin_email" value="{{ old('admin_email') }}"
                           placeholder="pastor@church.com" class="ca-input" style="width:100%;" required>
                </div>
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--ca-ink-3);margin-bottom:6px;">Subscription Plan *</label>
                <select name="subscription_plan" class="ca-input" style="width:100%;" required>
                    <option value="free"     {{ old('subscription_plan') === 'free'     ? 'selected' : '' }}>Free</option>
                    <option value="basic"    {{ old('subscription_plan') === 'basic'    ? 'selected' : '' }}>Basic</option>
                    <option value="standard" {{ old('subscription_plan') === 'standard' ? 'selected' : '' }}>Standard</option>
                    <option value="premium"  {{ old('subscription_plan') === 'premium'  ? 'selected' : '' }}>Premium</option>
                </select>
            </div>

            <div style="background:rgba(212,168,83,.08);border:1px solid rgba(212,168,83,.2);border-radius:8px;padding:12px 16px;margin-bottom:24px;font-size:12.5px;color:var(--ca-ink-3);">
                <strong style="color:var(--ca-ink-2);">What happens on submit:</strong>
                <ol style="margin:6px 0 0 16px;padding:0;line-height:2;">
                    <li>Tenant record created in central DB</li>
                    <li>Subdomain registered</li>
                    <li>Tenant database created + migrations run</li>
                    <li>Church admin user created (random password — send reset email manually)</li>
                    <li>Roles &amp; permissions seeded</li>
                </ol>
            </div>

            <div style="display:flex;gap:12px;">
                <button type="submit" class="ca-btn ca-btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Create Tenant
                </button>
                <a href="{{ route('admin.tenants.index') }}" class="ca-btn" style="background:var(--ca-surface-dim);color:var(--ca-ink-3);">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nameInput = document.querySelector('input[name="church_name"]');
        const slugInput = document.getElementById('subdomain');
        if (nameInput && slugInput && !slugInput.value) {
            nameInput.addEventListener('input', () => {
                slugInput.value = nameInput.value
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
            });
        }
    });
</script>
@endpush
@endsection
