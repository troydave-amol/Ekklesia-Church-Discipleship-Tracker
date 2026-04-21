@extends('layouts.central-admin')

@section('title', 'Edit Announcement')
@section('topbar-title', 'Edit Announcement')

@section('content')
<div class="ca-page-head">
    <h1 class="ca-page-title">Edit Announcement</h1>
    <p class="ca-page-desc">Update the announcement details.</p>
</div>

<div class="ca-form-card">
    <form action="{{ route('admin.announcements.update', $announcement) }}" method="post" class="ca-form">
        @csrf
        @method('PUT')

        <div class="ca-form-group">
            <label class="ca-label">Title</label>
            <input type="text" name="title" class="ca-input" value="{{ old('title', $announcement->title) }}" required placeholder="Announcement title">
            @error('title')
                <span class="ca-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="ca-form-group">
            <label class="ca-label">Message</label>
            <textarea name="body" class="ca-textarea" rows="8" required placeholder="Enter announcement message...">{{ old('body', $announcement->body) }}</textarea>
            <small class="ca-help">Markdown supported. Min 10 characters.</small>
            @error('body')
                <span class="ca-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="ca-form-group">
            <label class="ca-label">Audience</label>
            <select name="audience" class="ca-select" required>
                <option value="all" {{ old('audience', $announcement->audience) === 'all' ? 'selected' : '' }}>All Users</option>
                <option value="admins" {{ old('audience', $announcement->audience) === 'admins' ? 'selected' : '' }}>Admins Only</option>
                <option value="tenants" {{ old('audience', $announcement->audience) === 'tenants' ? 'selected' : '' }}>Tenants Only</option>
            </select>
            @error('audience')
                <span class="ca-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="ca-info-box">
            <strong>Status:</strong>
            @if($announcement->published_at)
                Published on {{ $announcement->published_at->format('M j, Y H:i') }}
            @else
                Draft
            @endif
        </div>

        <div class="ca-form-actions">
            <a href="{{ route('admin.announcements.index') }}" class="ca-btn ca-btn-ghost">Cancel</a>
            <button type="submit" class="ca-btn ca-btn-primary">Update Announcement</button>
        </div>
    </form>
</div>

<style>
.ca-form-card { background: white; border-radius: 8px; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 600px; }
.ca-form-group { margin-bottom: 1.5rem; }
.ca-label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: #1f2937; font-size: 14px; }
.ca-input, .ca-textarea, .ca-select { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; font-family: inherit; }
.ca-input:focus, .ca-textarea:focus, .ca-select:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
.ca-textarea { resize: vertical; }
.ca-error { color: #dc2626; font-size: 12px; display: block; margin-top: 0.25rem; }
.ca-help { color: #6b7280; font-size: 12px; display: block; margin-top: 0.25rem; }
.ca-info-box { background: #f0f9ff; border-left: 3px solid #2563eb; padding: 1rem; border-radius: 4px; font-size: 13px; color: #1e40af; margin-bottom: 1.5rem; }
.ca-form-actions { display: flex; gap: 1rem; margin-top: 2rem; }
.ca-btn { padding: 0.75rem 1.5rem; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
.ca-btn-primary { background: #2563eb; color: white; }
.ca-btn-primary:hover { background: #1d4ed8; }
.ca-btn-ghost { background: #f3f4f6; color: #374151; }
.ca-btn-ghost:hover { background: #e5e7eb; }
</style>
@endsection
