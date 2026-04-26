@extends('layouts.app')
@section('title', $lesson->title)

@section('content')
<div class="mb-20 flex gap-8">
    <a href="{{ route('lessons.index') }}" class="btn btn-ghost btn-sm">← Lessons</a>
    @can('lessons.manage')
    <a href="{{ route('lessons.edit', $lesson) }}" class="btn btn-ghost btn-sm">Edit</a>
    @endcan
</div>

<div class="card">
    <div class="card-body">
        <span class="badge badge-blue">{{ $lesson->program->name ?? 'Program' }}</span>
        <span class="mono text-muted" style="font-size:12px;margin-left:8px;">Week {{ $lesson->week_number }}</span>
        <h1 class="page-title mt-12">{{ $lesson->title }}</h1>
        <div class="page-subtitle mt-8" style="line-height:1.7;">{!! nl2br(e($lesson->content ?? '')) !!}</div>
    </div>
</div>
@endsection
