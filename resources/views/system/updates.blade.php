@extends('layouts.app')

@section('title', 'Available Updates')

@section('content')

<div class="page-header" style="margin-bottom:24px;">
    <h1 class="page-title">Available Updates</h1>
    <p class="page-subtitle">New features and improvements for your church</p>
</div>

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
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--blue)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12.01" y2="16"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
        <span style="font-size:13.5px;color:var(--blue);font-weight:500;">{{ session('info') }}</span>
    </div>
@endif

@if($featureCards->isEmpty())
    <div class="card" style="text-align:center;padding:64px 24px;">
        <div style="font-size:40px;margin-bottom:16px;">All set</div>
        <div style="font-size:16px;font-weight:600;color:var(--text);margin-bottom:6px;">You're all up to date!</div>
        <div style="font-size:13.5px;color:var(--text-muted);">Check back soon. New feature packages will appear here when they are published.</div>
    </div>
@else

@php
    $hasInstalling = $featureCards->contains(fn ($card) => $card['status'] === 'installing');
@endphp

<div style="display:grid;gap:16px;" x-data="{ installing: {{ $hasInstalling ? 'true' : 'false' }} }"
     x-init="if (installing) { setInterval(() => window.location.reload(), 30000) }">

    @foreach($featureCards as $card)
    @php
        $feature = $card['feature'];
        $install = $card['installation'];
        $status = $card['status'];
        $isComplete = $card['is_complete'];
        $includedComponents = $card['included_components'];
        $missingLabels = $card['missing_labels'];
        $borderColor = !$isComplete
            ? '#ef4444'
            : match($status) {
                'installed' => 'var(--green)',
                'installing' => '#f59e0b',
                'failed' => '#d4a853',
                default => 'var(--gold)',
            };
        $iconBackground = match($status) {
            'installed' => 'rgba(52,211,153,.1)',
            'installing' => 'rgba(245,158,11,.1)',
            'failed' => 'rgba(212,168,83,.12)',
            default => 'rgba(212,168,83,.1)',
        };
        $iconStroke = match($status) {
            'installed' => 'var(--green)',
            'installing' => '#f59e0b',
            'failed' => 'var(--gold)',
            default => 'var(--gold)',
        };
    @endphp

    <div class="card" style="border-left:3px solid {{ $borderColor }};" x-data="{ submitting: {{ $status === 'installing' ? 'true' : 'false' }} }">

        <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;border-bottom:1px solid var(--border);">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <div style="width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:{{ $iconBackground }};">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $iconStroke }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0022 16z"/>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                        <line x1="12" y1="22.08" x2="12" y2="12"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:14.5px;font-weight:600;color:var(--text);line-height:1.3;">
                        {{ $feature->name }}
                        <span style="display:inline-flex;align-items:center;margin-left:6px;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;background:var(--surface-alt);color:var(--text-muted);">v{{ $feature->version }}</span>
                        <span style="display:inline-flex;align-items:center;margin-left:4px;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;background:{{ match($feature->type) {'feature' => 'rgba(88,166,255,.12)', 'patch' => 'rgba(52,211,153,.12)', default => 'rgba(212,168,83,.12)'} }};color:{{ match($feature->type) {'feature' => 'var(--blue)', 'patch' => 'var(--green)', default => 'var(--gold)'} }};">{{ ucfirst($feature->type) }}</span>
                    </div>
                    <div style="font-size:12px;color:var(--text-dim);margin-top:2px;">
                        Published {{ $feature->published_at->format('M d, Y') }}
                    </div>
                </div>
            </div>

            @if($status === 'installed')
                <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;background:rgba(52,211,153,.1);color:var(--green);border:1px solid rgba(52,211,153,.2);">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Installed
                </span>
            @elseif($status === 'installing')
                <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;background:rgba(245,158,11,.1);color:#f59e0b;border:1px solid rgba(245,158,11,.2);">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 1s linear infinite;"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>
                    Installing...
                </span>
            @elseif($status === 'failed')
                <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;background:rgba(212,168,83,.12);color:var(--gold);border:1px solid rgba(212,168,83,.24);">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Install needs attention
                </span>
            @else
                <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;background:rgba(88,166,255,.1);color:var(--blue);border:1px solid rgba(88,166,255,.2);">
                    Available
                </span>
            @endif
        </div>

        <div style="padding:16px 20px;">
            @if($feature->description)
                <p style="font-size:13.5px;color:var(--text-muted);line-height:1.7;margin-bottom:16px;">{{ $feature->description }}</p>
            @endif

            @if(!$isComplete)
                <div style="display:flex;gap:10px;align-items:flex-start;padding:12px 14px;border-radius:10px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);margin-bottom:16px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div style="font-size:12.5px;color:#ef4444;line-height:1.5;">
                        This package is incomplete. Missing or incomplete generated files:
                        <strong>{{ implode(', ', $missingLabels) }}</strong>
                    </div>
                </div>
            @endif

            @if(count($includedComponents) > 0)
                <div style="margin-bottom:16px;">
                    <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;">Included in this update</div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;">
                        @foreach($includedComponents as $component)
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11.5px;background:var(--surface-alt);color:var(--text-muted);border:1px solid var(--border);">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                {{ $component['label'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($status === 'failed' && $install?->error_log)
                <div style="background:rgba(212,168,83,.08);border:1px solid rgba(212,168,83,.18);border-radius:8px;padding:10px 14px;margin-bottom:16px;">
                    <div style="font-size:11px;font-weight:700;color:var(--gold);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Install Notes</div>
                    <div style="font-size:12.5px;color:var(--text);font-family:'DM Mono',monospace;word-break:break-word;">{{ Str::limit($install->error_log, 300) }}</div>
                </div>
            @endif

            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                @if(!$isComplete)
                    <span style="font-size:12px;color:#ef4444;font-weight:500;">Install unavailable until the package files are completed in the central app.</span>

                @elseif($status === 'pending')
                    <form action="{{ route('system.updates.install', $feature) }}" method="POST" @submit.prevent="
                        submitting = true;
                        $el.querySelector('button').disabled = true;
                        fetch($el.action, {
                            method: 'POST',
                            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json'},
                        }).then(() => {
                            window.location.reload();
                        }).catch(() => { window.location.reload(); });
                    ">
                        @csrf
                        <button type="submit" class="btn btn-primary" :disabled="submitting" style="font-size:13px;padding:8px 20px;">
                            <svg x-show="!submitting" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            <svg x-show="submitting" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;animation:spin 1s linear infinite;"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>
                            <span x-text="submitting ? 'Starting...' : 'Install'"></span>
                        </button>
                    </form>
                    <span style="font-size:12px;color:var(--text-dim);">Installation runs in the background. This page will update.</span>

                @elseif($status === 'installing')
                    <button class="btn" style="font-size:13px;padding:8px 20px;opacity:0.6;cursor:not-allowed;" disabled>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;animation:spin 1s linear infinite;"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>
                        Installing...
                    </button>
                    <span style="font-size:12px;color:var(--text-dim);">Refresh in a few seconds to check status.</span>

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
                        <button type="submit" class="btn" style="font-size:13px;padding:8px 20px;background:rgba(212,168,83,.12);color:var(--gold);border:1px solid rgba(212,168,83,.24);">
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
