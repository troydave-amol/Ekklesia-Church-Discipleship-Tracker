@extends('layouts.central-admin')

@section('title', $tenant->name)

@section('content')
<h2>{{ $tenant->name }}</h2>
<p class="muted" style="margin-bottom:1.5rem;"><a href="{{ route('admin.tenants.index') }}">← All tenants</a></p>

<div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));">
    <div class="card">
        <div class="label">Email</div>
        <div>{{ $tenant->email }}</div>
    </div>
    <div class="card">
        <div class="label">Subdomain</div>
        <div><code>{{ $tenant->subdomain }}</code></div>
    </div>
    <div class="card">
        <div class="label">Database</div>
        <div><code>{{ $tenant->db_name }}</code></div>
    </div>
    <div class="card">
        <div class="label">Plan</div>
        <div>{{ ucfirst($tenant->subscription_plan) }}</div>
    </div>
    <div class="card">
        <div class="label">Status</div>
        <div>{{ $tenant->status }}</div>
    </div>
</div>

<h3 style="margin:1.5rem 0 0.5rem;font-size:1rem;">Domains</h3>
<ul class="muted">
    @foreach($tenant->domains as $d)
        <li><code>{{ $d->domain }}</code></li>
    @endforeach
</ul>
@endsection
