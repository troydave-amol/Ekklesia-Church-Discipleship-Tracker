@extends('layouts.app')

@section('title', 'Feature Updates')

@section('content')

{{-- ── Page Heading ──────────────────────────────────────────────── --}}
<div class="page-header" style="margin-bottom:24px;">
    <h1 class="page-title">Feature Updates</h1>
    <p class="page-subtitle">Install new features and modules published by the Ekklesia platform.</p>
</div>

{{-- ── Flash Messages ────────────────────────────────────────────── --}}
@if(session('success'))
    <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:8px;background:rgba(52,211,153,.08);border:1px solid rgba(52,211,153,.2);margin-bottom:20px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        <span style="font-size:13.5px;color:var(--green);font-weight:500;">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:8px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);margin-bottom:20px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <span style="font-size:13.5px;color:#ef4444;font-weight:500;">{{ session('error') }}</span>
    </div>
@endif

@if(session('info'))
    <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:8px;background:rgba(88,166,255,.08);border:1px solid rgba(88,166,255,.2);margin-bottom:20px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--blue)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        <span style="font-size:13.5px;color:var(--blue);font-weight:500;">{{ session('info') }}</span>
    </div>
@endif

{{-- ── Empty State ───────────────────────────────────────────────── --}}
@if($featureUpdates->isEmpty())
    <div class="card" style="text-align:center;padding:56px 24px;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:12px;">
            <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0022 16z"/>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
            <line x1="12" y1="22.08" x2="12" y2="12"/>
        </svg>
        <div style="font-size:15px;font-weight:500;color:var(--text-muted);">No feature updates available yet.</div>
        <div style="font-size:13px;color:var(--text-dim);margin-top:4px;">Check back soon — the platform team publishes new modules regularly.</div>
    </div>
@else

{{-- ── Feature Update Cards ──────────────────────────────────────── --}}
<div style="display:grid;gap:16px;">
    @foreach($featureUpdates as $feature)
    @php
        $install = $installations[$feature->id] ?? null;
        $status  = $install?->status ?? 'pending';
    @endphp

    <div class="card" style="border-left-width:3px;border-left-style:solid;border-left-color:{{ match($status) {
        'installed'  => 'var(--green)',
        'installing' => 'var(--blue)',
        'failed'     => '#ef4444',
        default      => 'var(--gold)',
    } }};">

        {{-- Header --}}
        <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;border-bottom:1px solid var(--border);">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                {{-- Package Icon --}}
                <div style="width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:{{ match($status) {
                    'installed'  => 'rgba(52,211,153,.1)',
                    'installing' => 'rgba(88,166,255,.1)',
                    'failed'     => 'rgba(239,68,68,.1)',
                    default      => 'rgba(212,168,83,.1)',
                } }};">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ match($status) {
                        'installed'  => 'var(--green)',
                        'installing' => 'var(--blue)',
                        'failed'     => '#ef4444',
                        default      => 'var(--gold)',
                    } }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0022 16z"/>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                        <line x1="12" y1="22.08" x2="12" y2="12"/>
                    </svg>
                </div>

                <div>
                    <div style="font-size:14.5px;font-weight:600;color:var(--text);line-height:1.3;">{{ $feature->name }}</div>
                    <div style="font-size:12px;color:var(--text-dim);margin-top:2px;">
                        v{{ $feature->version }} · {{ ucfirst($feature->type) }}
                        · Published {{ $feature->published_at->format('M d, Y') }}
                    </div>
                </div>
            </div>

            {{-- Status Badge --}}
            <div>
                @if($status === 'installed')
                    <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;background:rgba(52,211,153,.1);color:var(--green);border:1px solid rgba(52,211,153,.2);">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Installed
                    </span>
                @elseif($status === 'installing')
                    <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;background:rgba(88,166,255,.1);color:var(--blue);border:1px solid rgba(88,166,255,.2);">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 1s linear infinite;"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>
                        Installing…
                    </span>
                @elseif($status === 'failed')
                    <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;background:rgba(239,68,68,.1);color:#ef4444;border:1px solid rgba(239,68,68,.2);">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        Failed
                    </span>
                @else
                    <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;background:rgba(212,168,83,.1);color:var(--gold);border:1px solid rgba(212,168,83,.2);">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Pending
                    </span>
                @endif
            </div>
        </div>

        {{-- Body --}}
        <div style="padding:16px 20px;">
            @if($feature->description)
                <p style="font-size:13.5px;color:var(--text-muted);line-height:1.7;margin-bottom:16px;">{{ $feature->description }}</p>
            @endif

            {{-- Error Log (for failed installs) --}}
            @if($status === 'failed' && $install?->error_log)
                <div style="background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.15);border-radius:8px;padding:10px 14px;margin-bottom:16px;">
                    <div style="font-size:11px;font-weight:700;color:#ef4444;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Error Details</div>
                    <div style="font-size:12.5px;color:#ef4444;font-family:'DM Mono',monospace;word-break:break-word;">{{ Str::limit($install->error_log, 300) }}</div>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div style="display:flex;align-items:center;gap:10px;">
                @if($status === 'pending')
                    <form action="{{ route('system.updates.install', $feature) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="font-size:13px;padding:8px 20px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Install Update
                        </button>
                    </form>
                @elseif($status === 'installing')
                    <button class="btn" style="font-size:13px;padding:8px 20px;opacity:0.6;cursor:not-allowed;" disabled>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;animation:spin 1s linear infinite;"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>
                        Installing…
                    </button>
                    <span style="font-size:12px;color:var(--text-dim);">Refresh the page in a few seconds to check status.</span>
                @elseif($status === 'installed')
                    <div style="display:flex;align-items:center;gap:6px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        <span style="font-size:13px;font-weight:500;color:var(--green);">
                            Installed on {{ $install->installed_at?->format('M d, Y \a\t h:i A') }}
                        </span>
                    </div>
                @elseif($status === 'failed')
                    <form action="{{ route('system.updates.install', $feature) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn" style="font-size:13px;padding:8px 20px;background:rgba(239,68,68,.1);color:#ef4444;border:1px solid rgba(239,68,68,.2);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>
                            Retry Install
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endsection
