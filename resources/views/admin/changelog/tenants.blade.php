@extends('layouts.central-admin')

@section('title', "v{{ $release->version }} — Tenant Acknowledgments")
@section('topbar-title', 'Tenant Acknowledgments')

@section('content')

{{-- Page Header --}}
<div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
    <a href="{{ route('admin.changelog.index') }}"
       class="ca-btn" style="font-size:13px;padding:6px 12px;display:inline-flex;align-items:center;gap:6px;background:transparent;color:var(--ca-ink-4);border:1px solid var(--ca-border);">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        All releases
    </a>
    <div>
        <h1 class="ca-page-title">
            <span style="color:var(--ca-ink-4);font-weight:400;">v{{ $release->version }}</span>
            — Tenant acknowledgment status
        </h1>
        <p class="ca-page-desc">{{ $release->title }}</p>
    </div>
</div>

{{-- Stats Row --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
    <div class="ca-card" style="padding:18px 20px;border-left:3px solid var(--ca-sage);">
        <div style="font-size:11px;font-weight:600;color:var(--ca-ink-4);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Acknowledged</div>
        <div style="font-size:28px;font-weight:700;color:var(--ca-sage);font-family:'DM Mono',monospace;line-height:1;">{{ $acknowledgedCount }}</div>
        <div style="font-size:12px;color:var(--ca-ink-4);margin-top:4px;">tenants have seen this</div>
    </div>
    <div class="ca-card" style="padding:18px 20px;border-left:3px solid var(--ca-amber);">
        <div style="font-size:11px;font-weight:600;color:var(--ca-ink-4);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Not Yet Seen</div>
        <div style="font-size:28px;font-weight:700;color:var(--ca-amber);font-family:'DM Mono',monospace;line-height:1;">{{ $pendingCount }}</div>
        <div style="font-size:12px;color:var(--ca-ink-4);margin-top:4px;">tenants have not dismissed</div>
    </div>
    <div class="ca-card" style="padding:18px 20px;">
        <div style="font-size:11px;font-weight:600;color:var(--ca-ink-4);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Total Tenants</div>
        <div style="font-size:28px;font-weight:700;color:var(--ca-ink-2);font-family:'DM Mono',monospace;line-height:1;">{{ $tenantsWithStatus->count() }}</div>
        <div style="font-size:12px;color:var(--ca-ink-4);margin-top:4px;">
            @php $pct = $tenantsWithStatus->count() > 0 ? round(($acknowledgedCount / $tenantsWithStatus->count()) * 100) : 0; @endphp
            {{ $pct }}% acknowledgment rate
        </div>
    </div>
</div>

{{-- Filter + Table --}}
<div class="ca-card"
     x-data="{ filter: 'all' }">

    {{-- Filter Tabs --}}
    <div class="ca-card-head" style="padding:14px 20px;display:flex;align-items:center;justify-content:space-between;">
        <span class="ca-card-title">Tenant breakdown</span>
        <div style="display:flex;gap:6px;">
            @foreach(['all' => 'All', 'seen' => 'Seen', 'unseen' => 'Not yet seen'] as $key => $label)
            <button @click="filter = '{{ $key }}'"
                    :class="filter === '{{ $key }}' ? 'ca-btn-approve' : ''"
                    class="ca-btn"
                    style="font-size:12px;padding:4px 12px;"
                    :style="filter !== '{{ $key }}' ? 'background:var(--ca-surface-dim);color:var(--ca-ink-3);' : ''">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    @if($tenantsWithStatus->isEmpty())
        <div style="text-align:center;padding:48px 24px;color:var(--ca-ink-4);font-size:13.5px;">
            No tenants found.
        </div>
    @else
        <div class="ca-table-wrap">
            <table class="ca-table">
                <thead>
                    <tr>
                        <th>Church</th>
                        <th>Tenant ID</th>
                        <th>Domain</th>
                        <th>Acknowledged At</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenantsWithStatus as $row)
                    @php $t = $row['tenant']; @endphp
                    <tr
                        x-show="filter === 'all' || (filter === 'seen' && {{ $row['acknowledged'] ? 'true' : 'false' }}) || (filter === 'unseen' && {{ !$row['acknowledged'] ? 'true' : 'false' }})"
                        x-transition
                    >
                        <td>
                            <div style="font-size:13.5px;font-weight:500;color:var(--ca-ink-2);">{{ $t->name ?? '—' }}</div>
                            <div style="font-size:12px;color:var(--ca-ink-4);">{{ $t->email ?? '' }}</div>
                        </td>
                        <td>
                            <span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--ca-ink-3);">{{ $t->id }}</span>
                        </td>
                        <td>
                            <span style="font-size:13px;color:var(--ca-ink-3);">
                                {{ $t->domains->first()?->domain ?? ($t->subdomain ?? '—') }}
                            </span>
                        </td>
                        <td class="ca-table-muted">
                            {{ $row['acknowledged_at'] ? $row['acknowledged_at']->format('M d, Y g:i A') : '—' }}
                        </td>
                        <td>
                            @if($row['acknowledged'])
                                <span class="ca-pill ca-pill-ok" style="display:inline-flex;align-items:center;gap:6px;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    Seen on {{ $row['acknowledged_at']?->format('M d') }}
                                </span>
                            @else
                                <span class="ca-pill ca-pill-pending" style="display:inline-flex;align-items:center;gap:6px;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    Not yet seen
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
