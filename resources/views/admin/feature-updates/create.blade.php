@extends('layouts.central-admin')

@section('title', 'Create Feature Update')
@section('topbar-title', 'Create Feature Update')

@section('content')
<div class="ca-page-head" style="margin-bottom:24px;">
    <h1 class="ca-page-title">Create Feature Update</h1>
    <p class="ca-page-desc">Define a new feature package. The system will scaffold the migration, model, factory, seeder, and controller automatically.</p>
</div>

<div class="ca-card" style="max-width:640px;">
    <div class="ca-card-head" style="padding:14px 20px;">
        <span class="ca-card-title">Feature Details</span>
    </div>
    <div style="padding:20px;">
        @if($errors->any())
            <div style="background:#fdeaea;border:1px solid #e6c3c3;border-radius:8px;padding:12px 16px;margin-bottom:16px;">
                <ul style="margin:0;padding-left:18px;font-size:13px;color:#c0392b;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.feature-updates.store') }}" method="POST">
            @csrf

            {{-- Name --}}
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--ca-ink-3);margin-bottom:6px;">Feature Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Prayer Requests Module"
                       class="ca-input" style="width:100%;" required>
            </div>

            {{-- Slug --}}
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--ca-ink-3);margin-bottom:6px;">Slug *</label>
                <input type="text" name="slug" value="{{ old('slug') }}" placeholder="e.g. prayer-requests"
                       class="ca-input" style="width:100%;" required>
                <div style="font-size:11px;color:var(--ca-ink-4);margin-top:4px;">Lowercase, hyphenated. Used for table and class name generation.</div>
            </div>

            {{-- Version --}}
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--ca-ink-3);margin-bottom:6px;">Version *</label>
                <input type="text" name="version" value="{{ old('version', '1.0.0') }}" placeholder="e.g. 1.0.0"
                       class="ca-input" style="width:100%;" required>
            </div>

            {{-- Type --}}
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--ca-ink-3);margin-bottom:6px;">Type *</label>
                <select name="type" class="ca-input" style="width:100%;" required>
                    <option value="feature" {{ old('type') === 'feature' ? 'selected' : '' }}>Feature</option>
                    <option value="patch" {{ old('type') === 'patch' ? 'selected' : '' }}>Patch</option>
                    <option value="data" {{ old('type') === 'data' ? 'selected' : '' }}>Data</option>
                </select>
            </div>

            {{-- Description --}}
            <div style="margin-bottom:24px;">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--ca-ink-3);margin-bottom:6px;">Description</label>
                <textarea name="description" rows="4" placeholder="Describe what this feature does..."
                          class="ca-input" style="width:100%;resize:vertical;">{{ old('description') }}</textarea>
            </div>

            <div style="display:flex;gap:12px;">
                <button type="submit" class="ca-btn ca-btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0022 16z"/></svg>
                    Scaffold Feature
                </button>
                <a href="{{ route('admin.feature-updates.index') }}" class="ca-btn" style="background:var(--ca-surface-dim);color:var(--ca-ink-3);">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nameInput = document.querySelector('input[name="name"]');
        const slugInput = document.querySelector('input[name="slug"]');
        if (nameInput && slugInput) {
            nameInput.addEventListener('input', () => {
                slugInput.value = nameInput.value
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
            });
        }
    });
</script>
@endpush
@endsection
