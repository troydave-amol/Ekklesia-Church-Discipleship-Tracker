{{-- resources/views/dashboard/leader.blade.php --}}
@extends('layouts.app')
@section('title', 'Leader Dashboard')

@section('content')
@php $user = auth()->user(); @endphp

<div class="flex items-center justify-between mb-20">
    <div>
        <h1 class="page-title">Hello, {{ $user->first_name }}</h1>
        <p class="page-subtitle">Your disciples · {{ $myDisciples }} assigned</p>
    </div>
    <div class="flex gap-8">
        <a href="{{ route('users.disciples') }}" class="btn btn-ghost btn-sm">My disciples</a>
        <a href="{{ route('attendance.index') }}" class="btn btn-primary btn-sm">Attendance</a>
    </div>
</div>

<div class="grid-4 mb-20">
    <div class="stat-card" style="--accent-color:var(--gold)">
        <div class="stat-label">My disciples</div>
        <div class="stat-value">{{ $myDisciples }}</div>
        <div class="stat-sub">{{ $needsAttention }} need attention</div>
        <div class="stat-icon">@include('partials.icon',['name'=>'users','size'=>20,'color'=>'var(--gold)'])</div>
    </div>
    <div class="stat-card" style="--accent-color:var(--green)">
        <div class="stat-label">Avg attendance</div>
        <div class="stat-value">{{ $attendanceRate }}%</div>
        <div class="stat-sub">Your group</div>
        <div class="stat-icon">@include('partials.icon',['name'=>'calendar','size'=>20,'color'=>'var(--green)'])</div>
    </div>
    <div class="stat-card" style="--accent-color:var(--purple)">
        <div class="stat-label">Devotional rate</div>
        <div class="stat-value">{{ $devotionalRate }}%</div>
        <div class="stat-sub">This month (avg)</div>
        <div class="stat-icon">@include('partials.icon',['name'=>'heart','size'=>20,'color'=>'var(--purple)'])</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="section-title">Disciples</span>
        <a href="{{ route('devotionals.index') }}" class="btn btn-ghost btn-sm">Devotionals</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Attendance</th>
                    <th>Devotionals</th>
                </tr>
            </thead>
            <tbody>
                @forelse($disciples as $d)
                <tr>
                    <td>
                        <div style="font-weight:500;">{{ $d->first_name }} {{ $d->last_name }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $d->email }}</div>
                    </td>
                    <td><span class="mono">{{ $d->attendanceRate ?? 0 }}%</span></td>
                    <td><span class="mono">{{ $d->devotionalRate ?? 0 }}%</span></td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align:center;padding:32px;color:var(--text-muted);">No disciples assigned yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
