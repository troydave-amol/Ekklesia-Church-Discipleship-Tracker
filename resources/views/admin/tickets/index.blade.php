@extends('layouts.central-admin')

@section('title', 'Support Tickets')
@section('topbar-title', 'Support Tickets')

@section('content')
<div class="ca-page-head">
    <h1 class="ca-page-title">Tenant Support Tickets</h1>
    <p class="ca-page-desc">Support requests from church admins. Review, respond, and resolve.</p>
</div>

{{-- ═══ FILTERS ═══ --}}
<div class="ca-filters">
    <a href="{{ route('admin.tickets.index', ['status' => 'open']) }}"
       class="ca-filter {{ $status === 'open' ? 'active' : '' }}">Open</a>
    <a href="{{ route('admin.tickets.index', ['status' => 'in_progress']) }}"
       class="ca-filter {{ $status === 'in_progress' ? 'active' : '' }}">In Progress</a>
    <a href="{{ route('admin.tickets.index', ['status' => 'resolved']) }}"
       class="ca-filter {{ $status === 'resolved' ? 'active' : '' }}">Resolved</a>
    <a href="{{ route('admin.tickets.index', ['status' => 'closed']) }}"
       class="ca-filter {{ $status === 'closed' ? 'active' : '' }}">Closed</a>
    <a href="{{ route('admin.tickets.index') }}"
       class="ca-filter {{ $status === 'all' ? 'active' : '' }}">All</a>
</div>

{{-- ═══ TABLE ═══ --}}
<div class="ca-table-card">
    <table class="ca-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>From (Church)</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Created</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $t)
                <tr>
                    <td class="ca-table-strong">{{ $t->subject }}</td>
                    <td>
                        <div class="ca-table-strong">{{ $t->tenant->name ?? 'Unknown' }}</div>
                        <div class="ca-table-email">{{ $t->email ?? 'No email' }}</div>
                    </td>
                    <td>
                        <span class="ca-badge ca-badge-{{ strtolower($t->priority ?? 'medium') }}">
                            {{ ucfirst($t->priority ?? 'medium') }}
                        </span>
                    </td>
                    <td>
                        @php
                            $statusClass = match($t->status ?? 'open') {
                                'open' => 'ca-pill-pending',
                                'in_progress' => 'ca-pill-amber',
                                'resolved' => 'ca-pill-ok',
                                'closed' => 'ca-pill-gray',
                                default => 'ca-pill-gray'
                            };
                        @endphp
                        <span class="ca-pill {{ $statusClass }}">
                            <span class="ca-pill-dot"></span> {{ ucfirst(str_replace('_', ' ', $t->status ?? 'open')) }}
                        </span>
                    </td>
                    <td class="ca-table-muted">{{ $t->created_at->format('M j, Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.tickets.show', $t) }}" class="ca-btn ca-btn-link">Details →</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="ca-table-empty">No tickets in this view.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="ca-pagination">
    {{ $tickets->links() }}
</div>
@endsection
