{{-- resources/views/reports/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Reports')

@section('content')
@php
    $plan = auth()->user()->church->subscription_plan ?? 'free';
    $hasReports = in_array($plan, ['standard','premium']);
    $hasBulk    = $plan === 'premium';
@endphp

<div class="flex items-center justify-between mb-20">
    <div>
        <h1 class="page-title">Reports & Exports</h1>
        <p class="page-subtitle">Export attendance, devotional, and program data</p>
    </div>
    <span class="badge {{ match($plan){ 'premium'=>'badge-green','standard'=>'badge-gold','basic'=>'badge-purple',default=>'badge-blue' } }}">
        {{ strtoupper($plan) }} PLAN
    </span>
</div>

{{-- Gate notice --}}
@if(!$hasReports)
<div class="alert alert-warning">
    @include('partials.icon',['name'=>'shield','size'=>20])
    <div style="flex:1;">
        <div style="font-weight:600;font-size:15px;margin-bottom:4px;">Reports require Standard or Premium plan</div>
        <div style="font-size:13px;">Upgrade to unlock attendance exports, devotional reports, and program completion summaries.</div>
    </div>
    <a href="{{ route('settings.church') }}#subscription" class="btn btn-primary btn-sm" style="white-space:nowrap;">
        Upgrade Plan →
    </a>
</div>
@endif

{{-- Report cards --}}
<div class="grid-2" style="{{ !$hasReports ? 'opacity:.4;pointer-events:none;' : '' }}">

    {{-- Attendance Export --}}
    <div class="card">
        <div class="card-body">
            <div class="flex gap-16 mb-16">
                <div style="width:48px;height:48px;border-radius:12px;background:rgba(63,185,80,.12);border:1px solid rgba(63,185,80,.25);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    @include('partials.icon',['name'=>'calendar','size'=>22,'color'=>'var(--green)'])
                </div>
                <div>
                    <div style="font-weight:600;font-size:15px;margin-bottom:4px;">Attendance Export</div>
                    <div style="font-size:13px;color:var(--text-muted);">Export attendance records for all programs with filters by date range, program, and leader.</div>
                </div>
            </div>
            <form method="GET" action="{{ route('reports.attendance') }}" class="flex-col" style="display:flex;gap:10px;">
                <div class="form-grid form-grid-2">
                    <div>
                        <label class="field-label">From</label>
                        <input type="date" name="date_from" value="{{ now()->startOfMonth()->toDateString() }}">
                    </div>
                    <div>
                        <label class="field-label">To</label>
                        <input type="date" name="date_to" value="{{ now()->toDateString() }}">
                    </div>
                </div>
                <div>
                    <label class="field-label">Program</label>
                    <select name="program_id">
                        <option value="">All Programs</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-8">
                    <button type="submit" name="format" value="csv" class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;">
                        @include('partials.icon',['name'=>'download','size'=>13])
                        Export CSV
                    </button>
                    <button type="submit" name="format" value="xlsx" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center;">
                        Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Devotional Export --}}
    <div class="card">
        <div class="card-body">
            <div class="flex gap-16 mb-16">
                <div style="width:48px;height:48px;border-radius:12px;background:rgba(188,140,255,.12);border:1px solid rgba(188,140,255,.25);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    @include('partials.icon',['name'=>'heart','size'=>22,'color'=>'var(--purple)'])
                </div>
                <div>
                    <div style="font-weight:600;font-size:15px;margin-bottom:4px;">Devotional Export</div>
                    <div style="font-size:13px;color:var(--text-muted);">Export daily devotional completion records including streaks and overall compliance rates.</div>
                </div>
            </div>
            <form method="GET" action="{{ route('reports.devotionals') }}" style="display:flex;flex-direction:column;gap:10px;">
                <div class="form-grid form-grid-2">
                    <div>
                        <label class="field-label">From</label>
                        <input type="date" name="date_from" value="{{ now()->startOfMonth()->toDateString() }}">
                    </div>
                    <div>
                        <label class="field-label">To</label>
                        <input type="date" name="date_to" value="{{ now()->toDateString() }}">
                    </div>
                </div>
                <div>
                    <label class="field-label">Leader (optional)</label>
                    <select name="leader_id">
                        <option value="">All Leaders</option>
                        @foreach($leaders as $l)
                            <option value="{{ $l->id }}">{{ $l->first_name }} {{ $l->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-8">
                    <button type="submit" name="format" value="csv" class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;border-color:rgba(188,140,255,.4);color:var(--purple);">
                        @include('partials.icon',['name'=>'download','size'=>13,'color'=>'var(--purple)'])
                        Export CSV
                    </button>
                    <button type="submit" name="format" value="xlsx" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center;">
                        Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Program Completion --}}
    <div class="card">
        <div class="card-body">
            <div class="flex gap-16 mb-16">
                <div style="width:48px;height:48px;border-radius:12px;background:rgba(88,166,255,.12);border:1px solid rgba(88,166,255,.25);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    @include('partials.icon',['name'=>'book','size'=>22,'color'=>'var(--blue)'])
                </div>
                <div>
                    <div style="font-weight:600;font-size:15px;margin-bottom:4px;">Program Completion</div>
                    <div style="font-size:13px;color:var(--text-muted);">Summary report of lesson completion and disciple progress across all programs.</div>
                </div>
            </div>
            <form method="GET" action="{{ route('reports.programs') }}" style="display:flex;flex-direction:column;gap:10px;">
                <div>
                    <label class="field-label">Program</label>
                    <select name="program_id">
                        <option value="">All Programs</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-8">
                    <button type="submit" name="format" value="csv" class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;border-color:rgba(88,166,255,.4);color:var(--blue);">
                        @include('partials.icon',['name'=>'download','size'=>13,'color'=>'var(--blue)'])
                        Export CSV
                    </button>
                    <button type="submit" name="format" value="xlsx" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center;">
                        Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Historical Report (Premium only) --}}
    <div class="card" style="{{ !$hasBulk ? 'position:relative;overflow:hidden;' : '' }}">
        @if(!$hasBulk)
        <div style="position:absolute;inset:0;background:rgba(13,17,23,.6);backdrop-filter:blur(2px);z-index:2;display:flex;align-items:center;justify-content:center;border-radius:14px;">
            <div style="text-align:center;">
                <span class="badge badge-gold" style="margin-bottom:10px;">PREMIUM ONLY</span>
                <div style="font-size:13px;color:var(--text-muted);">Upgrade to access historical reports</div>
                <a href="{{ route('settings.church') }}#subscription" class="btn btn-primary btn-sm" style="margin-top:12px;">Upgrade →</a>
            </div>
        </div>
        @endif
        <div class="card-body">
            <div class="flex gap-16 mb-16">
                <div style="width:48px;height:48px;border-radius:12px;background:rgba(212,168,83,.12);border:1px solid rgba(212,168,83,.25);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    @include('partials.icon',['name'=>'chart','size'=>22,'color'=>'var(--gold)'])
                </div>
                <div>
                    <div style="font-weight:600;font-size:15px;margin-bottom:4px;">
                        Historical Report
                        <span class="badge badge-gold" style="margin-left:6px;font-size:9px;">PREMIUM</span>
                    </div>
                    <div style="font-size:13px;color:var(--text-muted);">Full historical data per program including year-over-year comparisons and trend analysis.</div>
                </div>
            </div>
            <form method="GET" action="{{ route('reports.historical') }}" style="display:flex;flex-direction:column;gap:10px;">
                <div class="form-grid form-grid-2">
                    <div>
                        <label class="field-label">Year</label>
                        <select name="year">
                            @for($y = now()->year; $y >= now()->year - 3; $y--)
                                <option value="{{ $y }}" {{ now()->year == $y ? 'selected':'' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="field-label">Program</label>
                        <select name="program_id">
                            <option value="">All Programs</option>
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-8">
                    <button type="submit" name="format" value="csv" class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;">
                        @include('partials.icon',['name'=>'download','size'=>13])
                        Export CSV
                    </button>
                    <button type="submit" name="format" value="xlsx" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center;">
                        Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
