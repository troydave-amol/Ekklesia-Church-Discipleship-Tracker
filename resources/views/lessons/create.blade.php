@extends('layouts.app')
@section('title', 'New lesson')

@section('content')
<div class="mb-20">
    <a href="{{ route('lessons.index') }}" class="btn btn-ghost btn-sm">← Lessons</a>
</div>
<h1 class="page-title">Upload lesson</h1>

<div class="card mt-20" style="max-width:640px;">
    <div class="card-body">
        <form method="POST" action="{{ route('lessons.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Program</label>
                <select name="program_id" class="form-control" required>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Week number</label>
                <input type="number" name="week_number" class="form-control" min="1" max="52" value="1" required>
            </div>
            <div class="form-group">
                <label class="form-label">Title</label>
                <input name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Content</label>
                <textarea name="content" class="form-control" rows="6"></textarea>
            </div>
            @if ($errors->any())
                <div class="alert alert-warning">{{ $errors->first() }}</div>
            @endif
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
@endsection
