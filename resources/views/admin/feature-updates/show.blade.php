@extends('layouts.central-admin')

@section('title', $featureUpdate->name)
@section('topbar-title', 'Feature Update Details')

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
        ← Back to Features
    </a>
</div>

{{-- Feature Meta Card --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    {{-- Status & Actions --}}
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
            <p style="font-size:13px;color:var(--ca-ink-3);margin-bottom:16px;">{{ $featureUpdate->description }}</p>
        @endif

        @if($featureUpdate->published_at)
            <div style="font-size:12px;color:var(--ca-ink-4);margin-bottom:16px;">
                Published: <strong style="color:var(--ca-ink-2);">{{ $featureUpdate->published_at->format('M d, Y \a\t h:i A') }}</strong>
            </div>
        @endif

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            @if($featureUpdate->status === 'draft')
                <form action="{{ route('admin.feature-updates.publish', $featureUpdate) }}" method="POST" onsubmit="return confirm('Publish this feature and notify all tenants?');">
                    @csrf
                    <button type="submit" class="ca-btn ca-btn-approve" style="font-size:12px;padding:6px 14px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 010 14.14"/></svg>
                        Publish & Notify Tenants
                    </button>
                </form>
            @endif

            @if($featureUpdate->status === 'published')
                <form action="{{ route('admin.feature-updates.seed', $featureUpdate) }}" method="POST" onsubmit="return confirm('Re-seed all installed tenants with 50 factory rows?');">
                    @csrf
                    <button type="submit" class="ca-btn" style="font-size:12px;padding:6px 14px;background:var(--ca-blue);color:#fff;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>
                        Re-seed Installed Tenants
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Payload / Generated Files --}}
    <div class="ca-card" style="padding:20px;">
        <div style="font-size:11px;font-weight:600;color:var(--ca-ink-4);text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px;">Scaffolded Payload</div>

        @if(!empty($featureUpdate->payload) && ($featureUpdate->payload['files_generated'] ?? false))
            <div style="font-size:13px;color:var(--ca-ink-3);line-height:1.8;">
                <div><strong>Model:</strong> <code>{{ $featureUpdate->payload['model'] ?? '—' }}</code></div>
                <div><strong>Migration:</strong> <code>{{ $featureUpdate->payload['migration_file'] ?? '—' }}</code></div>
                <div><strong>Factory:</strong> <code>{{ $featureUpdate->payload['factory_class'] ?? '—' }}</code></div>
                <div><strong>Seeder:</strong> <code>{{ $featureUpdate->payload['seeder_class'] ?? '—' }}</code></div>
                <div><strong>Factory Count:</strong> {{ $featureUpdate->payload['factory_count'] ?? 50 }}</div>
            </div>
        @else
            <div style="font-size:13px;color:var(--ca-ink-4);">Files have not been generated yet (scaffold job may still be processing).</div>
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
            <div style="font-size:13px;color:var(--ca-ink-4);">No tenants tracked yet. Publish the feature to create pending installations.</div>
        </div>
    @else
        <div class="ca-table-wrap">
            <table class="ca-table">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Status</th>
                        <th>Installed At</th>
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($featureUpdate->installations as $install)
                    <tr>
                        <td>
                            <span style="font-weight:500;color:var(--ca-ink-2);">{{ $install->tenant->name ?? $install->tenant_id }}</span>
                        </td>
                        <td>
                            @if($install->status === 'installed')
                                <span class="ca-pill ca-pill-ok"><span class="ca-pill-dot"></span> Installed</span>
                            @elseif($install->status === 'installing')
                                <span class="ca-pill ca-pill-info"><span class="ca-pill-dot"></span> Installing</span>
                            @elseif($install->status === 'pending')
                                <span class="ca-pill ca-pill-pending"><span class="ca-pill-dot"></span> Pending</span>
                            @elseif($install->status === 'failed')
                                <span class="ca-pill" style="background:#fdeaea;color:#c0392b;"><span class="ca-pill-dot" style="background:#c0392b;"></span> Failed</span>
                            @endif
                        </td>
                        <td class="ca-table-muted">{{ $install->installed_at?->format('M d, Y h:i A') ?? '—' }}</td>
                        <td>
                            @if($install->error_log)
                                <span style="font-size:11.5px;color:#c0392b;max-width:240px;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                      title="{{ $install->error_log }}">
                                    {{ Str::limit($install->error_log, 80) }}
                                </span>
                            @else
                                <span class="ca-table-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
