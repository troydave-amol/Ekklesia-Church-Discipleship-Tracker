@extends('layouts.app')

@section('title', 'System Information')

@section('content')
<div class="page-header" style="margin-bottom: 24px;">
    <h1 class="page-title">System information</h1>
</div>

<!-- Metrics -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="card" style="padding: 20px;">
        <div style="font-size: 11px; font-weight: 600; color: #8B949E; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px;">App version</div>
        <div style="font-size: 24px; font-weight: 700; color: #E6EDF3;">{{ $version ?? '0.1.0' }}</div>
    </div>

    <div class="card" style="padding: 20px;">
        <div style="font-size: 11px; font-weight: 600; color: #8B949E; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px;">Latest release</div>
        <div style="font-size: 24px; font-weight: 700; color: #E6EDF3;">{{ $latestRelease ?? 'N/A' }}</div>
    </div>

    <div class="card" style="padding: 20px;">
        <div style="font-size: 11px; font-weight: 600; color: #8B949E; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px;">Environment</div>
        <div style="font-size: 24px; font-weight: 700; color: #E6EDF3;">{{ app()->environment() }}</div>
    </div>
</div>

<!-- Changelog Section -->
<div class="card">
    <div style="padding: 16px; border-bottom: 1px solid #30363D;">
        <h2 style="font-size: 16px; font-weight: 600;">Recent releases</h2>
    </div>
    
    <div style="padding: 20px;">
        @if(!empty($changelogSections))
            @foreach($changelogSections as $section)
                <div style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #30363D;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <span style="display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: rgba(212, 168, 83, 0.1); color: #E8C47A;">
                            {{ $section['version'] }}
                        </span>
                        @if($section['date'])
                            <span style="font-size: 12px; color: #8B949E;">{{ $section['date'] }}</span>
                        @endif
                    </div>

                    @foreach(['Added', 'Changed', 'Fixed', 'Removed'] as $type)
                        @if(!empty($section['entries'][$type]))
                            <div style="margin-bottom: 12px;">
                                <h4 style="font-size: 12px; font-weight: 600; color: #8B949E; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px;">{{ $type }}</h4>
                                <ul style="margin: 0; padding: 0; list-style: none;">
                                    @foreach($section['entries'][$type] as $entry)
                                        <li style="margin-bottom: 4px; color: #D1D5DB; font-size: 13px;">• {{ $entry }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        @else
            <p style="color: #8B949E;">No release information available.</p>
        @endif
    </div>
</div>
@endsection
