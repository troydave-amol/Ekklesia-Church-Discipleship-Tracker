@extends('layouts.app')
@section('title', 'My lessons')

@section('content')
<h1 class="page-title">My lessons</h1>
<p class="page-subtitle">Progress for your current program</p>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-top:20px;">
    @forelse($lessons as $lesson)
    <div class="card">
        <div class="card-body">
            <div class="text-muted" style="font-size:12px;">{{ $lesson->program->name ?? '' }} · Week {{ $lesson->week_number }}</div>
            <div style="font-weight:600;margin-top:8px;">{{ $lesson->title }}</div>
            <a href="{{ route('lessons.show', $lesson) }}" class="btn btn-ghost btn-sm mt-12">Open</a>
        </div>
    </div>
    @empty
    <p class="text-muted">No lessons for your level yet.</p>
    @endforelse
</div>
@endsection
