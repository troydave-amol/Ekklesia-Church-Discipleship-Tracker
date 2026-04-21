@extends('layouts.central-admin')

@section('title', 'New Announcement')
@section('topbar-title', 'New Announcement')

@section('content')
<div class="ca-page-head">
    <h1 class="ca-page-title">Create Announcement</h1>
    <p class="ca-page-desc">Compose and publish a new system-wide announcement.</p>
</div>

<div class="ca-form-card">
    <form action="{{ route('admin.announcements.store') }}" method="post" class="ca-form">
        @csrf

        <div class="ca-form-group">
            <label class="ca-label">Title</label>
            <input type="text" name="title" class="ca-input" value="{{ old('title') }}" required placeholder="Announcement title">
            @error('title')
                <span class="ca-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="ca-form-group">
            <label class="ca-label">Message</label>
            <textarea name="body" class="ca-textarea" rows="8" required placeholder="Enter announcement message...">{{ old('body') }}</textarea>
            <small class="ca-help">Markdown supported. Min 10 characters.</small>
            @error('body')
                <span class="ca-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="ca-form-group">
            <label class="ca-label">Audience</label>
            <select name="audience" class="ca-select" required>
                <option value="">Select audience...</option>
                <option value="all" {{ old('audience') === 'all' ? 'selected' : '' }}>All Users</option>
                <option value="admins" {{ old('audience') === 'admins' ? 'selected' : '' }}>Admins Only</option>
                <option value="tenants" {{ old('audience') === 'tenants' ? 'selected' : '' }}>Tenants Only</option>
            </select>
            @error('audience')
                <span class="ca-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="ca-form-group">
            <label class="ca-checkbox">
                <input type="checkbox" name="publish_now" value="1" {{ old('publish_now') ? 'checked' : '' }}>
                <span>Publish immediately</span>
            </label>
            <small class="ca-help">If unchecked, announcement will be saved as draft</small>
        </div>

        <div class="ca-form-actions">
            <a href="{{ route('admin.announcements.index') }}" class="ca-btn ca-btn-ghost">Cancel</a>
            <button type="submit" class="ca-btn ca-btn-primary">Create Announcement</button>
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
.ca-checkbox { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
.ca-checkbox input { cursor: pointer; }
.ca-form-actions { display: flex; gap: 1rem; margin-top: 2rem; }
.ca-btn { padding: 0.75rem 1.5rem; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
.ca-btn-primary { background: #2563eb; color: white; }
.ca-btn-primary:hover { background: #1d4ed8; }
.ca-btn-ghost { background: #f3f4f6; color: #374151; }
.ca-btn-ghost:hover { background: #e5e7eb; }
</style>
@endsection
