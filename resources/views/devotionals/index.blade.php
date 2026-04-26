{{-- resources/views/devotionals/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Devotionals')

@section('content')

<div class="flex items-center justify-between mb-20">
    <div>
        <h1 class="page-title">Devotional Tracking</h1>
        <p class="page-subtitle">Week of {{ $weekStart->format('F j') }} – {{ $weekEnd->format('F j, Y') }}</p>
    </div>
    <div class="flex gap-8">
        <a href="?week={{ $prevWeek }}" class="btn btn-ghost btn-sm">← Prev Week</a>
        <a href="?week={{ $nextWeek }}" class="btn btn-ghost btn-sm">Next Week →</a>
        @can('reports.export')
        <a href="{{ route('reports.devotionals') }}" class="btn btn-ghost btn-sm">
            @include('partials.icon',['name'=>'download','size'=>13])
            Export
        </a>
        @endcan
    </div>
</div>

{{-- Stats --}}
<div class="grid-4 mb-20">
    <div class="stat-card" style="--accent-color:var(--green)">
        <div class="stat-label">Completed Today</div>
        <div class="stat-value">{{ $completedToday }}/{{ $totalDisciples }}</div>
        <div class="stat-sub">{{ $todayRate }}% rate</div>
        <div class="stat-icon">@include('partials.icon',['name'=>'heart','size'=>20,'color'=>'var(--green)'])</div>
    </div>
    <div class="stat-card" style="--accent-color:var(--blue)">
        <div class="stat-label">Weekly Average</div>
        <div class="stat-value">{{ $weeklyAvg }}%</div>
        <div class="stat-sub">
            @if($weeklyChange >= 0) ↑ {{ abs($weeklyChange) }}% vs last week
            @else ↓ {{ abs($weeklyChange) }}% vs last week @endif
        </div>
        <div class="stat-icon">@include('partials.icon',['name'=>'chart','size'=>20,'color'=>'var(--blue)'])</div>
    </div>
    <div class="stat-card" style="--accent-color:var(--orange)">
        <div class="stat-label">Longest Streak</div>
        <div class="stat-value">{{ $longestStreak->streak ?? 0 }}d</div>
        <div class="stat-sub">{{ $longestStreak->name ?? '—' }}</div>
        <div class="stat-icon">@include('partials.icon',['name'=>'fire','size'=>20,'color'=>'var(--orange)'])</div>
    </div>
    <div class="stat-card" style="--accent-color:var(--purple)">
        <div class="stat-label">Perfect Week</div>
        <div class="stat-value">{{ $perfectWeekCount }}</div>
        <div class="stat-sub">disciples (7/7 days)</div>
        <div class="stat-icon">@include('partials.icon',['name'=>'star','size'=>20,'color'=>'var(--purple)'])</div>
    </div>
</div>

{{-- Weekly grid table --}}
<div class="card">
    <div class="card-header">
        <span class="section-title">This Week's Devotional Status</span>
        <div class="flex gap-12" style="font-size:12px;color:var(--text-muted);">
            <span style="display:flex;align-items:center;gap:5px;"><div style="width:10px;height:10px;border-radius:3px;background:rgba(63,185,80,.4);"></div>Completed</span>
            <span style="display:flex;align-items:center;gap:5px;"><div style="width:10px;height:10px;border-radius:3px;background:rgba(248,81,73,.2);"></div>Missed</span>
            <span style="display:flex;align-items:center;gap:5px;"><div style="width:10px;height:10px;border-radius:3px;background:var(--border);"></div>Upcoming</span>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Disciple</th>
                    @foreach($weekDays as $day)
                    <th style="text-align:center;min-width:60px;">
                        <div>{{ $day->format('D') }}</div>
                        <div style="font-size:10px;color:var(--text-dim);">{{ $day->format('M j') }}</div>
                    </th>
                    @endforeach
                    <th>Streak</th>
                    <th style="min-width:110px;">Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($disciples as $d)
                @php $devData = $d->weeklyDevotionals; @endphp
                <tr>
                    <td>
                        <div class="flex items-center gap-12">
                            <div class="avatar avatar-sm" style="background:rgba(212,168,83,.12);border:1.5px solid rgba(212,168,83,.25);color:var(--gold);">
                                {{ strtoupper(substr($d->first_name,0,1).substr($d->last_name,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:500;font-size:14px;">{{ $d->first_name }} {{ $d->last_name }}</div>
                                <div style="font-size:11px;color:var(--text-muted);">{{ $d->membership->leader->first_name ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    @foreach($weekDays as $day)
                    @php
                        $dateStr  = $day->toDateString();
                        $isToday  = $day->isToday();
                        $isFuture = $day->isFuture();
                        $done     = isset($devData[$dateStr]) && $devData[$dateStr];
                    @endphp
                    <td style="text-align:center;">
                        @if($isFuture)
                            <div style="width:28px;height:28px;border-radius:7px;background:var(--border-light);margin:0 auto;"></div>
                        @else
                            <div style="width:28px;height:28px;border-radius:7px;background:{{ $done ? 'rgba(63,185,80,.2)' : 'rgba(248,81,73,.1)' }};border:1px solid {{ $done ? 'rgba(63,185,80,.35)' : 'rgba(248,81,73,.2)' }};{{ $isToday ? 'box-shadow:0 0 0 2px var(--gold);' : '' }}display:flex;align-items:center;justify-content:center;margin:0 auto;transition:all .15s;">
                                @if($done)
                                    @include('partials.icon',['name'=>'check','size'=>13,'color'=>'var(--green)'])
                                @else
                                    @include('partials.icon',['name'=>'x','size'=>11,'color'=>'var(--red)'])
                                @endif
                            </div>
                        @endif
                    </td>
                    @endforeach
                    <td>
                        <div class="flex items-center gap-5">
                            @include('partials.icon',['name'=>'fire','size'=>13,'color'=>'var(--orange)'])
                            <span class="mono" style="color:var(--orange);font-size:13px;">{{ $d->streak ?? 0 }}d</span>
                        </div>
                    </td>
                    <td>
                        @php $rate=$d->devotional_rate??0; $rc=$rate>=80?'var(--green)':($rate>=60?'var(--orange)':'var(--red)'); @endphp
                        <div class="flex items-center gap-8">
                            <div class="progress" style="flex:1;"><div class="progress-bar" style="width:{{ $rate }}%;background:{{ $rc }};"></div></div>
                            <span class="mono" style="font-size:12px;color:{{ $rc }};">{{ $rate }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Quick Mark Today (for leaders) --}}
@if(auth()->user()->isChurchAdmin() || auth()->user()->isRestrictedLeader())
<div class="card mt-16">
    <div class="card-header">
        <span class="section-title">Mark Today's Devotionals</span>
        <span style="font-size:13px;color:var(--text-muted);">{{ today()->format('F d, Y') }}</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('devotionals.store') }}">
            @csrf
            <input type="hidden" name="date" value="{{ today()->toDateString() }}">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px;margin-bottom:16px;">
                @foreach($disciples as $d)
                @php $todayDone = isset($d->weeklyDevotionals[today()->toDateString()]) && $d->weeklyDevotionals[today()->toDateString()]; @endphp
                <label style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--bg);border-radius:10px;border:1px solid {{ $todayDone ? 'rgba(63,185,80,.3)' : 'var(--border-light)' }};cursor:pointer;transition:border-color .15s;">
                    <input type="hidden" name="completed[{{ $d->id }}]" value="0">
                    <input type="checkbox" name="completed[{{ $d->id }}]" value="1" {{ $todayDone ? 'checked' : '' }} style="width:auto;">
                    <div class="avatar avatar-sm" style="background:rgba(212,168,83,.12);border:1.5px solid rgba(212,168,83,.25);color:var(--gold);">
                        {{ strtoupper(substr($d->first_name,0,1).substr($d->last_name,0,1)) }}
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:500;">{{ $d->first_name }} {{ $d->last_name }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $d->streak ?? 0 }}d streak</div>
                    </div>
                </label>
                @endforeach
            </div>
            <button type="submit" class="btn btn-primary">
                @include('partials.icon',['name'=>'check','size'=>14])
                Save Today's Devotionals
            </button>
        </form>
    </div>
</div>
@endif

@endsection
