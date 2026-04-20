@extends('layouts.app')

@section('title', 'Tenant Migration Status')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title">Tenant migration status</h1>
    </div>
    <form action="{{ request()->url() }}" method="GET" style="margin: 0;">
        <button type="submit" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh
        </button>
    </form>
</div>

<!-- Status Alert -->
@if($pendingCount > 0)
    <div style="background: #FFFBEB; border: 1px solid #FCD34D; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
        <svg width="20" height="20" fill="none" stroke="#D97706" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span style="color: #92400E; font-size: 13px; font-weight: 500;">Warning: {{ $pendingCount }} pending {{ $pendingCount === 1 ? 'migration' : 'migrations' }} detected across tenants.</span>
    </div>
@else
    <div style="background: #F0FDF4; border: 1px solid #86EFAC; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
        <svg width="20" height="20" fill="none" stroke="#22C55E" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span style="color: #166534; font-size: 13px; font-weight: 500;">All tenant migrations are up to date.</span>
    </div>
@endif

<!-- Migrations Table -->
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tenant ID</th>
                    <th>Migration file</th>
                    <th style="width: 100px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @if(empty($rows))
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 24px 14px; color: #8B949E;">
                            No migration data available. Run migrations to see status.
                        </td>
                    </tr>
                @else
                    @foreach($rows as $row)
                        <tr>
                            <td style="color: #8B949E; font-family: 'DM Mono', monospace;">{{ $row['tenant'] }}</td>
                            <td style="font-family: 'DM Mono', monospace; font-size: 12px;">{{ $row['file'] }}</td>
                            <td>
                                @if(trim($row['status']) === 'Ran' || strpos(strtolower($row['status']), 'ran') !== false)
                                    <span style="display: inline-flex; align-items: center; gap: 6px; color: #34D399;">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Ran
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 6px; color: #F85149;">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                        Pending
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
