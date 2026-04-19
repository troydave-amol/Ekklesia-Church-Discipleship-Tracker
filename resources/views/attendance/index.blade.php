{{-- resources/views/attendance/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Attendance')

@section('content')

<div class="flex items-center justify-between mb-20">
    <div>
        <h1 class="page-title">Attendance Tracking</h1>
        <p class="page-subtitle">Record and monitor lesson attendance</p>
    </div>
</div>

{{-- Filter bar --}}
<div class="card mb-20">
    <div class="card-body">
        <form method="GET" action="{{ route('attendance.index') }}">
            <div class="form-grid form-grid-3" style="align-items:end;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="field-label">Program</label>
                    <select name="program_id" id="programSelect" onchange="this.form.submit()">
                        <option value="">Select Program…</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="field-label">Lesson</label>
                    <select name="lesson_id">
                        <option value="">Select Lesson…</option>
                        @foreach($lessons as $l)
                            <option value="{{ $l->id }}" {{ request('lesson_id') == $l->id ? 'selected' : '' }}>
                                Week {{ $l->week_number }} — {{ $l->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="field-label">Date</label>
                    <input type="date" name="date" value="{{ request('date', today()->toDateString()) }}">
                </div>
            </div>
            <div style="margin-top:14px;">
                <button type="submit" class="btn btn-primary">
                    @include('partials.icon',['name'=>'search','size'=>14])
                    Load Attendance
                </button>
            </div>
        </form>
    </div>
</div>

@if(request('lesson_id') && isset($attendanceDisciples))

{{-- Summary Bar --}}
@php
    $total   = $attendanceDisciples->count();
    $present = $attendanceDisciples->where('is_present', true)->count();
    $absent  = $total - $present;
    $rate    = $total > 0 ? round(($present/$total)*100) : 0;
@endphp
<div class="grid-4 mb-20">
    <div class="stat-card" style="--accent-color:var(--blue)">
        <div class="stat-label">Total Disciples</div>
        <div class="stat-value">{{ $total }}</div>
    </div>
    <div class="stat-card" style="--accent-color:var(--green)">
        <div class="stat-label">Present</div>
        <div class="stat-value" style="color:var(--green)">{{ $present }}</div>
    </div>
    <div class="stat-card" style="--accent-color:var(--red)">
        <div class="stat-label">Absent</div>
        <div class="stat-value" style="color:var(--red)">{{ $absent }}</div>
    </div>
    <div class="stat-card" style="--accent-color:var(--gold)">
        <div class="stat-label">Rate</div>
        <div class="stat-value">{{ $rate }}%</div>
    </div>
</div>

{{-- Attendance form --}}
<form method="POST" action="{{ route('attendance.store') }}">
    @csrf
    <input type="hidden" name="lesson_id" value="{{ request('lesson_id') }}">
    <input type="hidden" name="date" value="{{ request('date', today()->toDateString()) }}">

    <div class="card">
        <div class="card-header">
            <div>
                <span class="section-title">{{ $selectedLesson->title ?? 'Lesson' }}</span>
                <span style="font-size:13px;color:var(--text-muted);margin-left:10px;">
                    Week {{ $selectedLesson->week_number ?? '' }} &nbsp;·&nbsp;
                    {{ \Carbon\Carbon::parse(request('date'))->format('F d, Y') }}
                </span>
            </div>
            <div class="flex gap-8">
                <button type="button" class="btn btn-ghost btn-sm" onclick="markAll(true)">
                    @include('partials.icon',['name'=>'check','size'=>13])
                    Mark All Present
                </button>
                <button type="button" class="btn btn-ghost btn-sm" onclick="markAll(false)">
                    @include('partials.icon',['name'=>'x','size'=>13])
                    Clear All
                </button>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:60px;">Present</th>
                        <th>Name</th>
                        <th>Leader</th>
                        <th>Program Level</th>
                        <th>Last Session</th>
                        <th>Overall Rate</th>
                    </tr>
                </thead>
                <tbody id="attendanceTable">
                    @foreach($attendanceDisciples as $d)
                    <tr id="row-{{ $d->id }}">
                        <td>
                            <label style="display:flex;align-items:center;justify-content:center;cursor:pointer;">
                                <input type="hidden" name="attendance[{{ $d->id }}]" value="0">
                                <input type="checkbox"
                                       name="attendance[{{ $d->id }}]"
                                       value="1"
                                       class="att-check"
                                       data-row="{{ $d->id }}"
                                       {{ $d->is_present ? 'checked' : '' }}
                                       onchange="styleRow(this)"
                                       style="width:auto;display:none;">
                                <div class="check-box {{ $d->is_present ? 'checked' : '' }}" style="width:26px;height:26px;border-radius:7px;border:2px solid {{ $d->is_present ? 'var(--green)' : 'var(--border)' }};background:{{ $d->is_present ? 'rgba(63,185,80,.15)' : 'transparent' }};display:flex;align-items:center;justify-content:center;transition:all .15s;">
                                    @if($d->is_present)
                                        @include('partials.icon',['name'=>'check','size'=>13,'color'=>'var(--green)'])
                                    @endif
                                </div>
                            </label>
                        </td>
                        <td>
                            <div class="flex items-center gap-12">
                                <div class="avatar avatar-sm" style="background:rgba(212,168,83,.12);border:1.5px solid rgba(212,168,83,.25);color:var(--gold);">
                                    {{ strtoupper(substr($d->first_name,0,1).substr($d->last_name,0,1)) }}
                                </div>
                                <span style="font-weight:500;">{{ $d->first_name }} {{ $d->last_name }}</span>
                            </div>
                        </td>
                        <td><span style="font-size:13px;color:var(--text-muted);">{{ $d->membership->leader->first_name ?? '—' }}</span></td>
                        <td>
                            <span class="badge badge-blue" style="font-size:10px;">
                                {{ strtoupper(str_replace('_',' ',$d->membership->destiny_training_level?->value ?? '')) }}
                            </span>
                        </td>
                        <td><span class="mono" style="font-size:12px;color:var(--text-muted);">{{ $d->last_attendance_date ? \Carbon\Carbon::parse($d->last_attendance_date)->format('M d') : '—' }}</span></td>
                        <td>
                            @php $att=$d->attendance_rate??0; $ac=$att>=80?'var(--green)':($att>=60?'var(--orange)':'var(--red)'); @endphp
                            <div class="flex items-center gap-8">
                                <div class="progress" style="width:80px;"><div class="progress-bar" style="width:{{ $att }}%;background:{{ $ac }};"></div></div>
                                <span class="mono" style="font-size:12px;color:{{ $ac }};">{{ $att }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer flex justify-between items-center">
            <div style="font-size:13px;color:var(--text-muted);">
                <span id="presentCount" style="color:var(--green);font-weight:600;">{{ $present }}</span> present ·
                <span id="absentCount" style="color:var(--red);font-weight:600;">{{ $absent }}</span> absent
            </div>
            <button type="submit" class="btn btn-primary">
                @include('partials.icon',['name'=>'check','size'=>14])
                Save Attendance
            </button>
        </div>
    </div>
</form>

@else

{{-- Empty state --}}
<div class="card">
    <div style="padding:64px;text-align:center;color:var(--text-muted);">
        @include('partials.icon',['name'=>'calendar','size'=>40,'color'=>'var(--border)'])
        <div style="font-size:16px;margin-top:16px;margin-bottom:8px;color:var(--text);">Select a program and lesson to begin</div>
        <div style="font-size:13px;">Choose the program, lesson, and date above to load the attendance sheet.</div>
    </div>
</div>

@endif

{{-- Recent Attendance History --}}
<div class="card mt-16">
    <div class="card-header">
        <span class="section-title">Recent Attendance Records</span>
        @can('export-reports')
        <a href="{{ route('reports.attendance') }}" class="btn btn-ghost btn-sm">
            @include('partials.icon',['name'=>'download','size'=>13])
            Export
        </a>
        @endcan
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Date</th><th>Lesson</th><th>Program</th><th>Present</th><th>Absent</th><th>Rate</th></tr>
            </thead>
            <tbody>
                @forelse($recentAttendance ?? [] as $record)
                <tr>
                    <td><span class="mono" style="font-size:13px;">{{ $record->date->format('M d, Y') }}</span></td>
                    <td>{{ $record->lesson->title }}</td>
                    <td><span class="badge badge-blue">{{ $record->lesson->program->name }}</span></td>
                    <td style="color:var(--green);">{{ $record->present_count }}</td>
                    <td style="color:var(--red);">{{ $record->absent_count }}</td>
                    <td>
                        @php $r=round($record->present_count/max($record->total_count,1)*100); @endphp
                        <span class="mono" style="color:{{ $r>=80?'var(--green)':($r>=60?'var(--orange)':'var(--red)') }};">{{ $r }}%</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text-muted);">No records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Toggle checkbox + visual
document.querySelectorAll('.att-check').forEach(cb => {
    cb.closest('label').addEventListener('click', () => {
        cb.checked = !cb.checked;
        styleRow(cb);
    });
});

function styleRow(cb) {
    const box = cb.closest('label').querySelector('.check-box');
    if (cb.checked) {
        box.style.borderColor = 'var(--green)';
        box.style.background  = 'rgba(63,185,80,.15)';
        box.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>`;
    } else {
        box.style.borderColor = 'var(--border)';
        box.style.background  = 'transparent';
        box.innerHTML = '';
    }
    updateCounts();
}

function markAll(checked) {
    document.querySelectorAll('.att-check').forEach(cb => {
        cb.checked = checked;
        styleRow(cb);
    });
}

function updateCounts() {
    const all     = document.querySelectorAll('.att-check').length;
    const present = document.querySelectorAll('.att-check:checked').length;
    document.getElementById('presentCount').textContent = present;
    document.getElementById('absentCount').textContent  = all - present;
}
</script>
@endpush
