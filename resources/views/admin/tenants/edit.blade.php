@extends('layouts.central-admin')

@section('title', 'Edit Tenant')
@section('topbar-title', 'Edit Tenant')

@section('content')

<a href="{{ route('admin.tenants.show', $tenant) }}" class="ca-back">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="19" y1="12" x2="5" y2="12"/>
        <polyline points="12 19 5 12 12 5"/>
    </svg>
    Back to details
</a>

<div class="ca-page-head">
    <h1 class="ca-page-title">Edit Tenant</h1>
    <p class="ca-page-desc">Update workspace configuration for <strong>{{ $tenant->name }}</strong>.</p>
</div>

<div class="ca-card" style="max-width:640px;">
    <div class="ca-card-head">
        <span class="ca-card-title">Tenant Details</span>
    </div>
    <div class="ca-card-body">
        <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}">
            @csrf
            @method('PUT')

            {{-- Validation errors --}}
            @if($errors->any())
                <div class="ca-flash ca-flash-err" style="margin-bottom:20px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="ca-form-group">
                <label class="ca-label">Church Name</label>
                <input type="text" name="name" class="ca-input" value="{{ old('name', $tenant->name) }}" required>
            </div>

            <div class="ca-form-group">
                <label class="ca-label">Email</label>
                <input type="email" name="email" class="ca-input" value="{{ old('email', $tenant->email) }}" required>
            </div>

            <div class="ca-form-row">
                <div class="ca-form-group" style="flex:1;">
                    <label class="ca-label">Subscription Plan</label>
                    <select name="subscription_plan" class="ca-input">
                        @foreach(['free','basic','standard','premium'] as $plan)
                            <option value="{{ $plan }}" {{ old('subscription_plan', $tenant->subscription_plan) === $plan ? 'selected' : '' }}>
                                {{ ucfirst($plan) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="ca-form-group" style="flex:1;">
                    <label class="ca-label">Status</label>
                    <select name="status" class="ca-input">
                        @foreach(['active','inactive','suspended'] as $status)
                            <option value="{{ $status }}" {{ old('status', $tenant->status) === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Read-only info --}}
            <div class="ca-form-divider"></div>

            <div class="ca-form-row">
                <div class="ca-form-group" style="flex:1;">
                    <label class="ca-label">Subdomain</label>
                    <div class="ca-readonly"><code class="ca-code">{{ $tenant->subdomain }}</code></div>
                </div>
                <div class="ca-form-group" style="flex:1;">
                    <label class="ca-label">Database</label>
                    <div class="ca-readonly"><code class="ca-code">{{ $tenant->db_name }}</code></div>
                </div>
            </div>

            <div class="ca-form-actions">
                <a href="{{ route('admin.tenants.show', $tenant) }}" class="ca-cta ca-cta-ghost" style="flex:1;">Cancel</a>
                <button type="submit" class="ca-cta ca-cta-gold" style="flex:2;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@endsection
