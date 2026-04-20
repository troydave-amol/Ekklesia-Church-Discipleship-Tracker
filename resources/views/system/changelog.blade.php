@extends('layouts.app')

@section('title', 'Changelog')

@section('content')
<div class="page-header" style="margin-bottom: 24px;">
    <h1 class="page-title">Changelog</h1>
</div>

<!-- Changelog Timeline -->
<div style="position: relative; padding: 20px 0;">
    <!-- Timeline Line -->
    <div style="position: absolute; left: 19px; top: 0; bottom: 0; width: 2px; background: #30363D;"></div>

    @if(!empty($changelogSections))
        @foreach($changelogSections as $index => $section)
            <div style="margin-bottom: 32px; position: relative;">
                <!-- Timeline Node -->
                <div style="position: absolute; left: -4px; top: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <div style="width: 24px; height: 24px; border-radius: 50%; background: @if($section['version'] === 'Unreleased') #6E7681 @else #D4A853 @endif; border: 3px solid #0D1117; display: flex; align-items: center; justify-content: center;">
                        <svg width="12" height="12" fill="white" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Content -->
                <div style="margin-left: 60px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <span style="display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; @if($section['version'] === 'Unreleased') border: 2px dashed #8B949E; color: #8B949E; background: transparent; @else background: rgba(212, 168, 83, 0.1); color: #E8C47A; @endif">
                            {{ $section['version'] }}
                        </span>
                        @if($section['date'])
                            <span style="font-size: 12px; color: #8B949E;">{{ $section['date'] }}</span>
                        @endif
                    </div>

                    <div class="card" style="padding: 16px;">
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
                                @if(!$loop->last)
                                    <div style="height: 1px; background: #30363D; margin: 12px 0;"></div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div style="text-align: center; padding: 40px 20px;">
            <p style="font-size: 16px; color: #8B949E;">No changelog data available.</p>
        </div>
    @endif
</div>
@endsection
