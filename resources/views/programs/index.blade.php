{{-- resources/views/programs/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Programs')

@section('content')

<div class="flex items-center justify-between mb-20">
    <div>
        <h1 class="page-title">Destiny Training Programs</h1>
        <p class="page-subtitle">Manage your church's discipleship curriculum</p>
    </div>
    @can('manage-programs')
    <button class="btn btn-primary" onclick="document.getElementById('createProgramModal').style.display='flex'">
        @include('partials.icon',['name'=>'plus','size'=>14])
        Create Program
    </button>
    @endcan
</div>

@php
    $programColors = [
        'Life Class' => ['color'=>'var(--blue)',   'dim'=>'rgba(88,166,255,.15)',   'border'=>'rgba(88,166,255,.3)'],
        'SOL 1'      => ['color'=>'var(--purple)', 'dim'=>'rgba(188,140,255,.15)',  'border'=>'rgba(188,140,255,.3)'],
        'SOL 2'      => ['color'=>'var(--orange)', 'dim'=>'rgba(255,123,84,.15)',   'border'=>'rgba(255,123,84,.3)'],
        'SOL 3'      => ['color'=>'var(--green)',  'dim'=>'rgba(63,185,80,.15)',    'border'=>'rgba(63,185,80,.3)'],
    ];
@endphp

<div class="grid-2">
    @forelse($programs as $program)
    @php
        $pc = $programColors[$program->name] ?? ['color'=>'var(--gold)','dim'=>'rgba(212,168,83,.1)','border'=>'rgba(212,168,83,.25)'];
    @endphp
    <div class="card" style="border-top:3px solid {{ $pc['color'] }};">
        <div class="card-body">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-16">
                <div>
                    <div style="font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:{{ $pc['color'] }};">{{ $program->name }}</div>
                    <div style="font-size:13px;color:var(--text-muted);margin-top:3px;">{{ $program->description }}</div>
                </div>
                <div class="flex items-center gap-8">
                    <label class="toggle-wrap">
                        <div class="toggle-switch">
                            <form method="POST" action="{{ route('programs.toggle', $program) }}">
                                @csrf @method('PATCH')
                                <input type="checkbox" {{ $program->is_active ? 'checked' : '' }} onchange="this.form.submit()">
                                <span class="toggle-slider"></span>
                            </form>
                        </div>
                        <span style="font-size:12px;color:{{ $program->is_active ? 'var(--green)' : 'var(--text-muted)' }};">{{ $program->is_active ? 'Active' : 'Inactive' }}</span>
                    </label>
                </div>
            </div>

            {{-- Stats row --}}
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:16px;">
                <div style="background:{{ $pc['dim'] }};border:1px solid {{ $pc['border'] }};border-radius:10px;padding:12px 14px;">
                    <div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:4px;">Disciples</div>
                    <div class="mono" style="font-size:22px;font-weight:700;color:{{ $pc['color'] }};">{{ $program->disciples_count }}</div>
                </div>
                <div style="background:var(--bg);border:1px solid var(--border-light);border-radius:10px;padding:12px 14px;">
                    <div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:4px;">Lessons</div>
                    <div class="mono" style="font-size:22px;font-weight:700;">{{ $program->lessons_count }}</div>
                </div>
                <div style="background:var(--bg);border:1px solid var(--border-light);border-radius:10px;padding:12px 14px;">
                    <div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:4px;">Completion</div>
                    <div class="mono" style="font-size:22px;font-weight:700;">{{ $program->completion_rate ?? 0 }}%</div>
                </div>
            </div>

            {{-- Progress --}}
            <div style="margin-bottom:16px;">
                <div class="flex justify-between" style="font-size:12px;margin-bottom:6px;">
                    <span class="text-muted">Overall Progress</span>
                    <span class="mono" style="color:{{ $pc['color'] }};">{{ $program->completion_rate ?? 0 }}%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar" style="width:{{ $program->completion_rate ?? 0 }}%;background:{{ $pc['color'] }};"></div>
                </div>
            </div>

            {{-- Attendance & Devotional mini stats --}}
            <div class="flex gap-12 mb-16" style="font-size:13px;">
                <div style="flex:1;background:var(--bg);border-radius:8px;padding:8px 12px;border:1px solid var(--border-light);">
                    <div style="color:var(--text-muted);font-size:11px;margin-bottom:2px;">AVG ATTENDANCE</div>
                    <div class="mono" style="color:var(--green);">{{ $program->avg_attendance ?? 0 }}%</div>
                </div>
                <div style="flex:1;background:var(--bg);border-radius:8px;padding:8px 12px;border:1px solid var(--border-light);">
                    <div style="color:var(--text-muted);font-size:11px;margin-bottom:2px;">DEVOTIONAL RATE</div>
                    <div class="mono" style="color:var(--purple);">{{ $program->devotional_rate ?? 0 }}%</div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-8">
                <a href="{{ route('lessons.index', ['program_id' => $program->id]) }}"
                   class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;border-color:{{ $pc['color'] }}66;color:{{ $pc['color'] }};">
                    @include('partials.icon',['name'=>'book','size'=>12,'color'=>$pc['color']])
                    Lessons
                </a>
                <a href="{{ route('users.index', ['program_id' => $program->id]) }}"
                   class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;border-color:{{ $pc['color'] }}66;color:{{ $pc['color'] }};">
                    @include('partials.icon',['name'=>'users','size'=>12,'color'=>$pc['color']])
                    Disciples
                </a>
                @can('manage-programs')
                <a href="{{ route('programs.edit', $program) }}" class="btn btn-ghost btn-icon btn-sm">
                    @include('partials.icon',['name'=>'edit','size'=>13])
                </a>
                @endcan
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1;" class="card">
        <div style="padding:64px;text-align:center;color:var(--text-muted);">
            @include('partials.icon',['name'=>'cross','size'=>40,'color'=>'var(--border)'])
            <div style="font-size:16px;margin-top:16px;margin-bottom:8px;color:var(--text);">No programs created yet</div>
            <p style="font-size:13px;margin-bottom:20px;">Create your first destiny training program to get started.</p>
            <button class="btn btn-primary" onclick="document.getElementById('createProgramModal').style.display='flex'">
                @include('partials.icon',['name'=>'plus','size'=>14])
                Create First Program
            </button>
        </div>
    </div>
    @endforelse
</div>

{{-- Create Program Modal --}}
<div id="createProgramModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:200;align-items:center;justify-content:center;" onclick="if(event.target===this)this.style.display='none'">
    <div class="card" style="width:480px;max-width:94vw;" onclick="event.stopPropagation()">
        <div class="card-header">
            <span class="section-title">Create Program</span>
            <button class="btn btn-ghost btn-icon btn-sm" onclick="document.getElementById('createProgramModal').style.display='none'">
                @include('partials.icon',['name'=>'x','size'=>14])
            </button>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('programs.store') }}">
                @csrf
                <div class="form-group">
                    <label class="field-label">Program Name</label>
                    <select name="name" required>
                        <option value="">Select program…</option>
                        <option value="Life Class">Life Class</option>
                        <option value="SOL 1">School of Leaders 1</option>
                        <option value="SOL 2">School of Leaders 2</option>
                        <option value="SOL 3">School of Leaders 3</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="field-label">Description</label>
                    <textarea name="description" rows="3" placeholder="Describe the purpose of this program…"></textarea>
                </div>
                <div class="flex gap-8">
                    <button type="button" class="btn btn-ghost" style="flex:1;" onclick="document.getElementById('createProgramModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="flex:2;">Create Program</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
