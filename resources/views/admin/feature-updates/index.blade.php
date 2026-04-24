@extends('layouts.central-admin')

@section('title', 'Product Releases')
@section('topbar-title', 'Product Releases')

@section('content')
<div class="ca-page-head" style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
    <div>
        <h1 class="ca-page-title">Product Releases & Feature Packages</h1>
        <p class="ca-page-desc">Manage core application versions and dynamic feature scaffolding for tenants.</p>
    </div>
</div>

{{-- ── SECTION A: Application Versions ─────────────────────────────────────── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
    <h2 style="font-size:16px;font-weight:600;color:var(--ca-ink-1);margin:0;">Application Versions</h2>
    <a href="{{ route('admin.changelog.create') }}" class="ca-btn ca-btn-primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Version
    </a>
</div>

<div class="ca-card" style="margin-bottom:40px;">
    @if($versionHistories->isEmpty())
        <div style="text-align:center;padding:56px 24px;">
            <div style="font-size:14px;font-weight:500;color:var(--ca-ink-3);">No application versions yet</div>
        </div>
    @else
        <div class="ca-table-wrap">
            <table class="ca-table">
                <thead>
                    <tr>
                        <th>Version</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Pushed At</th>
                        <th>Tenants Acknowledged</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($versionHistories as $version)
                    <tr>
                        <td><span class="ca-pill" style="background:var(--ca-surface-dim);color:var(--ca-ink-3);">v{{ $version->version }}</span></td>
                        <td style="font-weight:500;color:var(--ca-ink-2);">{{ $version->title }}</td>
                        <td>
                            @if($version->status === 'published')
                                <span class="ca-pill ca-pill-ok"><span class="ca-pill-dot"></span> Published</span>
                            @else
                                <span class="ca-pill ca-pill-pending"><span class="ca-pill-dot"></span> Draft</span>
                            @endif
                        </td>
                        <td class="ca-table-muted">{{ $version->pushed_at ? $version->pushed_at->format('M d, Y H:i') : 'Not pushed' }}</td>
                        <td>{{ $version->acknowledgments_count }} / {{ $totalTenants }}</td>
                        <td>
                            <div style="display:flex;gap:8px;">
                                @if($version->status === 'draft')
                                    <form action="{{ route('admin.changelog.publish', $version) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="ca-btn" style="font-size:12px;padding:5px 12px;background:var(--ca-sage);color:white;border:none;">Publish</button>
                                    </form>
                                @endif
                                
                                @if($version->status === 'published' && !$version->pushed_at)
                                    <form action="{{ route('admin.changelog.broadcast', $version) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="ca-btn" style="font-size:12px;padding:5px 12px;background:var(--ca-blue);color:white;border:none;">Push to Tenants</button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.changelog.tenants', $version) }}" class="ca-btn" style="font-size:12px;padding:5px 12px;background:var(--ca-surface-dim);color:var(--ca-ink-3);">
                                    Acknowledgments
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- ── SECTION B: Feature Packages ─────────────────────────────────────────── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
    <h2 style="font-size:16px;font-weight:600;color:var(--ca-ink-1);margin:0;">Feature Packages</h2>
    <a href="{{ route('admin.feature-updates.create') }}" class="ca-btn ca-btn-primary" style="background:var(--ca-ink-1);color:white;border:none;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Feature Package
    </a>
</div>

<div class="ca-card">
    @if($featureUpdates->isEmpty())
        <div style="text-align:center;padding:56px 24px;">
            <div style="font-size:14px;font-weight:500;color:var(--ca-ink-3);">No feature packages yet</div>
            <div style="font-size:12.5px;color:var(--ca-ink-4);margin-top:4px;">Create your first feature package using the button above.</div>
        </div>
    @else
        <div class="ca-table-wrap">
            <table class="ca-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Version</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Tenants Installed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($featureUpdates as $feature)
                    <tr>
                        <td>
                            <a href="{{ route('admin.feature-updates.show', $feature) }}" style="color:var(--ca-ink-2);font-weight:500;text-decoration:none;">
                                {{ $feature->name }}
                            </a>
                        </td>
                        <td><code style="font-size:12px;color:var(--ca-ink-3);">{{ $feature->slug }}</code></td>
                        <td><span class="ca-pill" style="background:var(--ca-surface-dim);color:var(--ca-ink-3);">v{{ $feature->version }}</span></td>
                        <td style="text-transform:capitalize;">{{ $feature->type }}</td>
                        <td>
                            @if($feature->status === 'published')
                                <span class="ca-pill ca-pill-ok"><span class="ca-pill-dot"></span> Published</span>
                            @elseif($feature->status === 'draft')
                                <span class="ca-pill ca-pill-pending"><span class="ca-pill-dot"></span> Draft</span>
                            @elseif($feature->status === 'scaffold_failed')
                                <span class="ca-pill" style="background:#fdeaea;color:#c0392b;"><span class="ca-pill-dot" style="background:#c0392b;"></span> Failed</span>
                            @else
                                <span class="ca-pill">{{ ucfirst($feature->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $feature->installed_count ?? 0 }} / {{ $totalTenants }}</td>
                        <td>
                            <div style="display:flex;gap:8px;">
                                <a href="{{ route('admin.feature-updates.show', $feature) }}" class="ca-btn" style="font-size:12px;padding:5px 12px;background:var(--ca-surface-dim);color:var(--ca-ink-3);">
                                    View
                                </a>
                                @if($feature->status === 'draft' || $feature->status === 'scaffold_failed')
                                    <form action="{{ route('admin.feature-updates.publish', $feature) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="ca-btn" style="font-size:12px;padding:5px 12px;background:var(--ca-sage);color:white;border:none;">Publish</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
