{{-- resources/views/dashboard/disciple.blade.php --}}
@extends('layouts.app')
@section('title', 'My Journey')

@section('content')
@php $user = auth()->user(); @endphp

<div class="flex items-center justify-between mb-20">
    <div>
        <h1 class="page-title">Welcome back, {{ $user->first_name }}</h1>
        <p class="page-subtitle">{{ $currentProgram->name ?? 'Life Class' }} · Week {{ $currentLesson->week_number ?? 1 }} of {{ $totalLessons }}</p>
    </div>
    <form method="POST" action="{{ route('devotionals.markToday') }}">
        @csrf
        @if(!$devotionalMarkedToday)
        <button type="submit" class="btn btn-primary">
            @include('partials.icon',['name'=>'heart','size'=>14])
            Mark Today's Devotional
        </button>
        @else
        <div style="display:flex;align-items:center;gap:8px;padding:9px 16px;background:rgba(63,185,80,.12);border:1px solid rgba(63,185,80,.3);border-radius:8px;color:var(--green);font-size:13px;font-weight:500;">
            @include('partials.icon',['name'=>'check','size'=>14,'color'=>'var(--green)'])
            Devotional done today!
        </div>
        @endif
    </form>
</div>

{{-- Stats --}}
<div class="grid-3 mb-20">
    <div class="stat-card" style="--accent-color:var(--blue)">
        <div class="stat-label">Lessons Completed</div>
        <div class="stat-value">{{ $completedLessons }}/{{ $totalLessons }}</div>
        <div class="stat-sub">{{ $completionRate }}% of {{ $currentProgram->name ?? 'program' }}</div>
        <div class="stat-icon">@include('partials.icon',['name'=>'book','size'=>20,'color'=>'var(--blue)'])</div>
    </div>
    <div class="stat-card" style="--accent-color:var(--green)">
        <div class="stat-label">Attendance Rate</div>
        <div class="stat-value">{{ $attendanceRate }}%</div>
        <div class="stat-sub">{{ $attendedSessions }} of {{ $totalSessions }} sessions</div>
        <div class="stat-icon">@include('partials.icon',['name'=>'calendar','size'=>20,'color'=>'var(--green)'])</div>
    </div>
    <div class="stat-card" style="--accent-color:var(--orange)">
        <div class="stat-label">Devotional Streak</div>
        <div class="stat-value">{{ $streak }} days</div>
        <div class="stat-sub">{{ $streak >= $personalBest ? '🔥 Personal best!' : 'Best: '.$personalBest.' days' }}</div>
        <div class="stat-icon">@include('partials.icon',['name'=>'fire','size'=>20,'color'=>'var(--orange)'])</div>
    </div>
</div>

