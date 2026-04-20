@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="page-header" style="margin-bottom: 24px;">
    <h1 class="page-title">Announcements</h1>
</div>

<!-- Unread Count Banner -->
@php
    $unreadCount = auth()->user()->notifications()
        ->where('type', 'App\\Notifications\\AnnouncementNotification')
        ->whereNull('read_at')
        ->count();
@endphp

@if($unreadCount > 0)
    <div style="background: #E0F2FE; border: 1px solid #BAE6FD; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
        <svg width="20" height="20" fill="none" stroke="#0369A1" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span style="color: #0369A1; font-size: 13px; font-weight: 500;">{{ $unreadCount }} unread {{ $unreadCount === 1 ? 'announcement' : 'announcements' }}</span>
    </div>
@endif

<!-- Announcements List -->
@php
    $announcements = auth()->user()->notifications()
        ->where('type', 'App\\Notifications\\AnnouncementNotification')
        ->orderBy('created_at', 'desc')
        ->get();
@endphp

@if($announcements->isEmpty())
    <div style="text-align: center; padding: 40px 20px;">
        <p style="font-size: 16px; color: #8B949E;">No announcements at this time.</p>
    </div>
@else
    <div style="display: flex; flex-direction: column; gap: 16px;">
        @foreach($announcements as $announcement)
            @php
                $data = $announcement->data;
                $typeColors = [
                    'info' => ['border' => '#3B82F6', 'bg' => '#EFF6FF'],
                    'warning' => ['border' => '#F59E0B', 'bg' => '#FFFBEB'],
                    'maintenance' => ['border' => '#EF4444', 'bg' => '#FEF2F2'],
                ];
                $colors = $typeColors[$data['type'] ?? 'info'] ?? $typeColors['info'];
                $isUnread = is_null($announcement->read_at);
            @endphp
            <div x-data="{ expanded: false }" style="background: white; border-left: 4px solid {{ $colors['border'] }}; border-radius: 8px; padding: 16px; @if($isUnread)border: 1px solid #E5E7EB; box-shadow: 0 0 0 2px {{ $colors['bg'] }};@else opacity: 0.75;@endif">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <h3 style="font-size: 15px; font-weight: 600; color: #1F2937; @if($isUnread)font-weight: 700;@endif">{{ $data['title'] ?? 'Announcement' }}</h3>
                            @if($isUnread)
                                <span style="display: inline-flex; align-items: center; gap: 4px;">
                                    <svg width="8" height="8" fill="#3B82F6" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="4" />
                                    </svg>
                                </span>
                            @endif
                        </div>

                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                            @php
                                $typeLabel = [
                                    'info' => 'Info',
                                    'warning' => 'Warning',
                                    'maintenance' => 'Maintenance',
                                ];
                                $typeBgColors = [
                                    'info' => 'background: #DBEAFE; color: #1E40AF;',
                                    'warning' => 'background: #FEF3C7; color: #92400E;',
                                    'maintenance' => 'background: #FEE2E2; color: #991B1B;',
                                ];
                            @endphp
                            <span style="display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 10px; font-weight: 700; {{ $typeBgColors[$data['type'] ?? 'info'] ?? $typeBgColors['info'] }}">
                                {{ $typeLabel[$data['type'] ?? 'info'] ?? 'Info' }}
                            </span>
                            <span style="font-size: 11px; color: #6B7280;">{{ $announcement->created_at->format('M d, Y H:i') }}</span>
                        </div>

                        <p x-show="!expanded" style="color: #4B5563; font-size: 13px; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $data['message'] ?? '' }}
                        </p>

                        <p x-show="expanded" style="color: #4B5563; font-size: 13px; line-height: 1.5;">
                            {{ $data['message'] ?? '' }}
                        </p>

                        @if(strlen($data['message'] ?? '') > 200)
                            <button @click="expanded = !expanded" style="margin-top: 8px; background: none; border: none; color: #3B82F6; cursor: pointer; font-size: 13px; font-weight: 500; padding: 0;">
                                <span x-show="!expanded">Read more</span>
                                <span x-show="expanded">Read less</span>
                            </button>
                        @endif
                    </div>

                    @if($isUnread)
                        <button x-data @click="fetch('{{ route('notifications.read', $announcement->id) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content } }).then(() => location.reload());" style="background: none; border: none; color: #3B82F6; cursor: pointer; font-size: 13px; font-weight: 500; padding: 0; white-space: nowrap;">Mark as read</button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
