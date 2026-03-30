@extends('layouts.central-admin')

@section('title', 'Monitoring')

@section('content')
<h2>Central app monitoring</h2>
<p class="muted" style="margin-bottom:1.5rem;">SaaS control plane for church tenants — registration queue, live churches, and plan distribution.</p>

<div class="grid">
    <div class="card">
        <div class="label">Pending approvals</div>
        <div class="value">{{ $pendingSignups }}</div>
        <div class="muted" style="margin-top:0.5rem;font-size:0.8rem;">Awaiting domain &amp; DB provisioning</div>
    </div>
    <div class="card">
        <div class="label">Active tenants</div>
        <div class="value">{{ $activeTenants }}</div>
    </div>
    <div class="card">
        <div class="label">Total tenants</div>
        <div class="value">{{ $totalTenants }}</div>
    </div>
</div>

<h2 style="margin-top:0.5rem;">Churches by plan</h2>
<div class="card" style="max-width:480px;">
    @forelse($planRows as $row)
        <div style="display:flex;justify-content:space-between;padding:0.35rem 0;border-bottom:1px solid #21262d;">
            <span>{{ $row['label'] }}</span>
            <strong>{{ $row['count'] }}</strong>
        </div>
    @empty
        <p class="muted">No plan data.</p>
    @endforelse
</div>

<div style="margin-top:2rem;">
    <a class="btn btn-primary" href="{{ route('admin.signups.index') }}">Review pending registrations</a>
</div>
@endsection
