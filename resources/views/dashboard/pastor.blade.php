{{-- resources/views/dashboard/pastor.blade.php --}}
@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php
    $user = auth()->user();
    $church = $user->church;
@endphp

{{-- Page Header --}}
<div class="flex items-center justify-between mb-20">
    <div>
        <h1 class="page-title">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ $user->first_name }}</h1>
        <p class="page-subtitle">{{ $church->name }}</p>
    </div>
    <div class="flex gap-8">
        <a href="{{ route('reports.index') }}" class="btn btn-ghost">
            @include('partials.icon',['name'=>'download','size'=>14])
            Reports
        </a>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            @include('partials.icon',['name'=>'plus','size'=>14])
            Add Disciple
        </a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid-4 mb-20">
    <div class="stat-card" style="--accent-color:var(--gold)">
        <div class="stat-label">Total Disciples</div>
        <div class="stat-value">{{ $totalDisciples }}</div>
        <div class="stat-sub">
            @if($newThisMonth > 0)
                ↑ {{ $newThisMonth }} this month
            @else
                No new disciples this month
            @endif
        </div>
        <div class="stat-icon">@include('partials.icon',['name'=>'users','size'=>20,'color'=>'var(--gold)'])</div>
    </div>

    <div class="stat-card" style="--accent-color:var(--blue)">
        <div class="stat-label">Active Leaders</div>
        <div class="stat-value">{{ $activeLeaders }}</div>
        <div class="stat-sub">
            @php $maxLeaders = match($church->subscription_plan){ 'premium'=>'Unlimited','standard'=>10,'basic'=>5,default=>5 }; @endphp
            Max: {{ $maxLeaders }} on {{ ucfirst($church->subscription_plan) }}
        </div>
        <div class="stat-icon">@include('partials.icon',['name'=>'shield','size'=>20,'color'=>'var(--blue)'])</div>
    </div>

    <div class="stat-card" style="--accent-color:var(--green)">
        <div class="stat-label">Avg Attendance</div>
        <div class="stat-value">{{ $avgAttendance }}%</div>
        <div class="stat-sub">Across all programs</div>
        <div class="stat-icon">@include('partials.icon',['name'=>'calendar','size'=>20,'color'=>'var(--green)'])</div>
    </div>

    <div class="stat-card" style="--accent-color:var(--purple)">
        <div class="stat-label">Devotional Rate</div>
        <div class="stat-value">{{ $devotionalRate }}%</div>
        <div class="stat-sub">This week</div>
        <div class="stat-icon">@include('partials.icon',['name'=>'heart','size'=>20,'color'=>'var(--purple)'])</div>
    </div>
</div>

{{-- Programs + Leader Performance --}}
<div class="grid-2 mb-20">

    {{-- Program Progress --}}
    <div class="card">
        <div class="card-header">
            <span class="section-title">Program Progress</span>
            <span class="text-muted" style="font-size:12px;">{{ $programs->count() }} active programs</span>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:20px;">
            @foreach($programs as $program)
            @php
                $pColors = ['Life Class'=>'var(--blue)','SOL 1'=>'var(--purple)','SOL 2'=>'var(--orange)','SOL 3'=>'var(--green)'];
                $pColor  = $pColors[$program->name] ?? 'var(--gold)';
            @endphp
            <div>
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-8">
                        <div style="width:8px;height:8px;border-radius:50%;background:{{ $pColor }};flex-shrink:0;"></div>
                        <span style="font-size:14px;">{{ $program->name }}</span>
                    </div>
                    <span class="text-muted" style="font-size:13px;">{{ $program->members_count }} disciples</span>
                </div>
                <div class="progress">
                    <div class="progress-bar" style="width:{{ $program->completion_rate ?? 0 }}%;background:{{ $pColor }};"></div>
                </div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;text-align:right;font-family:'DM Mono',monospace;">{{ $program->completion_rate ?? 0 }}% complete</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Leader Performance --}}
    <div class="card">
        <div class="card-header">
            <span class="section-title">Leader Performance</span>
            <a href="{{ route('users.index') }}?role=leader" class="btn btn-ghost btn-sm">View All</a>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:4px;">
            @foreach($leaders as $index => $leader)
            @php $lColors = ['var(--gold)','var(--blue)','var(--purple)','var(--green)','var(--orange)']; $lColor = $lColors[$index % 5]; @endphp
            <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:{{ !$loop->last ? '1px solid var(--border-light)' : 'none' }};">
                @php $initials = strtoupper(substr($leader->first_name,0,1).substr($leader->last_name,0,1)); @endphp
                <div class="avatar avatar-md" style="background:{{ $lColor }}22;border:1.5px solid {{ $lColor }}44;color:{{ $lColor }};">{{ $initials }}</div>
                <div style="flex:1;">
                    <div style="font-size:14px;font-weight:500;">{{ $leader->first_name }} {{ $leader->last_name }}</div>
                    <div style="font-size:12px;color:var(--text-muted);">{{ $leader->assigned_disciples_count }} disciples</div>
                </div>
                <div style="display:flex;gap:14px;">
                    <div style="text-align:center;">
                        <div style="font-size:10px;color:var(--text-muted);margin-bottom:2px;">ATT.</div>
                        <div class="mono" style="font-size:13px;color:var(--green);">{{ $leader->attendance_rate ?? 0 }}%</div>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-size:10px;color:var(--text-muted);margin-bottom:2px;">DEV.</div>
                        <div class="mono" style="font-size:13px;color:var(--purple);">{{ $leader->devotional_rate ?? 0 }}%</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Disciples Table --}}
