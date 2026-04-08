@extends('layouts.central-admin')

@section('title', 'Approvals')
@section('topbar-title', 'Approvals')

@section('content')
<div class="ca-page-head">
    <h1 class="ca-page-title">Tenant Registrations</h1>
    <p class="ca-page-desc">Review incoming church sign-ups. Approve to provision a workspace, or reject with a note.</p>
</div>

{{-- ═══ SEGMENTED FILTERS ═══ --}}
<div class="ca-filters">
    <a href="{{ route('admin.signups.index', ['status' => 'pending']) }}"
       class="ca-filter {{ $filter === 'pending' ? 'active' : '' }}">Pending</a>
    <a href="{{ route('admin.signups.index', ['status' => 'provisioned']) }}"
       class="ca-filter {{ $filter === 'provisioned' ? 'active' : '' }}">Provisioned</a>
    <a href="{{ route('admin.signups.index', ['status' => 'rejected']) }}"
       class="ca-filter {{ $filter === 'rejected' ? 'active' : '' }}">Rejected</a>
    <a href="{{ route('admin.signups.index', ['status' => 'all']) }}"
       class="ca-filter {{ $filter === 'all' ? 'active' : '' }}">All</a>
</div>

{{-- ═══ TABLE ═══ --}}
<div class="ca-table-card">
    <table class="ca-table">
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
                    <td class="ca-table-strong">{{ $s->church_name }}</td>
                    <td><code class="ca-code">{{ $s->subdomain }}</code></td>
                    <td>
                        <div class="ca-table-strong">{{ $s->pastor_name }}</div>
                        <div class="ca-table-email">{{ $s->pastor_email }}</div>
                    </td>
                    <td>{{ ucfirst($s->subscription_plan) }}</td>
                    <td>
                        @if($s->status === 'pending')
                            <span class="ca-pill ca-pill-pending">
                                <span class="ca-pill-dot"></span> Pending
                            </span>
                        @elseif($s->status === 'provisioned')
                            <span class="ca-pill ca-pill-ok">
                                <span class="ca-pill-dot"></span> Provisioned
                            </span>
                        @else
                            <span class="ca-pill ca-pill-no">
                                <span class="ca-pill-dot"></span> Rejected
                            </span>
                        @endif
                    </td>
                    <td class="ca-table-muted">{{ $s->created_at->format('M j, Y H:i') }}</td>
                    <td>
                        @if($s->status === 'pending')
                            <div class="ca-actions">
                                <form action="{{ route('admin.signups.approve', $s) }}" method="post" class="ca-inline">
                                    @csrf
                                    <button type="submit" class="ca-btn ca-btn-approve">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.signups.reject', $s) }}" method="post" class="ca-inline"
                                      onsubmit="return confirm('Reject this registration?');">
                                    @csrf
                                    <input type="hidden" name="rejection_note" value="">
                                    <button type="submit" class="ca-btn ca-btn-reject">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        Reject
                                    </button>
                                </form>
                            </div>
                        @elseif($s->status === 'provisioned' && $s->tenant_id)
                            <a href="{{ route('admin.tenants.show', $s->tenant_id) }}" class="ca-btn ca-btn-link">View tenant →</a>
                        @elseif($s->status === 'rejected' && $s->rejection_note)
                            <span class="ca-table-muted" style="font-style:italic;">{{ \Illuminate\Support\Str::limit($s->rejection_note, 40) }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="ca-table-empty">No signups in this view.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="ca-pagination">
    {{ $signups->links() }}
</div>
@endsection
