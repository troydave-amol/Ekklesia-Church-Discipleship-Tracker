@extends('layouts.app')

@section('title', "What's New")

@section('content')

{{-- ── Banner: latest unacknowledged release ────────────────────── --}}
@if(isset($latestUnacknowledged) && $latestUnacknowledged)
<div
    id="release-banner-{{ $latestUnacknowledged->id }}"
    x-data="{ show: true }"
    x-show="show"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-4"
    style="display:flex;align-items:center;justify-content:space-between;gap:16px;background:linear-gradient(90deg,#1e1b4b,#312e81);border:1px solid rgba(129,140,248,.35);border-radius:10px;padding:14px 20px;margin-bottom:20px;"
>
    <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0;">
        <span style="font-size:20px;">🎉</span>
        <div>
            <div style="font-size:13.5px;font-weight:600;color:#e0e7ff;">
                v{{ $latestUnacknowledged->version }} is available
                @if($latestUnacknowledged->is_major)
                    <span style="font-size:10px;font-weight:700;background:rgba(251,191,36,.2);color:#fbbf24;padding:2px 7px;border-radius:4px;letter-spacing:.04em;margin-left:6px;">MAJOR</span>
                @endif
            </div>
            <div style="font-size:12.5px;color:rgba(165,180,252,.8);margin-top:2px;">{{ $latestUnacknowledged->title }}</div>
        </div>
        <a href="{{ route('system.changelog') }}"
           style="flex-shrink:0;font-size:12.5px;font-weight:600;color:#818cf8;text-decoration:underline;text-underline-offset:2px;">
            See what's new →
        </a>
    </div>

    <button
        @click="
            fetch('{{ route('system.changelog.acknowledge', $latestUnacknowledged->id) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(() => { show = false; });
        "
        style="flex-shrink:0;background:rgba(129,140,248,.15);border:1px solid rgba(129,140,248,.3);border-radius:6px;color:rgba(165,180,252,.8);font-size:12px;font-weight:500;padding:5px 12px;cursor:pointer;font-family:inherit;transition:background .15s;"
        onmouseover="this.style.background='rgba(129,140,248,.25)'"
        onmouseout="this.style.background='rgba(129,140,248,.15)'"
    >
        Dismiss ✕
    </button>
</div>
@endif

{{-- ── Page Heading ──────────────────────────────────────────────── --}}
<div class="page-header" style="margin-bottom:24px;">
    <h1 class="page-title">What's New</h1>
    <p class="page-subtitle">Stay up to date with the latest improvements to Discipleship Tracker.</p>
</div>

{{-- ── Empty State ───────────────────────────────────────────────── --}}
@if($releases->isEmpty())
    <div class="card" style="text-align:center;padding:56px 24px;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:12px;">
            <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>
            <line x1="7" y1="7" x2="7.01" y2="7"/>
        </svg>
        <div style="font-size:15px;font-weight:500;color:var(--text-muted);">No releases published yet.</div>
        <div style="font-size:13px;color:var(--text-dim);margin-top:4px;">Check back soon for platform updates!</div>
    </div>
@else

{{-- ── Timeline ──────────────────────────────────────────────────── --}}
<div style="position:relative;padding:8px 0;">

    {{-- Vertical line --}}
    <div style="position:absolute;left:19px;top:0;bottom:0;width:2px;background:var(--border);"></div>

    @foreach($releases as $release)
    @php
        $isAcknowledged = in_array($release->id, $acknowledgedIds);
        $borderColor = $isAcknowledged ? 'var(--border)' : 'var(--blue)';
        $nodeColor   = $release->is_major ? 'var(--gold)' : ($isAcknowledged ? 'var(--text-dim)' : 'var(--blue)');
    @endphp

    <div
        id="release-card-{{ $release->id }}"
        style="margin-bottom:28px;position:relative;{{ $isAcknowledged ? 'opacity:0.82;' : '' }}"
        x-data="{ acknowledged: {{ $isAcknowledged ? 'true' : 'false' }}, ackDate: '{{ $isAcknowledged ? ($acknowledgedIds ? \App\Models\TenantReleaseAcknowledgment::on('central')->where('version_history_id', $release->id)->where('tenant_id', tenancy()->tenant->getTenantKey())->value('acknowledged_at') : null) : null }}' }"
    >
        {{-- Timeline node --}}
        <div style="position:absolute;left:-4px;top:8px;width:40px;height:40px;display:flex;align-items:center;justify-content:center;z-index:1;">
            <div style="width:24px;height:24px;border-radius:50%;border:3px solid var(--bg);display:flex;align-items:center;justify-content:center;background:{{ $nodeColor }};">
                <svg width="10" height="10" fill="white" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            </div>
        </div>

        {{-- Card --}}
        <div style="margin-left:54px;">
            <div class="card" style="border-left-width:3px;border-left-style:solid;border-left-color:{{ $borderColor }};transition:border-color .3s;">

                {{-- Card Header --}}
                <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                        {{-- Version pill --}}
                        <span style="display:inline-block;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700;font-family:'DM Mono',monospace;
                              {{ $release->is_major ? 'background:rgba(212,168,83,.1);color:var(--gold-light);border:1px solid rgba(212,168,83,.3);' : 'background:rgba(88,166,255,.08);color:var(--blue);border:1px solid rgba(88,166,255,.2);' }}">
                            v{{ $release->version }}
                        </span>

                        <span style="font-size:14px;font-weight:600;color:var(--text);">{{ $release->title }}</span>

                        @if($release->published_at)
                            <span style="font-size:12px;color:var(--text-dim);">{{ $release->published_at->format('M d, Y') }}</span>
                        @endif

                        @if($release->is_major)
                            <span style="font-size:10px;font-weight:700;background:rgba(212,168,83,.15);color:var(--gold);padding:3px 8px;border-radius:4px;letter-spacing:.05em;">MAJOR UPDATE</span>
                        @endif
                    </div>

                    {{-- Acknowledge/Dismiss button --}}
                    <div x-show="!acknowledged">
                        <button
                            @click="
                                fetch('{{ route('system.changelog.acknowledge', $release->id) }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                }).then(r => r.json()).then(d => {
                                    if (d.success) {
                                        acknowledged = true;
                                        ackDate = new Date().toLocaleDateString('en-US', {month:'short',day:'numeric',year:'numeric'});
                                        $el.closest('.card').style.borderLeftColor = 'var(--border)';
                                        $el.closest('[id^=release-card]').style.opacity = '0.82';
                                    }
                                });
                            "
                            class="btn btn-ghost btn-sm"
                            style="font-size:12px;"
                        >
                            Mark as seen
                        </button>
                    </div>
                </div>

                {{-- Card Body --}}
                <div style="padding:18px 20px;">

                    {{-- Release Notes --}}
                    @if($release->release_notes)
                    <p style="font-size:13.5px;color:var(--text-muted);line-height:1.7;margin-bottom:16px;">{{ $release->release_notes }}</p>
                    @endif

                    @php $hasFeatures = !empty($release->features); $hasFixes = !empty($release->bug_fixes); @endphp

                    @if($hasFeatures || $hasFixes)
                    <div style="display:grid;grid-template-columns:{{ ($hasFeatures && $hasFixes) ? '1fr 1fr' : '1fr' }};gap:16px;">

                        @if($hasFeatures)
                        <div>
                            <div style="font-size:11px;font-weight:700;color:var(--green);letter-spacing:.08em;text-transform:uppercase;margin-bottom:8px;display:flex;align-items:center;gap:6px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                What's New
                            </div>
                            <ul style="list-style:none;margin:0;padding:0;display:grid;gap:5px;">
                                @foreach($release->features as $feat)
                                <li style="font-size:13px;color:var(--text-muted);display:flex;align-items:flex-start;gap:8px;">
                                    <span style="color:var(--green);margin-top:2px;flex-shrink:0;">•</span>
                                    {{ $feat }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if($hasFixes)
                        <div>
                            <div style="font-size:11px;font-weight:700;color:var(--blue);letter-spacing:.08em;text-transform:uppercase;margin-bottom:8px;display:flex;align-items:center;gap:6px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                Bug Fixes
                            </div>
                            <ul style="list-style:none;margin:0;padding:0;display:grid;gap:5px;">
                                @foreach($release->bug_fixes as $fix)
                                <li style="font-size:13px;color:var(--text-muted);display:flex;align-items:flex-start;gap:8px;">
                                    <span style="color:var(--blue);margin-top:2px;flex-shrink:0;">•</span>
                                    {{ $fix }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                    </div>
                    @endif
                </div>

                {{-- Card Footer --}}
                <div style="padding:10px 20px;border-top:1px solid var(--border-light);display:flex;align-items:center;gap:8px;">
                    <template x-if="acknowledged">
                        <span style="font-size:12px;color:var(--green);display:flex;align-items:center;gap:5px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Acknowledged <span x-text="ackDate ? 'on ' + ackDate : ''"></span></span>
                        </span>
                    </template>
                    <template x-if="!acknowledged">
                        <span style="font-size:12px;color:var(--text-dim);">Not yet acknowledged</span>
                    </template>
                </div>

            </div>
        </div>
    </div>
    @endforeach

</div>
@endif

@endsection
