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

        {{-- Notifications --}}
        <div style="position:relative;">
            <button class="btn btn-ghost btn-icon" id="notifBtn" title="Notifications">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/>
                </svg>
            </button>
            {{-- Unread dot --}}
            @if(isset($unreadNotifications) && $unreadNotifications > 0)
            <div style="position:absolute;top:4px;right:4px;width:8px;height:8px;border-radius:50%;background:var(--red);border:2px solid var(--surface);"></div>
            @endif
        </div>

        {{-- Page breadcrumb / title --}}
        <div style="height:24px;width:1px;background:var(--border);"></div>
        <div style="font-size:13px;color:var(--text-muted);">
            {{ $church->name ?? 'Church' }}
        </div>
    </div>
</header>
