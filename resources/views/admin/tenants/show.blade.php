@extends('layouts.central-admin')

@section('title', $tenant->name)
@section('topbar-title', 'Tenant Detail')

@section('content')

<a href="{{ route('admin.tenants.index') }}" class="ca-back">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="19" y1="12" x2="5" y2="12"/>
        <polyline points="12 19 5 12 12 5"/>
    </svg>
    All tenants
</a>

<div class="ca-page-head">
    <h1 class="ca-page-title">{{ $tenant->name }}</h1>
    <p class="ca-page-desc">Workspace configuration and domain details for this tenant.</p>
</div>

{{-- ═══ DETAIL GRID ═══ --}}
<div class="ca-detail-grid">
    <div class="ca-detail-item">
        <div class="ca-detail-label">Email</div>
        <div class="ca-detail-value">{{ $tenant->email }}</div>
    </div>
    <div class="ca-detail-item">
        <div class="ca-detail-label">Subdomain</div>
        <div class="ca-detail-value"><code class="ca-code">{{ $tenant->subdomain }}</code></div>
    </div>
    <div class="ca-detail-item">
        <div class="ca-detail-label">Database</div>
        <div class="ca-detail-value"><code class="ca-code">{{ $tenant->db_name }}</code></div>
    </div>
    <div class="ca-detail-item">
        <div class="ca-detail-label">Subscription Plan</div>
        <div class="ca-detail-value">{{ ucfirst($tenant->subscription_plan) }}</div>
    </div>
    <div class="ca-detail-item">
        <div class="ca-detail-label">Status</div>
        <div class="ca-detail-value">
            @if($tenant->status === 'active')
                <span class="ca-pill ca-pill-active"><span class="ca-pill-dot"></span> Active</span>
            @else
                <span class="ca-pill ca-pill-inactive"><span class="ca-pill-dot"></span> {{ ucfirst($tenant->status) }}</span>
            @endif
        </div>
    </div>
    <div class="ca-detail-item">
        <div class="ca-detail-label">Created</div>
        <div class="ca-detail-value">{{ $tenant->created_at->format('M j, Y') }}</div>
    </div>
</div>

{{-- ═══ DOMAINS ═══ --}}
<div class="ca-card">
    <div class="ca-card-head">
        <span class="ca-card-title">Domains</span>
    </div>
    <div class="ca-card-body">
        @forelse($tenant->domains as $d)
            <div class="ca-plan-row">
                <span class="ca-plan-name">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="opacity:.5;">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/>
                    </svg>
                    <code class="ca-code">{{ $d->domain }}</code>
                </span>
            </div>
        @empty
            <p style="color:var(--ca-ink-4);font-size:13px;">No domains configured.</p>
        @endforelse
    </div>
</div>
@endsection
