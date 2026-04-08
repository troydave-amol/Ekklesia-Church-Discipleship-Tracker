@extends('layouts.central-admin')

@section('title', 'Monitoring')
@section('topbar-title', 'Monitoring')

@section('content')
<div class="ca-page-head">
    <h1 class="ca-page-title">Central Monitoring</h1>
    <p class="ca-page-desc">Registration queue, live church workspaces, and plan distribution.</p>
</div>

{{-- ═══ STAT CARDS ═══ --}}
<div class="ca-stats">

    <div class="ca-stat" data-accent="amber">
        <div class="ca-stat-top">
            <span class="ca-stat-label">Pending Approvals</span>
            <div class="ca-stat-icon amber">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
        </div>
        <div class="ca-stat-value">{{ $pendingSignups }}</div>
        <div class="ca-stat-sub">Awaiting review &amp; provisioning</div>
    </div>

    <div class="ca-stat" data-accent="sage">
        <div class="ca-stat-top">
            <span class="ca-stat-label">Active Tenants</span>
            <div class="ca-stat-icon sage">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
        </div>
        <div class="ca-stat-value">{{ $activeTenants }}</div>
        <div class="ca-stat-sub">Live church workspaces</div>
    </div>

    <div class="ca-stat" data-accent="gold">
        <div class="ca-stat-top">
            <span class="ca-stat-label">Total Tenants</span>
            <div class="ca-stat-icon gold">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
        </div>
        <div class="ca-stat-value">{{ $totalTenants }}</div>
        <div class="ca-stat-sub">All registered churches</div>
    </div>

</div>

{{-- ═══ BOTTOM ROW ═══ --}}
<div class="ca-grid-2">

    {{-- Plan Distribution --}}
    <div class="ca-card">
        <div class="ca-card-head">
            <span class="ca-card-title">Plan Distribution</span>
        </div>
        <div class="ca-card-body">
            @forelse($planRows as $row)
                <div class="ca-plan-row">
                    <span class="ca-plan-name">
                        <span class="ca-plan-dot {{ strtolower($row['label']) }}"></span>
                        {{ $row['label'] }}
                    </span>
                    <span class="ca-plan-count">{{ $row['count'] }}</span>
                </div>
            @empty
                <p style="color:var(--ca-ink-4);font-size:13px;">No plan data yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="ca-card">
        <div class="ca-card-head">
            <span class="ca-card-title">Quick Actions</span>
        </div>
        <div class="ca-card-body">
            <a href="{{ route('admin.signups.index') }}" class="ca-cta ca-cta-gold">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4-4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <line x1="19" y1="8" x2="19" y2="14"/>
                    <line x1="22" y1="11" x2="16" y2="11"/>
                </svg>
                Review Pending Registrations
            </a>
            <a href="{{ route('admin.tenants.index') }}" class="ca-cta ca-cta-ghost">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                View All Tenants
            </a>
        </div>
    </div>

</div>
@endsection
