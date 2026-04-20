{{-- resources/views/partials/topbar.blade.php --}}
@php
    $church = auth()->user()->church ?? null;
    $planBadgeClass = match($church->subscription_plan ?? 'free') {
        'premium'  => 'badge-green',
        'standard' => 'badge-gold',
        'basic'    => 'badge-purple',
        default    => 'badge-blue',
    };
    $planLabel = strtoupper($church->subscription_plan ?? 'FREE');
@endphp

<header class="topbar">
    {{-- Search --}}
    <div class="topbar-search">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6E7681" stroke-width="1.8">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" placeholder="Search disciples, lessons, programs…" id="globalSearch" autocomplete="off">
    </div>

    <div class="topbar-actions">
        {{-- Plan badge --}}
        <span class="badge {{ $planBadgeClass }}">{{ $planLabel }}</span>

        {{-- Notifications Dropdown --}}
        <div x-data="{ showNotifications: false }" style="position:relative;">
            <button @click="showNotifications = !showNotifications" class="btn btn-ghost btn-icon" title="Notifications">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/>
                </svg>
            </button>
            
            {{-- Unread indicator --}}
            @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
            @if($unreadCount > 0)
                <div style="position:absolute;top:4px;right:4px;width:8px;height:8px;border-radius:50%;background:var(--red);border:2px solid var(--surface);"></div>
            @endif

            {{-- Dropdown menu --}}
            <div @click.outside="showNotifications = false" x-show="showNotifications" x-transition style="position:fixed;right:24px;top:calc(var(--topbar-h) + 8px);width:360px;background:#161B22;border:1px solid #30363D;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,0.5);z-index:1000;display:flex;flex-direction:column;max-height:480px;">
                {{-- Header --}}
                <div style="padding:14px 16px;border-bottom:1px solid #30363D;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:14px;font-weight:600;color:#E6EDF3;">Notifications</span>
                    @if($unreadCount > 0)
                        <form action="{{ route('notifications.read-all') }}" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" style="background:none;border:none;color:#58A6FF;cursor:pointer;font-size:11px;font-weight:600;padding:0;">Mark all read</button>
                        </form>
                    @endif
                </div>

                {{-- Notifications list --}}
                @php $notifications = auth()->user()->notifications()->latest()->take(5)->get(); @endphp
                <div style="flex:1;overflow-y:auto;">
                    @if($notifications->isEmpty())
                        <div style="padding:20px;text-align:center;color:#8B949E;font-size:13px;">
                            No notifications yet
                        </div>
                    @else
                        @foreach($notifications as $notification)
                            @php $data = $notification->data; @endphp
                            <a href="{{ $data['url'] ?? '#' }}" @click="showNotifications = false" style="display:flex;gap:12px;padding:12px 16px;border-bottom:1px solid #30363D;text-decoration:none;color:inherit;transition:background 0.2s;align-items:flex-start;">
                                <div style="width:8px;height:8px;border-radius:50%;background:#3B82F6;flex-shrink:0;margin-top:4px;"></div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:12px;font-weight:500;color:#E6EDF3;">{{ $data['title'] ?? 'Notification' }}</div>
                                    <div style="font-size:11px;color:#8B949E;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ substr($data['message'] ?? '', 0, 50) }}</div>
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>

                {{-- Footer --}}
                <div style="padding:12px 16px;border-top:1px solid #30363D;">
                    <a href="{{ route('notifications.index') }}" @click="showNotifications = false" style="display:block;text-align:center;font-size:12px;font-weight:500;color:#58A6FF;text-decoration:none;">View all notifications</a>
                </div>
            </div>
        </div>

        {{-- Page breadcrumb / title --}}
        <div style="height:24px;width:1px;background:var(--border);"></div>
        <div style="font-size:13px;color:var(--text-muted);">
            {{ $church->name ?? 'Church' }}
        </div>
    </div>
</header>