<div class="card">
    <div class="card-header">
        <span class="section-title">Disciples Overview</span>
        <div class="flex gap-8">
            <form method="GET" action="{{ route('dashboard') }}" class="table-controls">
                <div class="input-group">
                    @include('partials.icon',['name'=>'search','size'=>14,'color'=>'var(--text-dim)'])
                    <input name="q" value="{{ request('q') }}" placeholder="Search disciples…" style="max-width:200px;">
                </div>
                <select name="program_id" style="max-width:150px;">
                    <option value="">All Programs</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-ghost">Filter</button>
            </form>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Program</th>
                    <th>Leader</th>
                    <th style="min-width:130px;">Attendance</th>
                    <th style="min-width:130px;">Devotional</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($disciples as $disciple)
                <tr>
                    <td>
                        <div class="flex items-center gap-12">
                            @php $ini = strtoupper(substr($disciple->first_name,0,1).substr($disciple->last_name,0,1)); @endphp
                            <div class="avatar avatar-sm" style="background:rgba(212,168,83,.15);border:1.5px solid rgba(212,168,83,.3);color:var(--gold);">{{ $ini }}</div>
                            <div>
                                <div style="font-weight:500;">{{ $disciple->first_name }} {{ $disciple->last_name }}</div>
                                @if($disciple->nickname)
                                    <div style="font-size:12px;color:var(--text-muted);">"{{ $disciple->nickname }}"</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td><span style="font-size:13px;color:var(--text-muted);">{{ $disciple->membership->destinyLevel() ?? '—' }}</span></td>
                    <td>
                        @if($disciple->membership->leader)
                            <span style="font-size:13px;">{{ $disciple->membership->leader->first_name }} {{ $disciple->membership->leader->last_name }}</span>
                        @else
                            <span style="color:var(--text-dim);">Unassigned</span>
                        @endif
                    </td>
                    <td>
                        @php $att = $disciple->attendance_rate ?? 0; $attColor = $att >= 80 ? 'var(--green)' : ($att >= 60 ? 'var(--orange)' : 'var(--red)'); @endphp
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="progress" style="flex:1;">
                                <div class="progress-bar" style="width:{{ $att }}%;background:{{ $attColor }};"></div>
                            </div>
                            <span class="mono" style="font-size:12px;color:{{ $attColor }};min-width:32px;text-align:right;">{{ $att }}%</span>
                        </div>
                    </td>
                    <td>
                        @php $dev = $disciple->devotional_rate ?? 0; $devColor = $dev >= 80 ? 'var(--purple)' : ($dev >= 60 ? 'var(--orange)' : 'var(--red)'); @endphp
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="progress" style="flex:1;">
                                <div class="progress-bar" style="width:{{ $dev }}%;background:{{ $devColor }};"></div>
                            </div>
                            <span class="mono" style="font-size:12px;color:{{ $devColor }};min-width:32px;text-align:right;">{{ $dev }}%</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $disciple->membership->status === 'active' ? 'badge-green' : 'badge-red' }}">
                            {{ strtoupper($disciple->membership->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="flex gap-8">
                            <a href="{{ route('users.show', $disciple) }}" class="btn btn-ghost btn-icon btn-sm">
                                @include('partials.icon',['name'=>'user','size'=>13])
                            </a>
                            <a href="{{ route('users.edit', $disciple) }}" class="btn btn-ghost btn-icon btn-sm">
                                @include('partials.icon',['name'=>'edit','size'=>13])
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">
                        No disciples found. <a href="{{ route('users.create') }}" style="color:var(--gold);">Add one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($disciples->hasPages())
    <div class="card-footer flex justify-between items-center">
        <span class="text-muted" style="font-size:13px;">Showing {{ $disciples->firstItem() }}–{{ $disciples->lastItem() }} of {{ $disciples->total() }}</span>
        {{ $disciples->withQueryString()->links('partials.pagination') }}
    </div>
    @endif
</div>
@endsection
