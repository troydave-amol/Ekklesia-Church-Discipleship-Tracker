@extends('layouts.central-admin')

@section('title', 'RBAC Management')
@section('topbar-title', 'RBAC per Tenant')

@section('content')
<div class="ca-page-head" style="margin-bottom:24px;">
    <h1 class="ca-page-title">Feature Updates — RBAC per Tenant</h1>
    <p class="ca-page-desc">Manage roles and permissions for each church tenant.</p>
</div>

<div class="ca-card">
    @if($tenants->isEmpty())
        <div style="text-align:center;padding:56px 24px;">
            <div style="font-size:14px;font-weight:500;color:var(--ca-ink-3);">No tenants provisioned yet.</div>
        </div>
    @else
        <div class="ca-table-wrap">
            <table class="ca-table">
                <thead>
                    <tr>
                        <th>Church Name</th>
                        <th>Subdomain</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenants as $tenant)
                    <tr>
                        <td style="font-weight:500;color:var(--ca-ink-2);">{{ $tenant->name }}</td>
                        <td>
                            @php
                                $domain = config('app.domain', 'localhost');
                                $scheme = request()->getScheme();
                                $port = request()->getPort();
                                $tenantUrl = in_array($domain, ['localhost', '127.0.0.1']) 
                                    ? "{$scheme}://{$tenant->subdomain}.{$domain}:{$port}" 
                                    : "https://{$tenant->subdomain}.{$domain}";
                            @endphp
                            <a href="{{ $tenantUrl }}" target="_blank" class="ca-subdomain-link" title="Open {{ $tenant->subdomain }}.{{ $domain }}">
                                <code class="ca-code">{{ $tenant->subdomain }}</code>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.5;">
                                    <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                                </svg>
                            </a>
                        </td>
                        <td>{{ ucfirst($tenant->subscription_plan ?? 'free') }}</td>
                        <td>
                            @if($tenant->status === 'active')
                                <span class="ca-pill ca-pill-active"><span class="ca-pill-dot"></span> Active</span>
                            @else
                                <span class="ca-pill ca-pill-inactive"><span class="ca-pill-dot"></span> {{ ucfirst($tenant->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.rbac.show', $tenant) }}" class="ca-btn ca-btn-link">
                                Manage RBAC →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
