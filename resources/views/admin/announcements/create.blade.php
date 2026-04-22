@extends('layouts.central-admin')

@section('title', 'New Announcement')
@section('topbar-title', 'New Announcement')

@section('content')
<a href="{{ route('admin.announcements.index') }}" class="ca-back">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    Back to Announcements
</a>

<div class="ca-page-head">
    <h1 class="ca-page-title">Create Announcement</h1>
    <p class="ca-page-desc">Compose and publish a new system-wide announcement to tenants.</p>
</div>

<div class="ca-card">
    <div class="ca-card-head">
        <span class="ca-card-title">Announcement Details</span>
    </div>
    <div class="ca-card-body">
        <form action="{{ route('admin.announcements.store') }}" method="post">
            @csrf

            <div class="ca-form-group">
                <label class="ca-form-label">Title</label>
                <input type="text" name="title" class="ca-form-input" value="{{ old('title') }}" required placeholder="Announcement title">
                @error('title')
                    <span class="ca-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="ca-form-group">
                <label class="ca-form-label">Message</label>
                <textarea name="body" class="ca-form-input ca-form-textarea" rows="6" required placeholder="Enter announcement message...">{{ old('body') }}</textarea>
                <span class="ca-form-hint">Markdown supported. Min 10 characters.</span>
                @error('body')
                    <span class="ca-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="ca-form-group">
                <label class="ca-form-label">Audience</label>
                <select name="audience" class="ca-form-input ca-form-select" required>
                    <option value="">Select audience…</option>
                    <option value="all" {{ old('audience') === 'all' ? 'selected' : '' }}>All Users</option>
                    <option value="admins" {{ old('audience') === 'admins' ? 'selected' : '' }}>Admins Only</option>
                    <option value="tenants" {{ old('audience') === 'tenants' ? 'selected' : '' }}>Tenants Only</option>
                </select>
                @error('audience')
                    <span class="ca-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="ca-form-group">
                <label class="ca-form-check">
                    <input type="checkbox" name="publish_now" value="1" {{ old('publish_now') ? 'checked' : '' }}>
                    <span>Publish immediately</span>
                </label>
                <span class="ca-form-hint">If unchecked, announcement will be saved as draft.</span>
            </div>

            <div class="ca-form-actions">
                <a href="{{ route('admin.announcements.index') }}" class="ca-btn ca-btn-reject" style="border-color:var(--ca-border-strong);color:var(--ca-ink-3);">Cancel</a>
                <button type="submit" class="ca-btn ca-btn-approve">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Create Announcement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
