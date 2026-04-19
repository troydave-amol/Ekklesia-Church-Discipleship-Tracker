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

<div class="ca-page-head" style="display:flex;align-items:flex-start;justify-content:space-between;">
    <div>
        <h1 class="ca-page-title">{{ $tenant->name }}</h1>
        <p class="ca-page-desc">Workspace configuration and domain details for this tenant.</p>
    </div>
    <div class="ca-actions">
        <a href="{{ route('admin.tenants.edit', $tenant) }}" class="ca-btn ca-cta-ghost" style="gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Edit Tenant
        </a>
        <form method="POST" action="{{ route('admin.tenants.toggle-status', $tenant) }}" class="ca-inline">
            @csrf
            @method('PATCH')
            @if($tenant->status === 'active')
                <button type="submit" class="ca-btn ca-btn-reject" onclick="return confirm('Are you sure you want to suspend this tenant? All users will be locked out immediately.')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                    Disable
                </button>
            @else
                <button type="submit" class="ca-btn ca-btn-approve">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Enable
                </button>
            @endif
        </form>
    </div>
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
