@extends('layouts.central-admin')

@section('title', 'Edit Announcement')
@section('topbar-title', 'Edit Announcement')

@section('content')
<a href="{{ route('admin.announcements.index') }}" class="ca-back">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    Back to Announcements
</a>

<div class="ca-page-head">
    <h1 class="ca-page-title">Edit Announcement</h1>
    <p class="ca-page-desc">Update the announcement details.</p>
</div>

<div class="ca-card">
    <div class="ca-card-head">
        <span class="ca-card-title">Announcement Details</span>
        @if($announcement->published_at)
            <span class="ca-pill ca-pill-ok"><span class="ca-pill-dot"></span> Published {{ $announcement->published_at->format('M j, Y H:i') }}</span>
        @else
            <span class="ca-pill ca-pill-pending"><span class="ca-pill-dot"></span> Draft</span>
        @endif
    </div>
    <div class="ca-card-body">
        <form action="{{ route('admin.announcements.update', $announcement) }}" method="post">
            @csrf @method('PUT')

            <div class="ca-form-group">
                <label class="ca-form-label">Title</label>
                <input type="text" name="title" class="ca-form-input" value="{{ old('title', $announcement->title) }}" required placeholder="Announcement title">
                @error('title')
                    <span class="ca-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="ca-form-group">
                <label class="ca-form-label">Message</label>
                <textarea name="body" class="ca-form-input ca-form-textarea" rows="6" required placeholder="Enter announcement message...">{{ old('body', $announcement->body) }}</textarea>
                <span class="ca-form-hint">Markdown supported. Min 10 characters.</span>
                @error('body')
                    <span class="ca-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="ca-form-group">
                <label class="ca-form-label">Audience</label>
                <select name="audience" class="ca-form-input ca-form-select" required>
                    <option value="all" {{ old('audience', $announcement->audience) === 'all' ? 'selected' : '' }}>All Users</option>
                    <option value="admins" {{ old('audience', $announcement->audience) === 'admins' ? 'selected' : '' }}>Admins Only</option>
                    <option value="tenants" {{ old('audience', $announcement->audience) === 'tenants' ? 'selected' : '' }}>Tenants Only</option>
                </select>
                @error('audience')
                    <span class="ca-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="ca-form-actions">
                <a href="{{ route('admin.announcements.index') }}" class="ca-btn ca-btn-reject" style="border-color:var(--ca-border-strong);color:var(--ca-ink-3);">Cancel</a>
                <button type="submit" class="ca-btn ca-btn-approve">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Update Announcement
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Danger Zone --}}
<div class="ca-card" style="margin-top:18px;border-color:var(--ca-red-ring);">
    <div class="ca-card-head">
        <span class="ca-card-title" style="color:var(--ca-red);">Danger Zone</span>
    </div>
    <div class="ca-card-body" style="display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div style="font-size:13.5px;font-weight:500;color:var(--ca-ink);">Delete this announcement</div>
            <div style="font-size:12px;color:var(--ca-ink-4);margin-top:2px;">This action cannot be undone.</div>
        </div>
        <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?')">
            @csrf @method('DELETE')
            <button type="submit" class="ca-btn ca-btn-reject">Delete</button>
        </form>
    </div>
</div>
@endsection
