@extends('layouts.central-admin')

@section('title', $featureUpdate->name)
@section('topbar-title', 'Feature Package Details')

@section('content')
<div class="ca-page-head" style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
    <div>
        <h1 class="ca-page-title">{{ $featureUpdate->name }}</h1>
        <p class="ca-page-desc">
            <code style="font-size:12px;color:var(--ca-ink-4);">{{ $featureUpdate->slug }}</code>
            &nbsp;·&nbsp; v{{ $featureUpdate->version }}
            &nbsp;·&nbsp; {{ ucfirst($featureUpdate->type) }}
        </p>
    </div>
    <a href="{{ route('admin.feature-updates.index') }}" class="ca-btn" style="background:var(--ca-surface-dim);color:var(--ca-ink-3);">
        ← Back
    </a>
</div>

@php
    $adminUser = auth('admin')->user();
    $isSuperAdmin = $adminUser && method_exists($adminUser, 'isSuperAdmin') && $adminUser->isSuperAdmin();
    $installationStatusSummary = collect($deleteImpact['installation_status_counts'] ?? [])
        ->map(fn ($count, $status) => ucfirst($status) . ': ' . $count)
        ->implode(' · ');
@endphp

@if(session('success'))
    <div class="ca-card" style="padding:14px 18px;margin-bottom:20px;background:rgba(39,174,96,.08);border:1px solid rgba(39,174,96,.18);color:#1f7a45;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="ca-card" style="padding:14px 18px;margin-bottom:20px;background:#fdeaea;border:1px solid #efc2c2;color:#c0392b;">
        {{ session('error') }}
    </div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">

    {{-- Status & Actions Card --}}
    <div class="ca-card" style="padding:20px;">
        <div style="font-size:11px;font-weight:600;color:var(--ca-ink-4);text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px;">Status & Actions</div>

        <div style="margin-bottom:16px;">
            @if($featureUpdate->status === 'published')
                <span class="ca-pill ca-pill-ok"><span class="ca-pill-dot"></span> Published</span>
            @elseif($featureUpdate->status === 'draft')
                <span class="ca-pill ca-pill-pending"><span class="ca-pill-dot"></span> Draft</span>
            @elseif($featureUpdate->status === 'scaffold_failed')
                <span class="ca-pill" style="background:#fdeaea;color:#c0392b;"><span class="ca-pill-dot" style="background:#c0392b;"></span> Scaffold Failed</span>
            @else
                <span class="ca-pill">{{ ucfirst($featureUpdate->status) }}</span>
            @endif
        </div>

        @if($featureUpdate->description)
            <p style="font-size:13px;color:var(--ca-ink-3);margin-bottom:16px;line-height:1.6;">{{ $featureUpdate->description }}</p>
        @endif

        @if($featureUpdate->published_at)
            <div style="font-size:12px;color:var(--ca-ink-4);margin-bottom:16px;">
                Published: <strong style="color:var(--ca-ink-2);">{{ $featureUpdate->published_at->format('M d, Y \a\t h:i A') }}</strong>
            </div>
        @endif

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            {{-- Re-scaffold --}}
            <form action="{{ route('admin.feature-updates.store') }}" method="POST" style="display:inline;">
                {{-- Re-scaffold is done via a separate route; placeholder link for now --}}
            </form>

            @if($featureUpdate->status === 'draft' || $featureUpdate->status === 'scaffold_failed')
                <form action="{{ route('admin.feature-updates.publish', $featureUpdate) }}" method="POST" onsubmit="return confirm('Publish this feature and notify all tenants?');">
                    @csrf
                    <button type="submit" class="ca-btn" style="font-size:12px;padding:6px 14px;background:var(--ca-sage);color:white;border:none;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 010 14.14"/></svg>
                        Publish & Notify
                    </button>
                </form>
            @endif

            @if($featureUpdate->status === 'published')
                <form action="{{ route('admin.feature-updates.seed', $featureUpdate) }}" method="POST" onsubmit="return confirm('Re-seed all installed tenants?');">
                    @csrf
                    <button type="submit" class="ca-btn" style="font-size:12px;padding:6px 14px;background:var(--ca-blue,#4a9eff);color:white;border:none;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>
                        Re-seed Tenants
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Generated Files Card --}}
    <div class="ca-card" style="padding:20px;">
        <div style="font-size:11px;font-weight:600;color:var(--ca-ink-4);text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px;">Generated Files</div>

        @if($inspection['selected_count'] === 0)
            <div style="font-size:13px;color:var(--ca-ink-4);">No scaffold components were selected for this feature package.</div>
        @else
            @if(!$inspection['is_complete'])
                <div style="display:flex;gap:10px;align-items:flex-start;padding:10px 12px;border-radius:10px;background:#fdeaea;border:1px solid #efc2c2;margin-bottom:14px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#c0392b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div style="font-size:12px;line-height:1.5;color:#c0392b;">
                        Missing or incomplete selected components:
                        <strong>{{ implode(', ', $inspection['missing_labels']) }}</strong>
                    </div>
                </div>
            @endif

            <div style="display:grid;gap:10px;">
                @foreach($inspection['selected_components'] as $component)
                    <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;border:1px solid var(--ca-border);border-radius:10px;background:var(--ca-surface);">
                        @if($component['exists'])
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#27ae60" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;"><polyline points="20 6 9 17 4 12"/></svg>
                        @else
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#c0392b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        @endif

                        <div style="min-width:0;">
                            <div style="font-size:13px;font-weight:600;color:{{ $component['exists'] ? 'var(--ca-ink-2)' : '#c0392b' }};">
                                {{ $component['label'] }}
                            </div>
                            <div style="font-family:'DM Mono',monospace;font-size:11.5px;color:var(--ca-ink-4);margin-top:2px;word-break:break-all;">
                                {{ $component['relative_path'] ?? 'Path metadata unavailable' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top:12px;font-size:12px;color:var(--ca-ink-4);">
                {{ $inspection['existing_count'] }}/{{ $inspection['selected_count'] }} selected files present on disk
            </div>
        @endif
    </div>
</div>

{{-- Tenant Installations Table --}}
<div class="ca-card">
    <div class="ca-card-head" style="padding:14px 20px;">
        <span class="ca-card-title">Tenant Installation Status</span>
        <span style="font-size:12px;color:var(--ca-ink-4);">
            {{ $featureUpdate->installations->count() }} tenant(s) tracked
        </span>
    </div>

    @if($featureUpdate->installations->isEmpty())
        <div style="text-align:center;padding:48px 24px;">
            <div style="font-size:13px;color:var(--ca-ink-4);">No tenants tracked yet. Publish the feature to create pending installation records.</div>
        </div>
    @else
        <div class="ca-table-wrap">
            <table class="ca-table">
                <thead>
                    <tr>
                        <th>Church Name</th>
                        <th>Tenant ID</th>
                        <th>Status</th>
                        <th>Installed At</th>
                        <th>Error</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($featureUpdate->installations as $install)
                    <tr>
                        <td style="font-weight:500;color:var(--ca-ink-2);">{{ $install->tenant->name ?? '—' }}</td>
                        <td><code style="font-size:12px;color:var(--ca-ink-3);">{{ $install->tenant_id }}</code></td>
                        <td>
                            @if($install->status === 'installed')
                                <span class="ca-pill ca-pill-ok"><span class="ca-pill-dot"></span> Installed</span>
                            @elseif($install->status === 'installing')
                                <span class="ca-pill" style="background:rgba(88,166,255,.1);color:#4a9eff;border:1px solid rgba(88,166,255,.2);"><span class="ca-pill-dot" style="background:#4a9eff;"></span> Installing</span>
                            @elseif($install->status === 'pending')
                                <span class="ca-pill ca-pill-pending"><span class="ca-pill-dot"></span> Pending</span>
                            @elseif($install->status === 'failed')
                                <span class="ca-pill" style="background:#fdeaea;color:#c0392b;"><span class="ca-pill-dot" style="background:#c0392b;"></span> Failed</span>
                            @endif
                        </td>
                        <td class="ca-table-muted">{{ $install->installed_at?->format('M d, Y h:i A') ?? '—' }}</td>
                        <td>
                            @if($install->error_log)
                                <span style="font-size:11.5px;color:#c0392b;max-width:220px;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                      title="{{ $install->error_log }}">
                                    {{ Str::limit($install->error_log, 70) }}
                                </span>
                            @else
                                <span class="ca-table-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($install->status === 'failed' || $install->status === 'pending')
                                {{-- Retry is handled by re-running the install on the tenant side --}}
                                <span class="ca-table-muted" style="font-size:12px;">Retry via tenant</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@if($isSuperAdmin)
    <div class="ca-danger-zone" style="margin-top:24px;">
        <div class="ca-danger-zone-head">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            <span>Danger Zone</span>
        </div>
        <div class="ca-danger-zone-body">
            <div class="ca-danger-zone-row" style="align-items:flex-start;gap:18px;">
                <div style="flex:1;min-width:0;">
                    <div class="ca-danger-zone-title">Delete this feature package</div>
                    <div class="ca-danger-zone-desc" style="margin-bottom:12px;">
                        Permanently removes <strong>{{ $featureUpdate->name }}</strong> (v{{ $featureUpdate->version }}) from the central app, deletes its tracked installation records, and cleans up any managed scaffold files still present on disk.
                    </div>
                    <div style="display:grid;gap:6px;font-size:12px;color:var(--ca-ink-3);line-height:1.6;">
                        <div><strong style="color:var(--ca-ink-2);">{{ $deleteImpact['selected_component_count'] }}</strong> selected scaffold file(s) targeted for cleanup.</div>
                        <div><strong style="color:var(--ca-ink-2);">{{ $deleteImpact['tracked_tenant_count'] }}</strong> tenant installation record(s) will be removed from central tracking.</div>
                        <div>Tenant-side schema changes, seeded data, and past tenant notifications will <strong>not</strong> be rolled back.</div>
                        @if($installationStatusSummary !== '')
                            <div>Current installation states: {{ $installationStatusSummary }}</div>
                        @endif
                    </div>

                    @if($deleteImpact['has_installing_installations'])
                        <div style="display:flex;gap:10px;align-items:flex-start;padding:10px 12px;border-radius:10px;background:#fff4e5;border:1px solid #f3d2a4;color:#9a5d00;margin-top:14px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <div style="font-size:12px;line-height:1.5;">
                                Deletion is temporarily blocked because at least one tenant installation is currently in progress.
                            </div>
                        </div>
                    @endif
                </div>

                <form method="POST" action="{{ route('admin.feature-updates.destroy', $featureUpdate) }}" id="delete-feature-package-form" style="flex-shrink:0;">
                    @csrf
                    @method('DELETE')
                    <button
                        type="button"
                        class="ca-cta ca-cta-danger"
                        onclick="confirmDeleteFeaturePackage()"
                        @if($deleteImpact['has_installing_installations']) disabled @endif
                        style="@if($deleteImpact['has_installing_installations']) opacity:.6;cursor:not-allowed; @endif"
                    >
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                            <line x1="10" y1="11" x2="10" y2="17"/>
                            <line x1="14" y1="11" x2="14" y2="17"/>
                        </svg>
                        Delete Feature Package
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function confirmDeleteFeaturePackage() {
        const packageName = @json($featureUpdate->name);
        const packageVersion = @json($featureUpdate->version);
        const typedName = prompt(`Type "${packageName}" to confirm deletion of feature package v${packageVersion}.`);

        if (typedName === null) {
            return;
        }

        if (typedName.trim() !== packageName) {
            alert(`Package name did not match. "${packageName}" was not deleted.`);
            return;
        }

        if (confirm(`Final warning: deleting "${packageName}" will remove its central tracking records and delete any managed scaffold files still present on disk.\n\nTenant-side schema changes and seeded data will NOT be rolled back.\n\nClick OK to permanently delete this feature package.`)) {
            document.getElementById('delete-feature-package-form').submit();
        }
    }
    </script>
@endif
@endsection
