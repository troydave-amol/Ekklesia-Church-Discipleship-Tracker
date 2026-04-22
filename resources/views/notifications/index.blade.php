@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="page-header" style="margin-bottom: 24px;">
    <h1 class="page-title">Notifications</h1>
</div>

@if($notifications->isEmpty())
    <div style="text-align: center; padding: 40px 20px;">
        <p style="font-size: 16px; color: #8B949E;">No notifications yet.</p>
    </div>
@else
    <div style="display: flex; flex-direction: column; gap: 12px;">
        @foreach($notifications as $notification)
            @php
                $data = $notification->data;
                $icon = ($data['type'] ?? '') === 'ticket' ? 'ticket' : 'bell';
            @endphp
            <a href="{{ $data['url'] ?? '#' }}" style="display: flex; gap: 12px; padding: 16px; background: white; border: 1px solid #E5E7EB; border-radius: 8px; text-decoration: none; color: inherit; transition: all 0.2s;">
                <div style="flex-shrink: 0; width: 8px; height: 8px; border-radius: 50%; background: @if($icon === 'ticket') #9CA3AF @else #3B82F6 @endif; margin-top: 6px;"></div>
                <div style="flex: 1; min-width: 0;">
                    <h3 style="font-size: 14px; font-weight: 600; color: #1F2937; margin-bottom: 2px;">{{ $data['title'] ?? 'Notification' }}</h3>
                    <p style="font-size: 13px; color: #6B7280; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ substr($data['message'] ?? '', 0, 60) }}</p>
                    <span style="font-size: 11px; color: #9CA3AF;">{{ $notification->created_at->diffForHumans() }}</span>
                </div>
            </a>
        @endforeach
    </div>

    <div style="margin-top: 20px;">
        {{ $notifications->links() }}
    </div>
@endif
@endsection
