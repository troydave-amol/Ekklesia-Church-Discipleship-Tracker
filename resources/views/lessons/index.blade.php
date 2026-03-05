{{-- resources/views/lessons/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Lessons')

@section('content')

<div class="flex items-center justify-between mb-20">
    <div>
        <h1 class="page-title">Lesson Management</h1>
        <p class="page-subtitle">Upload and manage weekly lessons per program</p>
    </div>
    @can('manage-lessons')
    <a href="{{ route('lessons.create') }}" class="btn btn-primary">
        @include('partials.icon',['name'=>'upload','size'=>14])
        Upload New Lesson
    </a>
    @endcan
</div>

{{-- Program filter pills --}}
<div class="flex gap-8 mb-20" style="flex-wrap:wrap;">
    <a href="{{ route('lessons.index') }}"
       class="btn {{ !request('program_id') ? 'btn-primary' : 'btn-ghost' }} btn-sm"
       style="{{ !request('program_id') ? '' : 'border-radius:20px;' }}">
        All Programs
    </a>
    @foreach($programs as $p)
    @php $pColors=['Life Class'=>'var(--blue)','SOL 1'=>'var(--purple)','SOL 2'=>'var(--orange)','SOL 3'=>'var(--green)']; $pc=$pColors[$p->name]??'var(--gold)'; @endphp
    <a href="{{ route('lessons.index', ['program_id' => $p->id]) }}"
       style="padding:6px 16px;border-radius:20px;border:1px solid {{ request('program_id')==$p->id ? $pc : 'var(--border)' }};background:{{ request('program_id')==$p->id ? $pc.'22' : 'transparent' }};color:{{ request('program_id')==$p->id ? $pc : 'var(--text-muted)' }};font-size:13px;transition:all .2s;">
        {{ $p->name }}
    </a>
    @endforeach
</div>

{{-- Lesson cards grid --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;">
    @forelse($lessons as $lesson)
    @php
        $pColors=['Life Class'=>'var(--blue)','SOL 1'=>'var(--purple)','SOL 2'=>'var(--orange)','SOL 3'=>'var(--green)'];
        $lc=$pColors[$lesson->program->name??'']??'var(--gold)';
    @endphp
    <div class="card" style="display:flex;flex-direction:column;">
        <div class="card-body" style="flex:1;">
            <div class="flex justify-between items-center mb-12">
                <span class="badge badge-blue">{{ $lesson->program->name ?? 'Unknown' }}</span>
                <span class="mono" style="font-size:11px;color:var(--text-dim);">WEEK {{ $lesson->week_number }}</span>
            </div>
            <div style="font-weight:600;font-size:16px;margin-bottom:8px;line-height:1.3;">{{ $lesson->title }}</div>
            <div style="font-size:13px;color:var(--text-muted);line-height:1.6;flex:1;">
                {{ Str::limit(strip_tags($lesson->content), 100) }}
            </div>
        </div>
        <div style="padding:12px 20px;border-top:1px solid var(--border-light);display:flex;gap:8px;">
            <a href="{{ route('lessons.show', $lesson) }}" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center;">
                View
            </a>
            @can('manage-lessons')
            <a href="{{ route('lessons.edit', $lesson) }}" class="btn btn-ghost btn-icon btn-sm">
                @include('partials.icon',['name'=>'edit','size'=>13])
            </a>
            <form method="POST" action="{{ route('lessons.destroy', $lesson) }}" onsubmit="return confirm('Delete this lesson?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-icon btn-sm">
                    @include('partials.icon',['name'=>'trash','size'=>13,'color'=>'var(--red)'])
                </button>
            </form>
            @endcan
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1;" class="card">
        <div style="padding:64px;text-align:center;color:var(--text-muted);">
            @include('partials.icon',['name'=>'book','size'=>40,'color'=>'var(--border)'])
            <div style="font-size:16px;margin-top:16px;margin-bottom:8px;color:var(--text);">No lessons yet</div>
            @can('manage-lessons')
            <a href="{{ route('lessons.create') }}" class="btn btn-primary" style="margin-top:8px;">Upload First Lesson</a>
            @endcan
        </div>
    </div>
    @endforelse
</div>

@if($lessons->hasPages())
<div class="flex justify-between items-center mt-20">
    <span class="text-muted" style="font-size:13px;">{{ $lessons->firstItem() }}–{{ $lessons->lastItem() }} of {{ $lessons->total() }} lessons</span>
    {{ $lessons->withQueryString()->links('partials.pagination') }}
</div>
@endif

@endsection
