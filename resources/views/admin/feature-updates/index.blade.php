@extends('layouts.central-admin')

@section('title', 'Feature Updates')
@section('topbar-title', 'Feature Updates')

@section('content')
<div class="ca-page-head" style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
    <div>
        <h1 class="ca-page-title">Feature Deployment Pipeline</h1>
        <p class="ca-page-desc">Scaffold new features, publish them, and push updates to all tenants.</p>
    </div>
    <a href="{{ route('admin.feature-updates.create') }}" class="ca-btn ca-btn-primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Feature
    </a>
</div>

{{-- Stats Row --}}
@php
    $totalFeatures   = $featureUpdates->count();
    $publishedCount  = $featureUpdates->where('status', 'published')->count();
    $draftCount      = $featureUpdates->where('status', 'draft')->count();
    $failedCount     = $featureUpdates->where('status', 'scaffold_failed')->count();
@endphp
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    @foreach([
        ['label' => 'Total Features',  'value' => $totalFeatures,   'color' => 'var(--ca-ink-2)'],
        ['label' => 'Published',       'value' => $publishedCount,  'color' => 'var(--ca-sage)'],
        ['label' => 'Drafts',          'value' => $draftCount,      'color' => 'var(--ca-amber)'],
        ['label' => 'Failed',          'value' => $failedCount,     'color' => 'var(--ca-red, #e74c3c)'],
    ] as $stat)
    <div class="ca-card" style="padding:18px 20px;">
        <div style="font-size:11px;font-weight:600;color:var(--ca-ink-4);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;">{{ $stat['label'] }}</div>
        <div style="font-size:28px;font-weight:700;color:{{ $stat['color'] }};font-family:'DM Mono',monospace;line-height:1;">{{ $stat['value'] }}</div>
    </div>
    @endforeach
</div>

{{-- Features Table --}}
<div class="ca-card">
    <div class="ca-card-head" style="padding:14px 20px;">
        <span class="ca-card-title">All Feature Updates</span>
    </div>

    @if($featureUpdates->isEmpty())
        <div style="text-align:center;padding:56px 24px;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--ca-ink-4)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:12px;">
                <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0022 16z"/>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                <line x1="12" y1="22.08" x2="12" y2="12"/>
            </svg>
            <div style="font-size:14px;font-weight:500;color:var(--ca-ink-3);">No features yet</div>
            <div style="font-size:12.5px;color:var(--ca-ink-4);margin-top:4px;">Create your first feature using the "New Feature" button above.</div>
        </div>
    @else
        <div class="ca-table-wrap">
            <table class="ca-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Version</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Published</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($featureUpdates as $feature)
                    <tr>
                        <td>
                            <a href="{{ route('admin.feature-updates.show', $feature) }}" style="color:var(--ca-ink-2);font-weight:500;text-decoration:none;">
                                {{ $feature->name }}
                            </a>
                        </td>
                        <td><code style="font-size:12px;color:var(--ca-ink-3);">{{ $feature->slug }}</code></td>
                        <td><span class="ca-pill" style="background:var(--ca-surface-dim);color:var(--ca-ink-3);">v{{ $feature->version }}</span></td>
                        <td style="text-transform:capitalize;">{{ $feature->type }}</td>
                        <td>
                            @if($feature->status === 'published')
                                <span class="ca-pill ca-pill-ok"><span class="ca-pill-dot"></span> Published</span>
                            @elseif($feature->status === 'draft')
                                <span class="ca-pill ca-pill-pending"><span class="ca-pill-dot"></span> Draft</span>
                            @elseif($feature->status === 'scaffold_failed')
                                <span class="ca-pill" style="background:#fdeaea;color:#c0392b;"><span class="ca-pill-dot" style="background:#c0392b;"></span> Failed</span>
                            @else
                                <span class="ca-pill">{{ ucfirst($feature->status) }}</span>
                            @endif
                        </td>
                        <td class="ca-table-muted">{{ $feature->published_at?->format('M d, Y') ?? '—' }}</td>
                        <td>
                            <a href="{{ route('admin.feature-updates.show', $feature) }}" class="ca-btn" style="font-size:12px;padding:5px 12px;background:var(--ca-surface-dim);color:var(--ca-ink-3);">
                                View
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
