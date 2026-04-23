@extends('layouts.central-admin')

@section('title', 'RBAC Audit - ' . $tenant->name)
@section('topbar-title', 'RBAC Audit Log: ' . $tenant->name)

@section('content')
<a href="{{ route('admin.rbac.show', $tenant) }}" class="ca-back">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="19" y1="12" x2="5" y2="12"/>
        <polyline points="12 19 5 12 12 5"/>
    </svg>
    Back to RBAC Overview
</a>

<div class="ca-page-head">
    <h1 class="ca-page-title">RBAC Change History</h1>
    <p class="ca-page-desc">View all role and permission modifications for {{ $tenant->name }}.</p>
</div>

{{-- ═══ TABLE ═══ --}}
<div class="ca-table-card">
    <table class="ca-table">
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>Administrator</th>
                <th>Action</th>
                <th>Description</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($history as $log)
                <tr>
                    <td class="ca-table-muted">{{ $log->created_at->format('M j, Y H:i') }}</td>
                    <td>{{ $log->changedByAdmin->name ?? 'System' }}</td>
                    <td>
                        @php
                            $actionColor = match($log->action) {
                                'created' => 'ca-badge-ok',
                                'deleted' => 'ca-badge-no',
                                'updated' => 'ca-badge-warn',
                                default => 'ca-badge'
                            };
                        @endphp
                        <span class="ca-badge {{ $actionColor }}">{{ ucfirst($log->action) }}</span>
                    </td>
                    <td class="ca-table-strong">{{ $log->change_description }}</td>
                    <td>
                        @if($log->old_values || $log->new_values)
                            <details style="cursor: pointer;">
                                <summary style="font-size: 12px; color: #2563eb;">View details</summary>
                                <div style="margin-top: 0.5rem; font-family: monospace; font-size: 11px; background: #f3f4f6; padding: 0.5rem; border-radius: 3px; max-height: 150px; overflow-y: auto;">
                                    @if($log->old_values)
                                        <div><strong>Before:</strong> {{ json_encode($log->old_values) }}</div>
                                    @endif
                                    @if($log->new_values)
                                        <div><strong>After:</strong> {{ json_encode($log->new_values) }}</div>
                                    @endif
                                </div>
                            </details>
                        @else
                            <span style="color: #9ca3af; font-size: 12px;">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="ca-table-empty">No changes recorded for this tenant.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="ca-pagination">
    {{ $history->links() }}
</div>

<style>
.ca-badge-ok { background: #d1fae5; color: #065f46; }
.ca-badge-no { background: #fee2e2; color: #991b1b; }
.ca-badge-warn { background: #fef3c7; color: #92400e; }
</style>
@endsection

