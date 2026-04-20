@extends('layouts.app')

@section('title', 'Support Tickets')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title">Support tickets</h1>
    </div>
    <button class="btn btn-primary" @click="showNewTicketPanel = true">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New ticket
    </button>
</div>

<!-- New Ticket Slide-over Panel -->
<div x-data="{ showNewTicketPanel: false }" style="position: fixed; right: 0; top: 0; width: 100%; height: 100%; pointer-events: none; z-index: 40;">
    <!-- Backdrop -->
    <div @click="showNewTicketPanel = false" x-show="showNewTicketPanel" style="position: fixed; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); pointer-events: auto; z-index: 40;"></div>
    
    <!-- Panel -->
    <div x-show="showNewTicketPanel" style="position: fixed; right: 0; top: 0; height: 100%; width: 100%; max-width: 448px; background: #161B22; border-left: 1px solid #30363D; display: flex; flex-direction: column; pointer-events: auto; z-index: 50; animation: slideIn 0.3s ease;" x-transition>
        <!-- Header -->
        <div style="padding: 20px; border-bottom: 1px solid #30363D; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 18px; font-weight: 600;">Submit a support ticket</h2>
            <button @click="showNewTicketPanel = false" style="background: none; border: none; color: #8B949E; cursor: pointer; font-size: 24px;">
                ×
            </button>
        </div>

        <!-- Form -->
        <form action="{{ route('support.store') }}" method="POST" style="flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            
            <div class="form-group">
                <label class="field-label">Subject *</label>
                <input type="text" name="subject" maxlength="255" placeholder="What is your issue about?" style="width: 100%; height: 38px; background: #0D1117; border: 1px solid #30363D; border-radius: 8px; padding: 0 12px; color: #E6EDF3; font-family: inherit; font-size: 13px;" required>
            </div>

            <div class="form-group">
                <label class="field-label">Priority *</label>
                <select name="priority" style="width: 100%; height: 38px; background: #0D1117; border: 1px solid #30363D; border-radius: 8px; padding: 0 12px; color: #E6EDF3; font-family: inherit; font-size: 13px;">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>
            </div>

            <div class="form-group">
                <label class="field-label">Message *</label>
                <textarea name="message" rows="5" placeholder="Describe your issue..." style="width: 100%; background: #0D1117; border: 1px solid #30363D; border-radius: 8px; padding: 12px; color: #E6EDF3; font-family: inherit; font-size: 13px; resize: vertical;" required></textarea>
            </div>

            <div style="margin-top: auto; display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Submit ticket</button>
                <button type="button" @click="showNewTicketPanel = false" class="btn btn-ghost" style="flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Tickets List -->
@if($tickets->isEmpty())
    <div style="text-align: center; padding: 40px 20px;">
        <p style="font-size: 16px; color: #8B949E; margin-bottom: 16px;">No support tickets yet.</p>
        <button class="btn btn-primary" @click="showNewTicketPanel = true">Submit your first ticket</button>
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
                        @if(auth()->user()->role->slug === 'church_admin')
                            <th style="width: 150px;">Submitted by</th>
                        @endif
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
                            @if(auth()->user()->role->slug === 'church_admin')
                                <td>{{ $ticket->user->name }}</td>
                            @endif
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
