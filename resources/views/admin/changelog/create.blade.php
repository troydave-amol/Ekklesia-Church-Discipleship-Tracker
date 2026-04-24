@extends('layouts.central-admin')

@section('title', 'New Release')
@section('topbar-title', 'New Release')

@section('content')
<div class="ca-page-head" style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
    <a href="{{ route('admin.changelog.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--ca-ink-4);text-decoration:none;" class="ca-btn" style="padding:6px 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        All releases
    </a>
    <h1 class="ca-page-title">New Release</h1>
</div>

<div style="max-width:760px;">
    <form
        action="{{ route('admin.changelog.store') }}"
        method="POST"
        x-data="{
            isMajor: false,
            features: [''],
            bugFixes: [''],
            addFeature()  { this.features.push(''); },
            removeFeature(i) { this.features.splice(i, 1); if (!this.features.length) this.features = ['']; },
            addFix()      { this.bugFixes.push(''); },
            removeFix(i)  { this.bugFixes.splice(i, 1); if (!this.bugFixes.length) this.bugFixes = ['']; },
        }"
    >
        @csrf

        @if($errors->any())
            <div class="ca-flash ca-flash-err" style="margin-bottom:20px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                <ul style="margin:0;padding:0 0 0 16px;">
                    @foreach($errors->all() as $error)
                        <li style="font-size:13px;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ── Version & Title ──────────────────────────────────── --}}
        <div class="ca-card" style="margin-bottom:16px;">
            <div class="ca-card-head"><span class="ca-card-title">Release Info</span></div>
            <div class="ca-card-body" style="display:grid;gap:16px;">

                <div style="display:grid;grid-template-columns:200px 1fr;gap:16px;">
                    <div>
                        <label class="ca-label" for="release-version">Version Number <span style="color:var(--ca-red);">*</span></label>
                        <input id="release-version" type="text" name="version"
                               value="{{ old('version') }}"
                               placeholder="1.2.0"
                               required
                               style="font-family:'DM Mono',monospace;font-size:14px;"
                               class="{{ $errors->has('version') ? 'ca-is-invalid' : '' }}">
                        <div style="font-size:11.5px;color:var(--ca-ink-4);margin-top:4px;">Follow semver: major.minor.patch</div>
                        @error('version')<div class="ca-invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="ca-label" for="release-title">Release Title <span style="color:var(--ca-red);">*</span></label>
                        <input id="release-title" type="text" name="title"
                               value="{{ old('title') }}"
                               placeholder="Discipleship Tracker v1.2.0 — Leader Dashboard Update"
                               required
                               class="{{ $errors->has('title') ? 'ca-is-invalid' : '' }}">
                        @error('title')<div class="ca-invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Major toggle --}}
                <div>
                    <label class="ca-label">Release Type</label>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <label style="position:relative;width:40px;height:22px;cursor:pointer;flex-shrink:0;">
                            <input type="checkbox" name="is_major" value="1"
                                   x-model="isMajor"
                                   style="opacity:0;position:absolute;width:0;height:0;">
                            <span style="position:absolute;inset:0;border-radius:11px;transition:background .2s;"
                                  :style="isMajor ? 'background:var(--ca-amber)' : 'background:var(--ca-border)'">
                                <span style="position:absolute;width:16px;height:16px;background:#fff;border-radius:50%;top:3px;left:3px;transition:transform .2s;"
                                      :style="isMajor ? 'transform:translateX(18px)' : ''"></span>
                            </span>
                        </label>
                        <span style="font-size:13.5px;color:var(--ca-ink-2);">Major release</span>
                    </div>

                    <div x-show="isMajor" x-transition style="margin-top:10px;padding:10px 14px;border-radius:8px;background:var(--ca-amber-dim);border:1px solid rgba(212,168,83,.35);font-size:13px;color:var(--ca-amber);">
                        ⚠ Major releases will be highlighted prominently to all tenants.
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Release Notes ────────────────────────────────────── --}}
        <div class="ca-card" style="margin-bottom:16px;">
            <div class="ca-card-head"><span class="ca-card-title">Release Notes</span></div>
            <div class="ca-card-body">
                <label class="ca-label" for="release-notes">
                    Describe this version for your tenants <span style="color:var(--ca-ink-4);font-weight:400;">(supports markdown)</span>
                    <span style="color:var(--ca-red);">*</span>
                </label>
                <textarea id="release-notes" name="release_notes" rows="7"
                          placeholder="This release introduces the Leader Dashboard, giving program leaders real-time visibility into their disciples' progress..."
                          required
                          class="{{ $errors->has('release_notes') ? 'ca-is-invalid' : '' }}"
                          style="resize:vertical;">{{ old('release_notes') }}</textarea>
                @error('release_notes')<div class="ca-invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- ── New Features ─────────────────────────────────────── --}}
        <div class="ca-card" style="margin-bottom:16px;">
            <div class="ca-card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <span class="ca-card-title">New Features</span>
                <button type="button" @click="addFeature()"
                        class="ca-btn" style="font-size:12px;padding:5px 12px;background:var(--ca-sage-dim);color:var(--ca-sage);">
                    + Add feature
                </button>
            </div>
            <div class="ca-card-body" style="display:grid;gap:8px;">
                <template x-for="(feat, idx) in features" :key="idx">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <input type="text"
                               :name="'features[' + idx + ']'"
                               x-model="features[idx]"
                               placeholder="Added leader dashboard with real-time disciple progress"
                               style="flex:1;">
                        <button type="button" @click="removeFeature(idx)"
                                class="ca-btn" style="flex-shrink:0;width:32px;height:32px;padding:0;display:flex;align-items:center;justify-content:center;background:var(--ca-red-dim);color:var(--ca-red);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                </template>
                @error('features.*')<div class="ca-invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- ── Bug Fixes ────────────────────────────────────────── --}}
        <div class="ca-card" style="margin-bottom:24px;">
            <div class="ca-card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <span class="ca-card-title">Bug Fixes</span>
                <button type="button" @click="addFix()"
                        class="ca-btn" style="font-size:12px;padding:5px 12px;background:var(--ca-blue-dim);color:var(--ca-blue);">
                    + Add fix
                </button>
            </div>
            <div class="ca-card-body" style="display:grid;gap:8px;">
                <template x-for="(fix, idx) in bugFixes" :key="idx">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <input type="text"
                               :name="'bug_fixes[' + idx + ']'"
                               x-model="bugFixes[idx]"
                               placeholder="Fixed attendance import failing for large CSV files"
                               style="flex:1;">
                        <button type="button" @click="removeFix(idx)"
                                class="ca-btn" style="flex-shrink:0;width:32px;height:32px;padding:0;display:flex;align-items:center;justify-content:center;background:var(--ca-red-dim);color:var(--ca-red);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                </template>
                @error('bug_fixes.*')<div class="ca-invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- ── Actions ──────────────────────────────────────────── --}}
        <div style="display:flex;align-items:center;gap:12px;">
            <button type="submit" class="ca-btn ca-btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save as Draft
            </button>
            <a href="{{ route('admin.changelog.index') }}"
               class="ca-btn" style="background:transparent;color:var(--ca-ink-4);border:1px solid var(--ca-border);">
                Cancel
            </a>
        </div>

    </form>
</div>
@endsection
