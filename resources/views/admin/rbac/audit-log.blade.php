@extends('layouts.central-admin')

@section('title', 'RBAC Audit - ' . $tenant->name)
@section('topbar-title', 'RBAC Audit Log: ' . $tenant->name)

@section('content')
<div class="ca-page-head">
    <h1 class="ca-page-title">RBAC Change History</h1>
    <p class="ca-page-desc">View all role and permission modifications for {{ $tenant->name }}.</p>
    <a href="{{ route('admin.rbac.show', $tenant) }}" class="ca-btn ca-btn-ghost">← Back to RBAC</a>
</div>

{{-- ═══ TABLE ═══ --}}
<div class="ca-table-card">
    <table class="ca-table">
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>Administrator</th>
                <th>Action</th>
                <th>Role</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($auditLog as $log)
                <tr>
                    <td class="ca-table-muted">{{ $log->created_at->format('M j, Y H:i') }}</td>
                    <td>{{ $log->admin_name ?? 'System' }}</td>
                    <td>
                        @php
                            $actionColor = match($log->action ?? 'updated') {
                                'created' => 'ca-badge-ok',
                                'deleted' => 'ca-badge-no',
                                'updated' => 'ca-badge-warn',
                                default => 'ca-badge'
                            };
                            $actionLabel = match($log->action ?? 'updated') {
                                'created' => 'Created',
                                'deleted' => 'Deleted',
                                'updated' => 'Updated',
                                default => ucfirst($log->action ?? 'updated')
                            };
                        @endphp
                        <span class="ca-badge {{ $actionColor }}">{{ $actionLabel }}</span>
                    </td>
                    <td class="ca-table-strong">{{ $log->role_name ?? '—' }}</td>
                    <td>
                        @if($log->changes)
                            <details style="cursor: pointer;">
                                <summary style="font-size: 12px; color: #2563eb;">View details</summary>
                                <div style="margin-top: 0.5rem; font-family: monospace; font-size: 11px; background: #f3f4f6; padding: 0.5rem; border-radius: 3px; max-height: 150px; overflow-y: auto;">
                                    <pre>{{ $log->changes }}</pre>
                                </div>
                            </details>
                        @else
                            <span style="color: #9ca3af; font-size: 12px;">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="ca-table-empty">No changes recorded.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="ca-pagination">
    {{ $auditLog->links() }}
</div>

<style>
.ca-badge-ok { background: #d1fae5; color: #065f46; }
.ca-badge-no { background: #fee2e2; color: #991b1b; }
.ca-badge-warn { background: #fef3c7; color: #92400e; }
pre { margin: 0; white-space: pre-wrap; word-wrap: break-word; }
</style>
@endsection
