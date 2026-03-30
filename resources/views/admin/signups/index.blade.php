@extends('layouts.central-admin')

@section('title', 'Approvals')

@section('content')
<h2>Tenant registrations</h2>
<p class="muted" style="margin-bottom:1rem;">New churches (pastors) request a subdomain. Approve to create the domain record and tenant database.</p>

<div class="stack" style="margin-bottom:1rem;">
    <a href="{{ route('admin.signups.index', ['status' => 'pending']) }}" class="btn btn-ghost" @if($filter==='pending') style="border-color:#58a6ff;color:#58a6ff;" @endif>Pending</a>
    <a href="{{ route('admin.signups.index', ['status' => 'provisioned']) }}" class="btn btn-ghost" @if($filter==='provisioned') style="border-color:#58a6ff;color:#58a6ff;" @endif>Provisioned</a>
    <a href="{{ route('admin.signups.index', ['status' => 'rejected']) }}" class="btn btn-ghost" @if($filter==='rejected') style="border-color:#58a6ff;color:#58a6ff;" @endif>Rejected</a>
    <a href="{{ route('admin.signups.index', ['status' => 'all']) }}" class="btn btn-ghost" @if($filter==='all') style="border-color:#58a6ff;color:#58a6ff;" @endif>All</a>
</div>

<table>
    <thead>
        <tr>
            <th>Church</th>
            <th>Subdomain</th>
            <th>Pastor</th>
            <th>Plan</th>
            <th>Status</th>
            <th>Requested</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse($signups as $s)
            <tr>
                <td>{{ $s->church_name }}</td>
                <td><code>{{ $s->subdomain }}</code></td>
                <td>{{ $s->pastor_name }}<br><span class="muted">{{ $s->pastor_email }}</span></td>
                <td>{{ ucfirst($s->subscription_plan) }}</td>
                <td>
                    @if($s->status === 'pending')
                        <span class="pill pill-pending">Pending</span>
                    @elseif($s->status === 'provisioned')
                        <span class="pill pill-ok">Provisioned</span>
                    @else
                        <span class="pill pill-no">Rejected</span>
                    @endif
                </td>
                <td class="muted">{{ $s->created_at->format('M j, Y H:i') }}</td>
                <td>
                    @if($s->status === 'pending')
                        <form action="{{ route('admin.signups.approve', $s) }}" method="post" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">Approve &amp; provision</button>
                        </form>
                        <form action="{{ route('admin.signups.reject', $s) }}" method="post" class="inline" style="margin-top:0.35rem;" onsubmit="return confirm('Reject this registration?');">
                            @csrf
                            <input type="hidden" name="rejection_note" value="">
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </form>
                    @elseif($s->status === 'provisioned' && $s->tenant_id)
                        <a href="{{ route('admin.tenants.show', $s->tenant_id) }}">View tenant</a>
                    @elseif($s->status === 'rejected' && $s->rejection_note)
                        <span class="muted">{{ \Illuminate\Support\Str::limit($s->rejection_note, 40) }}</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="muted">No signups in this view.</td></tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top:1rem;">
    {{ $signups->links() }}
</div>
@endsection
