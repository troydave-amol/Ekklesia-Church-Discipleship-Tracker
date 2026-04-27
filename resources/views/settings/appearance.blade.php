@extends('layouts.app')

@section('title', 'Appearance Settings')

@section('content')
<div class="flex justify-between items-center mb-20">
    <div>
        <h1 class="page-title">Appearance</h1>
        <p class="page-subtitle">Customize your church's brand colors and logo.</p>
    </div>
    <a href="{{ route('settings.church') }}" class="btn btn-ghost btn-sm">← Back to Settings</a>
</div>

<form method="POST" action="{{ route('settings.appearance.update') }}" enctype="multipart/form-data" id="appearanceForm">
    @csrf
    @method('PUT')

    <div class="grid-2" style="gap:24px;">

        {{-- ── Color Card ───────────────────────────────────────── --}}
        <div class="card">
            <div class="card-header">
                <span class="section-title">Brand Color</span>
            </div>
            <div class="card-body">
                <p class="text-muted" style="font-size:13px;margin-bottom:16px;">
                    Choose a primary brand color for your church's interface. This color will be applied to navigation highlights, buttons, and accents throughout your workspace.
                </p>

                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
                    <label for="colorPicker" style="cursor:pointer;">
                        <div id="colorPreview" style="
                            width:56px;height:56px;border-radius:12px;border:2px solid var(--border);
                            background:{{ $church->brand_color }};
                            transition: background .2s, box-shadow .2s;
                            box-shadow: 0 2px 12px {{ $church->brand_color }}44;
                        "></div>
                    </label>
                    <div style="flex:1;">
                        <label class="field-label" for="primary_color">Hex Color</label>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <input
                                type="color"
                                id="colorPicker"
                                value="{{ $church->brand_color }}"
                                style="width:40px;height:38px;padding:2px;cursor:pointer;border-radius:6px;"
                            >
                            <input
                                type="text"
                                name="primary_color"
                                id="colorHex"
                                value="{{ $church->brand_color }}"
                                maxlength="7"
                                placeholder="#D4A853"
                                style="flex:1;font-family:'DM Mono',monospace;"
                            >
                        </div>
                    </div>
                </div>

                {{-- Live Preview --}}
                <div style="border:1px solid var(--border);border-radius:var(--radius);padding:16px;">
                    <p style="font-size:11px;color:var(--text-dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px;">Live Preview</p>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        <button type="button" id="previewBtnPrimary" class="btn" style="background:{{ $church->brand_color }};color:#0D1117;font-weight:600;">Primary Button</button>
                        <button type="button" id="previewBtnSecondary" class="btn" style="background:transparent;border:1px solid {{ $church->brand_color }};color:{{ $church->brand_color }};">Secondary</button>
                        <span id="previewBadge" class="badge" style="background:{{ $church->brand_color }}1A;color:{{ $church->brand_color }};border:1px solid {{ $church->brand_color }}44;">Badge</span>
                    </div>
                    <div style="margin-top:12px;height:4px;background:var(--border-light);border-radius:2px;overflow:hidden;">
                        <div id="previewProgress" style="width:65%;height:100%;border-radius:2px;background:{{ $church->brand_color }};transition:background .2s;"></div>
                    </div>
                </div>

                <button type="button" id="resetColor" class="btn btn-ghost btn-sm" style="margin-top:12px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
                    Reset to Default
                </button>

                @error('primary_color')
                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                @enderror

                {{-- Accessibility Warning --}}
                @if($accessibilityCheck && !$accessibilityCheck['passes'])
                <div class="alert alert-warning" style="margin-top:12px;padding:12px 16px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <div>
                        <strong>Accessibility Notice:</strong>
                        <ul style="margin:4px 0 0 0;padding-left:20px;font-size:12px;">
                            @foreach($accessibilityCheck['issues'] as $issue)
                                <li>{{ $issue }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ── Logo Card ────────────────────────────────────────── --}}
        <div class="card">
            <div class="card-header">
                <span class="section-title">Church Logo</span>
            </div>
            <div class="card-body">
                <p class="text-muted" style="font-size:13px;margin-bottom:16px;">
                    Upload your church's logo. It will replace the default icon in the sidebar. Recommended: square image, 200×200px minimum, PNG or SVG.
                </p>

                {{-- Current Logo --}}
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
                    <div id="logoPreviewContainer" style="
                        width:72px;height:72px;border-radius:14px;overflow:hidden;flex-shrink:0;
                        background:var(--surface);
                        display:flex;align-items:center;justify-content:center;
                        border:2px solid var(--border);
                    ">
                        @if($church->logo_path)
                            <img id="logoImg" src="{{ $church->logo_url }}" alt="Church Logo" style="width:100%;height:100%;object-fit:contain;padding:4px;">
                        @else
                            <svg id="logoPlaceholder" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--text-dim)" stroke-width="2">
                                <line x1="12" y1="2" x2="12" y2="22"/><line x1="2" y1="9" x2="22" y2="9"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:500;margin-bottom:2px;">
                            {{ $church->logo_path ? 'Current Logo' : 'No logo uploaded' }}
                        </div>
                        <div style="font-size:12px;color:var(--text-muted);">PNG, JPG, SVG, or WebP · Max 2 MB</div>
                    </div>
                </div>

                {{-- Upload Zone --}}
                <div
                    id="dropZone"
                    style="
                        border:2px dashed var(--border);border-radius:var(--radius);
                        padding:28px;text-align:center;cursor:pointer;
                        transition:border-color .2s, background .2s;
                    "
                >
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--text-dim)" stroke-width="1.5" style="margin-bottom:8px;">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <p style="font-size:13px;color:var(--text-muted);">Drag & drop your logo here, or <span style="color:var(--gold);font-weight:500;">click to browse</span></p>
                    <input type="file" name="logo" id="logoInput" accept="image/png,image/jpeg,image/svg+xml,image/webp" style="display:none;">
                </div>

                @if($church->logo_path)
                <div style="margin-top:12px;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--red);">
                        <input type="checkbox" name="remove_logo" value="1" style="width:auto;height:auto;">
                        Remove current logo
                    </label>
                </div>
                @endif

                @error('logo')
                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    {{-- Save --}}
    <div style="margin-top:24px;display:flex;justify-content:flex-end;gap:8px;">
        <a href="{{ route('settings.church') }}" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            Save Changes
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
(function () {
    const DEFAULT_COLOR = '#D4A853';
    const picker = document.getElementById('colorPicker');
    const hex    = document.getElementById('colorHex');
    const preview = document.getElementById('colorPreview');
    const btnP   = document.getElementById('previewBtnPrimary');
    const btnS   = document.getElementById('previewBtnSecondary');
    const badge  = document.getElementById('previewBadge');
    const prog   = document.getElementById('previewProgress');
    const reset  = document.getElementById('resetColor');

    function applyColor(c) {
        preview.style.background = c;
        preview.style.boxShadow  = '0 2px 12px ' + c + '44';
        btnP.style.background = c;
        btnS.style.borderColor = c;
        btnS.style.color = c;
        badge.style.background = c + '1A';
        badge.style.color = c;
        badge.style.borderColor = c + '44';
        prog.style.background = c;
    }

    picker.addEventListener('input', (e) => {
        hex.value = e.target.value;
        applyColor(e.target.value);
    });

    hex.addEventListener('input', (e) => {
        const v = e.target.value;
        if (/^#[0-9A-Fa-f]{6}$/.test(v)) {
            picker.value = v;
            applyColor(v);
        }
    });

    reset.addEventListener('click', () => {
        hex.value = DEFAULT_COLOR;
        picker.value = DEFAULT_COLOR;
        applyColor(DEFAULT_COLOR);
    });

    // ── Drag & Drop ──
    const zone  = document.getElementById('dropZone');
    const input = document.getElementById('logoInput');

    zone.addEventListener('click', () => input.click());
    zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.style.borderColor = 'var(--gold)'; zone.style.background = 'rgba(212,168,83,.04)'; });
    zone.addEventListener('dragleave', () => { zone.style.borderColor = 'var(--border)'; zone.style.background = 'transparent'; });
    zone.addEventListener('drop', (e) => {
        e.preventDefault();
        zone.style.borderColor = 'var(--border)';
        zone.style.background = 'transparent';
        if (e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            previewLogo(e.dataTransfer.files[0]);
        }
    });

    input.addEventListener('change', () => {
        if (input.files.length) previewLogo(input.files[0]);
    });

    function previewLogo(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const container = document.getElementById('logoPreviewContainer');
            container.style.background = 'var(--surface)';
            container.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:contain;padding:4px;" alt="Preview">';
            zone.innerHTML = '<p style="font-size:13px;color:var(--green);font-weight:500;">✓ ' + file.name + ' selected</p>';
        };
        reader.readAsDataURL(file);
    }
})();
</script>
@endpush
