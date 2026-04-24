@extends('layouts.central-admin')

@section('title', 'Tenants')
@section('topbar-title', 'Tenants')

@section('content')
<div class="ca-page-head" style="display:flex;align-items:flex-start;justify-content:space-between;">
    <div>
        <h1 class="ca-page-title">Provisioned Tenants</h1>
        <p class="ca-page-desc">Churches with an active subdomain and database — their workspaces are live.</p>
    </div>
    <a href="{{ route('admin.tenants.create') }}" class="ca-btn ca-btn-primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Tenant
    </a>
</div>

<div class="ca-table-card">
    <table class="ca-table">
        <thead>
            <tr>
                <th>Church</th>
                <th>Subdomain</th>
                <th>Plan</th>
                <th>Status</th>
                <th>Created</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($tenants as $t)
                <tr>
                    <td class="ca-table-strong">{{ $t->name }}</td>
                    <td><code class="ca-code">{{ $t->subdomain }}</code></td>
                    <td>{{ ucfirst($t->subscription_plan) }}</td>
                    <td>
                        @if($t->status === 'active')
                            <span class="ca-pill ca-pill-active">
                                <span class="ca-pill-dot"></span> Active
                            </span>
                        @else
                            <span class="ca-pill ca-pill-inactive">
                                <span class="ca-pill-dot"></span> {{ ucfirst($t->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="ca-table-muted">{{ $t->created_at->format('M j, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.tenants.show', $t) }}" class="ca-btn ca-btn-link">Details →</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="ca-table-empty">No tenants provisioned yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="ca-pagination">
    {{ $tenants->links() }}
</div>
@endsection
