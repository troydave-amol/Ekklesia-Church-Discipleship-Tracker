@extends('layouts.central-admin')

@section('title', $tenant->name)
@section('topbar-title', 'Tenant Detail')

@section('content')

<a href="{{ route('admin.tenants.index') }}" class="ca-back">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="19" y1="12" x2="5" y2="12"/>
        <polyline points="12 19 5 12 12 5"/>
    </svg>
    All tenants
</a>

<div class="ca-page-head" style="display:flex;align-items:flex-start;justify-content:space-between;">
    <div>
        <h1 class="ca-page-title">{{ $tenant->name }}</h1>
        <p class="ca-page-desc">Workspace configuration and domain details for this tenant.</p>
    </div>
    <div class="ca-actions">
        <a href="{{ route('admin.tenants.edit', $tenant) }}" class="ca-btn ca-cta-ghost" style="gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Edit Tenant
        </a>
        <a href="{{ route('admin.rbac.show', $tenant) }}" class="ca-btn ca-cta-ghost" style="gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3m0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22"/>
            </svg>
            Manage RBAC
        </a>
        <form method="POST" action="{{ route('admin.tenants.toggle-status', $tenant) }}" class="ca-inline">
            @csrf
            @method('PATCH')
            @if($tenant->status === 'active')
                <button type="submit" class="ca-btn ca-btn-reject" onclick="return confirm('Are you sure you want to suspend this tenant? All users will be locked out immediately.')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                    Disable
                </button>
            @else
                <button type="submit" class="ca-btn ca-btn-approve">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Enable
                </button>
            @endif
        </form>
    </div>
</div>

{{-- ═══ DETAIL GRID ═══ --}}
<div class="ca-detail-grid">
    <div class="ca-detail-item">
        <div class="ca-detail-label">Email</div>
        <div class="ca-detail-value">{{ $tenant->email }}</div>
    </div>
    <div class="ca-detail-item">
        <div class="ca-detail-label">Subdomain</div>
        <div class="ca-detail-value">
            @php
                $domain = config('app.domain', 'localhost');
                $scheme = request()->getScheme();
                $port = request()->getPort();
                $tenantUrl = in_array($domain, ['localhost', '127.0.0.1']) 
                    ? "{$scheme}://{$tenant->subdomain}.{$domain}:{$port}" 
                    : "https://{$tenant->subdomain}.{$domain}";
            @endphp
            <a href="{{ $tenantUrl }}" target="_blank" class="ca-subdomain-link" title="Open {{ $tenant->subdomain }}.{{ $domain }}">
                <code class="ca-code">{{ $tenant->subdomain }}</code>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.5;">
                    <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                </svg>
            </a>
        </div>
    </div>
    <div class="ca-detail-item">
        <div class="ca-detail-label">Database</div>
        <div class="ca-detail-value"><code class="ca-code">{{ $tenant->db_name }}</code></div>
    </div>
    <div class="ca-detail-item">
        <div class="ca-detail-label">Subscription Plan</div>
        <div class="ca-detail-value">{{ ucfirst($tenant->subscription_plan) }}</div>
    </div>
    <div class="ca-detail-item">
        <div class="ca-detail-label">Status</div>
        <div class="ca-detail-value">
            @if($tenant->status === 'active')
                <span class="ca-pill ca-pill-active"><span class="ca-pill-dot"></span> Active</span>
            @else
                <span class="ca-pill ca-pill-inactive"><span class="ca-pill-dot"></span> {{ ucfirst($tenant->status) }}</span>
            @endif
        </div>
    </div>
    <div class="ca-detail-item">
        <div class="ca-detail-label">Created</div>
        <div class="ca-detail-value">{{ $tenant->created_at->format('M j, Y') }}</div>
    </div>
</div>

{{-- ═══ DOMAINS ═══ --}}
<div class="ca-card">
    <div class="ca-card-head">
        <span class="ca-card-title">Domains</span>
    </div>
    <div class="ca-card-body">
        @forelse($tenant->domains as $d)
            <div class="ca-plan-row">
                <span class="ca-plan-name">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="opacity:.5;">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/>
                    </svg>
                    @php
                        $dDomain = config('app.domain', 'localhost');
                        $dScheme = request()->getScheme();
                        $dPort = request()->getPort();
                        $dUrl = in_array($dDomain, ['localhost', '127.0.0.1']) 
                            ? "{$dScheme}://{$d->domain}.{$dDomain}:{$dPort}" 
                            : "https://{$d->domain}.{$dDomain}";
                    @endphp
                    <a href="{{ $dUrl }}" target="_blank" class="ca-subdomain-link" title="Open {{ $d->domain }}.{{ $dDomain }}">
                        <code class="ca-code">{{ $d->domain }}</code>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.5;">
                            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                    </a>
                </span>
            </div>
        @empty
            <p style="color:var(--ca-ink-4);font-size:13px;">No domains configured.</p>
        @endforelse
    </div>
</div>

{{-- ═══ DANGER ZONE ═══ --}}
<div class="ca-danger-zone">
    <div class="ca-danger-zone-head">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <span>Danger Zone</span>
    </div>
    <div class="ca-danger-zone-body">
        <div class="ca-danger-zone-row">
            <div>
                <div class="ca-danger-zone-title">Delete this tenant</div>
                <div class="ca-danger-zone-desc">
                    Permanently removes <strong>{{ $tenant->name }}</strong>, its database, all associated domains, and every record within the workspace. This action cannot be undone.
                </div>
            </div>
            <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}" id="delete-tenant-form">
                @csrf
                @method('DELETE')
                <button type="button" class="ca-cta ca-cta-danger" onclick="confirmDeleteTenant()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                        <line x1="10" y1="11" x2="10" y2="17"/>
                        <line x1="14" y1="11" x2="14" y2="17"/>
                    </svg>
                    Delete Tenant
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDeleteTenant() {
    const name = @json($tenant->name);
    if (confirm(`Are you sure you want to permanently delete "${name}"?\n\nThis will destroy the tenant database and all associated data. This action CANNOT be undone.`)) {
        if (confirm(`FINAL WARNING: You are about to delete "${name}" permanently.\n\nClick OK to proceed with deletion.`)) {
            document.getElementById('delete-tenant-form').submit();
        }
    }
}
</script>
@endsection
