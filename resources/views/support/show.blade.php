@extends('layouts.app')

@section('title', $ticket->subject)

@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('support.index') }}" style="color: #58A6FF; text-decoration: none; display: flex; align-items: center; gap: 6px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to tickets
    </a>
</div>

<div style="display: grid; grid-template-columns: 1fr 320px; gap: 24px;">
    <!-- Left: Ticket Thread -->
    <div>
        <h1 class="page-title" style="margin-bottom: 24px;">{{ $ticket->subject }}</h1>

        <!-- Original Message -->
        <div class="card" style="margin-bottom: 20px;">
            <div style="padding: 20px; border-bottom: 1px solid #30363D; display: flex; gap: 16px; align-items: flex-start;">
                <div class="avatar avatar-md" style="background: linear-gradient(135deg, #D4A853, #E8C47A); color: #0D1117; font-weight: 700;">
                    {{ strtoupper(substr($ticket->author_name ?? $ticket->user?->first_name ?? 'U', 0, 2)) }}
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <span style="font-weight: 500;">{{ $ticket->author_name ?? $ticket->user?->fullName() ?? 'Unknown' }}</span>
                        <span style="color: #8B949E; font-size: 12px;">{{ ucfirst(str_replace('_', ' ', $ticket->user?->role ?? '')) }}</span>
                    </div>
                    <span style="color: #8B949E; font-size: 12px;">{{ $ticket->created_at->format('M d, Y \a\t H:i') }}</span>
                </div>
            </div>
            <div style="padding: 20px; color: #E6EDF3;">
                {{ $ticket->message }}
            </div>
        </div>

        <!-- Replies -->
        @foreach($ticket->replies as $reply)
            @php $isAdmin = $reply->isAdminReply(); @endphp
            <div class="card" style="margin-bottom: 16px; @if(!$isAdmin && $reply->user_id === auth()->id()) margin-left: 40px; @endif">
                <div style="padding: 16px; border-bottom: 1px solid #30363D; display: flex; gap: 12px; align-items: flex-start;">
                    <div class="avatar avatar-sm" style="background: {{ $isAdmin ? 'linear-gradient(135deg, #60A5FA, #93C5FD)' : 'linear-gradient(135deg, #D4A853, #E8C47A)' }}; color: #0D1117; font-weight: 700; flex-shrink: 0;">
                        {{ $isAdmin ? 'AD' : strtoupper(substr($ticket->author_name ?? 'U', 0, 2)) }}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="font-weight: 500;">
                                @if($isAdmin)
                                    {{ $reply->admin->name ?? 'Support Team' }}
                                @else
                                    {{ $ticket->author_name ?? 'User' }}
                                @endif
                            </span>
                            @if($isAdmin)
                                <span style="display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 700; background: rgba(96, 165, 250, 0.1); color: #60A5FA;">ADMIN</span>
                            @endif
                        </div>
                        <span style="color: #8B949E; font-size: 12px;">{{ $reply->created_at->format('M d, Y \a\t H:i') }}</span>
                    </div>
                </div>
                <div style="padding: 16px; color: #E6EDF3;">
                    {{ $reply->message }}
                </div>
            </div>
        @endforeach

        <!-- Reply Form -->
        @if($ticket->status !== 'closed')
            <form action="{{ route('support.reply', $ticket) }}" method="POST" style="margin-top: 24px;">
                @csrf
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="field-label">Write a reply</label>
                    <textarea name="message" rows="4" placeholder="Type your message here..." style="width: 100%; background: #0D1117; border: 1px solid #30363D; border-radius: 8px; padding: 12px; color: #E6EDF3; font-family: inherit; font-size: 13px; resize: vertical;" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send reply</button>
            </form>
        @else
            <div style="margin-top: 24px; padding: 16px; background: #0D1117; border: 1px solid #30363D; border-radius: 8px; text-align: center;">
                <p style="color: #8B949E;">This ticket has been closed.</p>
            </div>
        @endif
    </div>

    <!-- Right: Details Sidebar -->
    <div>
        <div class="card" style="margin-bottom: 16px;">
            <div style="padding: 16px; border-bottom: 1px solid #30363D;">
                <h3 style="font-size: 12px; font-weight: 600; color: #8B949E; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px;">Status</h3>
                @if(auth()->user()->isChurchAdmin())
                    <select onchange="fetch('{{ route('support.updateStatus', $ticket) }}', { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ status: this.value }) }).then(r => r.json()).then(d => { if(d.success) location.reload(); })" style="width: 100%; height: 36px; background: #0D1117; border: 1px solid #30363D; border-radius: 6px; padding: 0 10px; color: #E6EDF3; font-size: 12px;">
                        <option value="open" @if($ticket->status === 'open') selected @endif>Open</option>
                        <option value="in_progress" @if($ticket->status === 'in_progress') selected @endif>In Progress</option>
                        <option value="resolved" @if($ticket->status === 'resolved') selected @endif>Resolved</option>
                        <option value="closed" @if($ticket->status === 'closed') selected @endif>Closed</option>
                    </select>
                @else
                    @php
                        $statusColors = [
                            'open' => ['bg' => '#FCD34D', 'text' => '#1F2937'],
                            'in_progress' => ['bg' => '#60A5FA', 'text' => '#1F2937'],
                            'resolved' => ['bg' => '#34D399', 'text' => '#1F2937'],
                            'closed' => ['bg' => '#9CA3AF', 'text' => '#FFFFFF'],
                        ];
                        $colors = $statusColors[$ticket->status] ?? $statusColors['open'];
                    @endphp
                    <span style="display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: {{ $colors['bg'] }}; color: {{ $colors['text'] }};">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                @endif
            </div>

            <div style="padding: 16px; border-bottom: 1px solid #30363D;">
                <h3 style="font-size: 12px; font-weight: 600; color: #8B949E; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px;">Priority</h3>
                @php
                    $priorityColors = [
                        'high' => ['bg' => '#FEE2E2', 'text' => '#991B1B'],
                        'medium' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
                        'low' => ['bg' => '#F3F4F6', 'text' => '#374151'],
                    ];
                    $pColors = $priorityColors[$ticket->priority] ?? $priorityColors['low'];
                @endphp
                <span style="display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: {{ $pColors['bg'] }}; color: {{ $pColors['text'] }};">
                    {{ ucfirst($ticket->priority) }}
                </span>
            </div>

            <div style="padding: 16px; border-bottom: 1px solid #30363D;">
                <h3 style="font-size: 12px; font-weight: 600; color: #8B949E; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px;">Submitted by</h3>
                <p style="font-size: 13px; color: #E6EDF3;">{{ $ticket->author_name ?? $ticket->user?->fullName() ?? 'Unknown' }}</p>
                <p style="font-size: 11px; color: #8B949E;">{{ $ticket->author_email ?? $ticket->user?->email ?? '' }}</p>
            </div>

            <div style="padding: 16px; border-bottom: 1px solid #30363D;">
                <h3 style="font-size: 12px; font-weight: 600; color: #8B949E; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px;">Created</h3>
                <p style="font-size: 13px; color: #E6EDF3;">{{ $ticket->created_at->format('M d, Y') }}</p>
                <p style="font-size: 11px; color: #8B949E;">{{ $ticket->created_at->format('H:i') }}</p>
            </div>

            <div style="padding: 16px;">
                <h3 style="font-size: 12px; font-weight: 600; color: #8B949E; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px;">Last updated</h3>
                <p style="font-size: 13px; color: #E6EDF3;">{{ $ticket->updated_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
