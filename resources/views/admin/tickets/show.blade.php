@extends('layouts.central-admin')

@section('title', 'Ticket: ' . $ticket->subject)
@section('topbar-title', 'Ticket Details')

@section('content')
<div class="ca-page-head">
    <div>
        <h1 class="ca-page-title">{{ $ticket->subject }}</h1>
        <p class="ca-page-desc">
            Submitted by {{ $ticket->author_name ?? 'Unknown' }}
            ({{ $ticket->author_email ?? 'N/A' }})
            · {{ $ticket->tenant->name ?? 'Unknown Church' }}
            · {{ $ticket->created_at->format('M j, Y H:i') }}
        </p>
    </div>
    <div style="display:flex;gap:8px;align-items:center;">
        @php
            $statusClass = match($ticket->status) {
                'open' => 'ca-pill-pending',
                'in_progress' => 'ca-pill-amber',
                'resolved' => 'ca-pill-ok',
                'closed' => 'ca-pill-gray',
                default => 'ca-pill-gray'
            };
        @endphp
        <span class="ca-pill {{ $statusClass }}">
            <span class="ca-pill-dot"></span> {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
        </span>
        <span class="ca-badge ca-badge-{{ strtolower($ticket->priority) }}">
            {{ ucfirst($ticket->priority) }}
        </span>
    </div>
</div>

{{-- ═══ ORIGINAL MESSAGE ═══ --}}
<div class="ca-card" style="margin-bottom:24px;">
    <div style="padding:20px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
            <div style="width:32px;height:32px;border-radius:50%;background:#E0E7FF;color:#4338CA;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:13px;">
                {{ strtoupper(substr($ticket->author_name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <div style="font-weight:600;font-size:14px;color:#1f2937;">{{ $ticket->author_name ?? 'Unknown' }}</div>
                <div style="font-size:12px;color:#6b7280;">{{ $ticket->created_at->diffForHumans() }}</div>
            </div>
        </div>
        <div style="font-size:14px;color:#374151;line-height:1.7;white-space:pre-wrap;">{{ $ticket->message }}</div>
    </div>
</div>

{{-- ═══ REPLIES ═══ --}}
@if($ticket->replies->isNotEmpty())
<div style="margin-bottom:24px;">
    <h3 style="font-size:14px;font-weight:600;color:#374151;margin-bottom:12px;">Replies ({{ $ticket->replies->count() }})</h3>
    @foreach($ticket->replies as $reply)
    @php $isAdmin = $reply->isAdminReply(); @endphp
    <div class="ca-card" style="margin-bottom:12px;border-left:3px solid {{ $isAdmin ? '#2563EB' : '#10B981' }};">
        <div style="padding:16px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                <div style="width:28px;height:28px;border-radius:50%;background:{{ $isAdmin ? '#DBEAFE' : '#D1FAE5' }};color:{{ $isAdmin ? '#2563EB' : '#059669' }};display:flex;align-items:center;justify-content:center;font-weight:600;font-size:11px;">
                    {{ $isAdmin ? 'A' : 'U' }}
                </div>
                <div>
                    <div style="font-weight:500;font-size:13px;color:#1f2937;">
                        @if($isAdmin)
                            {{ $reply->admin->name ?? 'Support Team' }}
                            <span style="background:#DBEAFE;color:#2563EB;font-size:10px;padding:2px 6px;border-radius:4px;margin-left:6px;font-weight:600;">ADMIN</span>
                        @else
                            {{ $ticket->author_name ?? 'User' }}
                        @endif
                    </div>
                    <div style="font-size:11px;color:#9ca3af;">{{ $reply->created_at->diffForHumans() }}</div>
                </div>
            </div>
            <div style="font-size:13px;color:#374151;line-height:1.6;white-space:pre-wrap;">{{ $reply->message }}</div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ═══ REPLY FORM ═══ --}}
@if($ticket->status !== 'closed')
<div class="ca-card">
    <div style="padding:20px;">
        <h3 style="font-size:14px;font-weight:600;color:#374151;margin-bottom:12px;">Send Reply</h3>
        <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST">
            @csrf
            <div style="margin-bottom:12px;">
                <textarea name="message" rows="4" required minlength="5" placeholder="Type your reply..."
                    style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;font-family:inherit;resize:vertical;">{{ old('message') }}</textarea>
                @error('message')
                    <span style="color:#dc2626;font-size:12px;">{{ $message }}</span>
                @enderror
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="ca-btn ca-btn-approve">Send Reply</button>
            </div>
        </form>
        <form action="{{ route('admin.tickets.close', $ticket) }}" method="POST" style="margin-top:12px;" onsubmit="return confirm('Close this ticket?')">
            @csrf @method('PATCH')
            <button type="submit" class="ca-btn ca-btn-reject">Close Ticket</button>
        </form>
    </div>
</div>
@else
<div class="ca-card">
    <div style="padding:20px;text-align:center;color:#6b7280;font-size:14px;">
        This ticket has been closed.
    </div>
</div>
@endif

<div style="margin-top:16px;">
    <a href="{{ route('admin.tickets.index') }}" class="ca-btn ca-btn-ghost">← Back to Tickets</a>
</div>

<style>
.ca-card { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
</style>
@endsection
