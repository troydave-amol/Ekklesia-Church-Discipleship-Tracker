@extends('layouts.app')

@section('title', 'All Support Tickets')

@section('content')
<div class="page-header" style="margin-bottom: 24px;">
    <h1 class="page-title">All support tickets</h1>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 20px; padding: 0;">
    <div style="padding: 16px; display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
        <span style="font-size: 12px; font-weight: 600; color: #8B949E; text-transform: uppercase; letter-spacing: 0.08em;">Status:</span>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ request()->url() }}?priority={{ request('priority', 'all') }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; @if(!request('status') || request('status') === 'all') background: #D4A853; color: #0D1117; @else background: #30363D; color: #8B949E; @endif text-decoration: none;">All</a>
            <a href="{{ request()->url() }}?status=open&priority={{ request('priority', 'all') }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; @if(request('status') === 'open') background: #FCD34D; color: #1F2937; @else background: #30363D; color: #8B949E; @endif text-decoration: none;">Open</a>
            <a href="{{ request()->url() }}?status=in_progress&priority={{ request('priority', 'all') }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; @if(request('status') === 'in_progress') background: #60A5FA; color: #1F2937; @else background: #30363D; color: #8B949E; @endif text-decoration: none;">In Progress</a>
            <a href="{{ request()->url() }}?status=resolved&priority={{ request('priority', 'all') }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; @if(request('status') === 'resolved') background: #34D399; color: #1F2937; @else background: #30363D; color: #8B949E; @endif text-decoration: none;">Resolved</a>
            <a href="{{ request()->url() }}?status=closed&priority={{ request('priority', 'all') }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; @if(request('status') === 'closed') background: #9CA3AF; color: #FFFFFF; @else background: #30363D; color: #8B949E; @endif text-decoration: none;">Closed</a>
        </div>

        <span style="font-size: 12px; font-weight: 600; color: #8B949E; text-transform: uppercase; letter-spacing: 0.08em; margin-left: auto;">Priority:</span>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ request()->url() }}?status={{ request('status', 'all') }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; @if(!request('priority') || request('priority') === 'all') background: #D4A853; color: #0D1117; @else background: #30363D; color: #8B949E; @endif text-decoration: none;">All</a>
            <a href="{{ request()->url() }}?status={{ request('status', 'all') }}&priority=high" style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; @if(request('priority') === 'high') background: #FEE2E2; color: #991B1B; @else background: #30363D; color: #8B949E; @endif text-decoration: none;">High</a>
            <a href="{{ request()->url() }}?status={{ request('status', 'all') }}&priority=medium" style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; @if(request('priority') === 'medium') background: #FEF3C7; color: #92400E; @else background: #30363D; color: #8B949E; @endif text-decoration: none;">Medium</a>
            <a href="{{ request()->url() }}?status={{ request('status', 'all') }}&priority=low" style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; @if(request('priority') === 'low') background: #F3F4F6; color: #374151; @else background: #30363D; color: #8B949E; @endif text-decoration: none;">Low</a>
        </div>
    </div>
</div>

<!-- Tickets List -->
@if($tickets->isEmpty())
    <div style="text-align: center; padding: 40px 20px;">
        <p style="font-size: 16px; color: #8B949E;">No support tickets match your filters.</p>
    </div>
@else
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Subject</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 100px;">Priority</th>
                        <th style="width: 150px;">Submitted by</th>
                        <th style="width: 120px;">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        <tr style="cursor: pointer;" onclick="window.location.href = '{{ route('support.show', $ticket) }}';">
                            <td style="color: #8B949E;">#{{ $ticket->id }}</td>
                            <td>
                                <a href="{{ route('support.show', $ticket) }}" style="color: #58A6FF; text-decoration: none;">{{ $ticket->subject }}</a>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'open' => ['bg' => '#FCD34D', 'text' => '#1F2937'],
                                        'in_progress' => ['bg' => '#60A5FA', 'text' => '#1F2937'],
                                        'resolved' => ['bg' => '#34D399', 'text' => '#1F2937'],
                                        'closed' => ['bg' => '#9CA3AF', 'text' => '#FFFFFF'],
                                    ];
                                    $colors = $statusColors[$ticket->status] ?? $statusColors['open'];
                                @endphp
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: {{ $colors['bg'] }}; color: {{ $colors['text'] }};">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $priorityColors = [
                                        'high' => ['bg' => '#FEE2E2', 'text' => '#991B1B'],
                                        'medium' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
                                        'low' => ['bg' => '#F3F4F6', 'text' => '#374151'],
                                    ];
                                    $pColors = $priorityColors[$ticket->priority] ?? $priorityColors['low'];
                                @endphp
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: {{ $pColors['bg'] }}; color: {{ $pColors['text'] }};">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td>{{ $ticket->user->name }}</td>
                            <td style="color: #8B949E;">{{ $ticket->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 20px;">
        {{ $tickets->links() }}
    </div>
@endif
@endsection
