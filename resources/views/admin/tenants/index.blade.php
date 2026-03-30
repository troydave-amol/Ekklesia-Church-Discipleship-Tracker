@extends('layouts.central-admin')

@section('title', 'Tenants')

@section('content')
<h2>Provisioned tenants</h2>
<p class="muted" style="margin-bottom:1rem;">Churches with an active subdomain and database.</p>

<table>
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
        @foreach($tenants as $t)
            <tr>
                <td>{{ $t->name }}</td>
                <td><code>{{ $t->subdomain }}</code></td>
                <td>{{ ucfirst($t->subscription_plan) }}</td>
                <td>{{ $t->status }}</td>
                <td class="muted">{{ $t->created_at->format('M j, Y') }}</td>
                <td><a href="{{ route('admin.tenants.show', $t) }}">Details</a></td>
            </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top:1rem;">{{ $tenants->links() }}</div>
@endsection
