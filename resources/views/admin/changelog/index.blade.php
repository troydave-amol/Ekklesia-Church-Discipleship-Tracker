@extends('layouts.central-admin')

@section('title', 'System Changelog')
@section('topbar-title', 'Changelog')

@section('content')
<div class="ca-page-head" style="display:flex;align-items:flex-start;justify-content:space-between;">
    <div>
        <h1 class="ca-page-title">System Changelog</h1>
        <p class="ca-page-desc">View release history and broadcast updates to all tenants.</p>
    </div>
</div>

@if(empty($sections))
    <div class="ca-card">
        <div class="ca-card-body" style="text-align:center;padding:48px 24px;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--ca-ink-4)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:12px;">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            <div style="font-size:14px;font-weight:500;color:var(--ca-ink-3);">No CHANGELOG.md found</div>
            <div style="font-size:12.5px;color:var(--ca-ink-4);margin-top:4px;">Create a CHANGELOG.md in the project root to see release history here.</div>
        </div>
    </div>
@else
    @foreach($sections as $i => $section)
        @php
            $ver = $section['version'];
            $isBroadcasted = in_array(strtolower("release v{$ver}"), $broadcastedTitles);
            $isUnreleased = strtolower($ver) === 'unreleased';
            // Build notes text for the hidden form field
            $noteLines = [];
            foreach ($section['entries'] as $category => $items) {
                $noteLines[] = "**{$category}**";
                foreach ($items as $item) {
                    $noteLines[] = "- {$item}";
                }
                $noteLines[] = '';
            }
            $notesText = implode("\n", $noteLines);
        @endphp

        <div class="ca-card" style="margin-bottom:18px;">
            <div class="ca-card-head">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="ca-card-title" style="font-size:16px;">
                        @if($isUnreleased)
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--ca-amber)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Unreleased
                        @else
                            v{{ $ver }}
                        @endif
                    </span>
                    @if($section['date'])
                        <span class="ca-table-muted">{{ $section['date'] }}</span>
                    @endif
                    @if($isBroadcasted)
                        <span class="ca-pill ca-pill-ok"><span class="ca-pill-dot"></span> Broadcasted</span>
                    @elseif($isUnreleased)
                        <span class="ca-pill ca-pill-pending"><span class="ca-pill-dot"></span> In Progress</span>
                    @endif
                </div>
                <div>
                    @if(!$isUnreleased && !$isBroadcasted)
                        <form action="{{ route('admin.changelog.broadcast') }}" method="POST" onsubmit="return confirm('Broadcast v{{ $ver }} release notes to all active tenants?')">
                            @csrf
                            <input type="hidden" name="version" value="{{ $ver }}">
                            <input type="hidden" name="notes" value="{{ $notesText }}">
                            <button type="submit" class="ca-btn ca-btn-approve">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                                    <path d="M19.07 4.93a10 10 0 010 14.14"/>
                                    <path d="M15.54 8.46a5 5 0 010 7.07"/>
                                </svg>
                                Broadcast
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="ca-card-body">
                @foreach($section['entries'] as $category => $items)
                    @php
                        $catColor = match($category) {
                            'Added'   => 'var(--ca-sage)',
                            'Fixed'   => 'var(--ca-blue)',
                            'Changed' => 'var(--ca-amber)',
                            'Removed' => 'var(--ca-red)',
                            default   => 'var(--ca-ink-4)',
                        };
                        $catBg = match($category) {
                            'Added'   => 'var(--ca-sage-dim)',
                            'Fixed'   => 'var(--ca-blue-dim)',
                            'Changed' => 'var(--ca-amber-dim)',
                            'Removed' => 'var(--ca-red-dim)',
                            default   => 'var(--ca-surface-dim)',
                        };
                    @endphp
                    <div style="margin-bottom:16px;">
                        <span style="display:inline-block;padding:3px 10px;border-radius:6px;font-size:11px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;background:{{ $catBg }};color:{{ $catColor }};margin-bottom:8px;">{{ $category }}</span>
                        <ul style="list-style:none;padding:0;margin:0;">
                            @foreach($items as $item)
                                <li style="display:flex;align-items:flex-start;gap:8px;padding:6px 0;font-size:13.5px;color:var(--ca-ink-2);border-bottom:1px solid var(--ca-border);">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="{{ $catColor }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;">
                                        @if($category === 'Added')
                                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                                        @elseif($category === 'Fixed')
                                            <polyline points="20 6 9 17 4 12"/>
                                        @elseif($category === 'Changed')
                                            <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/>
                                        @elseif($category === 'Removed')
                                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                        @endif
                                    </svg>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                @if(empty($section['entries']))
                    <div style="text-align:center;padding:20px;color:var(--ca-ink-4);font-size:13px;">
                        No categorized entries for this version.
                    </div>
                @endif
            </div>
        </div>
    @endforeach
@endif
@endsection
