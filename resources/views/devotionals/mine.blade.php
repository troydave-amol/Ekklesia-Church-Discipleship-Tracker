{{-- resources/views/devotionals/mine.blade.php --}}
@extends('layouts.app')
@section('title', 'My devotionals')

@section('content')
@php $d = $disciples->first(); @endphp

<div class="flex items-center justify-between mb-20">
    <div>
        <h1 class="page-title">My devotionals</h1>
        <p class="page-subtitle">Week of {{ $weekStart->format('F j') }} – {{ $weekEnd->format('F j, Y') }}</p>
    </div>
    <div class="flex gap-8">
        <a href="?week={{ $prevWeek }}" class="btn btn-ghost btn-sm">← Prev Week</a>
        <a href="?week={{ $nextWeek }}" class="btn btn-ghost btn-sm">Next Week →</a>
        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="section-title">This week</span>
        <span style="font-size:13px;color:var(--text-muted);">Streak: {{ $d->streak ?? 0 }}d · Rate: {{ $d->devotional_rate ?? 0 }}%</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Day</th>
                    @foreach($weekDays as $day)
                    <th style="text-align:center;min-width:60px;">
                        <div>{{ $day->format('D') }}</div>
                        <div style="font-size:10px;color:var(--text-dim);">{{ $day->format('M j') }}</div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight:500;">Status</td>
                    @foreach($weekDays as $day)
                    @php
                        $dateStr = $day->toDateString();
                        $done = isset($d->weeklyDevotionals[$dateStr]) && $d->weeklyDevotionals[$dateStr];
                        $isFuture = $day->isFuture();
                    @endphp
                    <td style="text-align:center;">
                        @if($isFuture)
                            <div style="width:28px;height:28px;border-radius:7px;background:var(--border-light);margin:0 auto;"></div>
                        @else
                            <div style="width:28px;height:28px;border-radius:7px;background:{{ $done ? 'rgba(63,185,80,.2)' : 'rgba(248,81,73,.1)' }};border:1px solid {{ $done ? 'rgba(63,185,80,.35)' : 'rgba(248,81,73,.2)' }};display:flex;align-items:center;justify-content:center;margin:0 auto;">
                                @if($done)
                                    @include('partials.icon',['name'=>'check','size'=>13,'color'=>'var(--green)'])
                                @else
                                    @include('partials.icon',['name'=>'x','size'=>11,'color'=>'var(--red)'])
                                @endif
                            </div>
                        @endif
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card mt-16">
    <div class="card-body" style="font-size:14px;color:var(--text-muted);">
        Mark today’s devotional from the <a href="{{ route('dashboard') }}" style="color:var(--gold);">dashboard</a>.
    </div>
</div>
@endsection
