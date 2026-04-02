@extends('layouts.app')
@section('title', 'Edit lesson')

@section('content')
<div class="mb-20">
    <a href="{{ route('lessons.index') }}" class="btn btn-ghost btn-sm">← Lessons</a>
</div>
<h1 class="page-title">Edit lesson</h1>

<div class="card mt-20" style="max-width:640px;">
    <div class="card-body">
        <form method="POST" action="{{ route('lessons.update', $lesson) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Program</label>
                <select name="program_id" class="form-control" required>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" {{ (int)$lesson->program_id === (int)$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Week number</label>
                <input type="number" name="week_number" class="form-control" min="1" max="52" value="{{ old('week_number', $lesson->week_number) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Title</label>
                <input name="title" class="form-control" value="{{ old('title', $lesson->title) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Content</label>
                <textarea name="content" class="form-control" rows="6">{{ old('content', $lesson->content) }}</textarea>
            </div>
            @if ($errors->any())
                <div class="alert alert-warning">{{ $errors->first() }}</div>
            @endif
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
@endsection
