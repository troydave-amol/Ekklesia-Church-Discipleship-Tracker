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

        @if(empty($files))
            <div style="font-size:13px;color:var(--ca-ink-4);">Scaffold has not run yet. Files will appear here once generated.</div>
        @else
            <div style="font-family:'DM Mono',monospace;font-size:12px;line-height:2;">
                @foreach($files as $file)
                    <div style="display:flex;align-items:center;gap:8px;">
                        @if($file['exists'])
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#27ae60" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span style="color:var(--ca-ink-2);">{{ $file['label'] }}</span>
                        @else
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#c0392b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            <span style="color:var(--ca-ink-4);text-decoration:line-through;">{{ $file['label'] }}</span>
                        @endif
                    </div>
                @endforeach
            </div>

            @php
                $existsCount = collect($files)->where('exists', true)->count();
                $totalCount  = count($files);
            @endphp
            <div style="margin-top:12px;font-size:12px;color:var(--ca-ink-4);">
                {{ $existsCount }}/{{ $totalCount }} files present on disk
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
@endsection