<div class="grid-2">
    {{-- My Lessons --}}
    <div class="card">
        <div class="card-header">
            <span class="section-title">My Lessons</span>
            <a href="{{ route('lessons.my') }}" class="btn btn-ghost btn-sm">View All</a>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
            @foreach($lessons as $lesson)
            @php
                $done    = $lesson->is_completed ?? false;
                $current = $lesson->is_current   ?? false;
            @endphp
            <a href="{{ route('lessons.show', $lesson) }}"
               style="display:flex;align-items:center;gap:14px;padding:12px 14px;background:var(--bg);border-radius:10px;border:1px solid {{ $current ? 'var(--gold-dim)' : 'var(--border-light)' }};transition:border-color .2s;">
                <div style="width:34px;height:34px;border-radius:8px;background:{{ $done ? 'rgba(63,185,80,.15)' : ($current ? 'rgba(212,168,83,.12)' : 'var(--border-light)') }};border:1px solid {{ $done ? 'rgba(63,185,80,.3)' : ($current ? 'rgba(212,168,83,.25)' : 'var(--border)') }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    @if($done)
                        @include('partials.icon',['name'=>'check','size'=>14,'color'=>'var(--green)'])
                    @elseif($current)
                        @include('partials.icon',['name'=>'arrow-right','size'=>14,'color'=>'var(--gold)'])
                    @else
                        <span class="mono" style="font-size:12px;color:var(--text-dim);">{{ $lesson->week_number }}</span>
                    @endif
                </div>
                <div style="flex:1;">
                    <div style="font-size:14px;font-weight:{{ $done ? '400' : '500' }};color:{{ $done ? 'var(--text-muted)' : 'var(--text)' }};{{ $done ? 'text-decoration:line-through;' : '' }}">{{ $lesson->title }}</div>
                    <div style="font-size:12px;color:var(--text-dim);">Week {{ $lesson->week_number }}</div>
                </div>
                @if($current)
                    <span class="badge badge-gold">CURRENT</span>
                @endif
            </a>
            @endforeach
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:16px;">
        {{-- Devotional Calendar --}}
        <div class="card">
            <div class="card-header">
                <span class="section-title">This Month</span>
                <span class="mono" style="font-size:12px;color:var(--text-muted);">{{ now()->format('F Y') }}</span>
            </div>
            <div class="card-body">
                {{-- Day labels --}}
                <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:6px;">
                    @foreach(['Su','Mo','Tu','We','Th','Fr','Sa'] as $d)
                        <div style="text-align:center;font-size:10px;color:var(--text-dim);font-weight:600;">{{ $d }}</div>
                    @endforeach
                </div>
                {{-- Calendar cells --}}
                <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;">
                    {{-- Leading empty cells --}}
                    @for($i = 0; $i < now()->startOfMonth()->dayOfWeek; $i++)
                        <div></div>
                    @endfor
                    @for($day = 1; $day <= now()->daysInMonth; $day++)
                    @php
                        $date    = now()->startOfMonth()->addDays($day - 1);
                        $isFuture = $date->isFuture() && !$date->isToday();
                        $isToday  = $date->isToday();
                        $completed = $calendarData[$date->toDateString()] ?? false;
                    @endphp
                    <div style="aspect-ratio:1;border-radius:7px;background:{{ $isToday ? 'rgba(212,168,83,.2)' : ($isFuture ? 'var(--border-light)' : ($completed ? 'rgba(63,185,80,.25)' : 'rgba(248,81,73,.1)')) }};border:{{ $isToday ? '1.5px solid var(--gold)' : '1px solid transparent' }};display:flex;align-items:center;justify-content:center;font-size:10px;color:{{ $isToday ? 'var(--gold)' : ($isFuture ? 'var(--text-dim)' : ($completed ? 'var(--green)' : 'var(--red)')) }};font-family:'DM Mono',monospace;font-weight:500;">
                        {{ $isToday ? '●' : ($isFuture ? '' : $day) }}
                    </div>
                    @endfor
                </div>
                @if($streak > 0)
                <div style="font-size:12px;color:var(--text-muted);margin-top:14px;text-align:center;">
                    🔥 {{ $streak }}-day streak — keep it going!
                </div>
                @endif
            </div>
        </div>

        {{-- My Leader --}}
        <div class="card">
            <div class="card-header"><span class="section-title">My Leader</span></div>
            <div class="card-body">
                @if($leader = $user->membership->leader ?? null)
                <div class="flex items-center gap-12" style="margin-bottom:14px;">
                    <div class="avatar avatar-lg" style="background:rgba(212,168,83,.15);border:1.5px solid rgba(212,168,83,.3);color:var(--gold);">
                        {{ strtoupper(substr($leader->first_name,0,1).substr($leader->last_name,0,1)) }}
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:15px;">{{ $leader->first_name }} {{ $leader->last_name }}</div>
                        <span class="badge badge-blue" style="margin-top:4px;">LEADER</span>
                    </div>
                </div>
                <div style="font-size:13px;color:var(--text-muted);">{{ $leader->email }}</div>
                @else
                <div style="color:var(--text-muted);font-size:13px;">No leader assigned yet.</div>
                @endif
            </div>
        </div>

        {{-- Program progress --}}
        <div class="card">
            <div class="card-header"><span class="section-title">Program Progress</span></div>
            <div class="card-body">
                <div style="margin-bottom:10px;font-size:14px;font-weight:500;">{{ $currentProgram->name ?? 'Life Class' }}</div>
                <div class="progress" style="margin-bottom:8px;height:8px;">
                    <div class="progress-bar" style="width:{{ $completionRate }}%;background:var(--blue);"></div>
                </div>
                <div class="flex justify-between" style="font-size:12px;color:var(--text-muted);">
                    <span>{{ $completedLessons }} lessons done</span>
                    <span class="mono" style="color:var(--blue);">{{ $completionRate }}%</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
