<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Central Admin') — Ekklesia</title>
    @vite(['resources/css/admin.css'])
    @stack('styles')
</head>
<body class="ca-body">
<div class="ca-shell">

    {{-- ═══ SIDEBAR ═══ --}}
    <aside class="ca-sidebar">

        <div class="ca-brand">
            <div class="ca-brand-name">Ekk<em>l</em>esia</div>
            <div class="ca-brand-tag">Control Plane</div>
        </div>

        <nav class="ca-nav">
            <div class="ca-nav-group">
                <div class="ca-nav-label">Overview</div>
                <a href="{{ route('admin.dashboard') }}"
                   class="ca-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="ca-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                    </svg>
                    Monitoring
                </a>
            </div>

            <div class="ca-nav-group">
                <div class="ca-nav-label">Manage</div>
                <a href="{{ route('admin.signups.index') }}"
                   class="ca-nav-link {{ request()->routeIs('admin.signups.*') ? 'active' : '' }}">
                    <svg class="ca-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4-4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <line x1="19" y1="8" x2="19" y2="14"/>
                        <line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>
                    Approvals
                </a>
                <a href="{{ route('admin.tenants.index') }}"
                   class="ca-nav-link {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}">
                    <svg class="ca-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Tenants
                </a>
            </div>

            <div class="ca-nav-group">
                <div class="ca-nav-label">Support & Content</div>
                <a href="{{ route('admin.tickets.index') }}"
                   class="ca-nav-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
                    <svg class="ca-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                    </svg>
                    Support Tickets
                </a>
                <a href="{{ route('admin.announcements.index') }}"
                   class="ca-nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                    <svg class="ca-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                        <line x1="9" y1="10" x2="15" y2="10"/>
                        <line x1="9" y1="14" x2="15" y2="14"/>
                    </svg>
                    Announcements
                </a>
            </div>

            <div class="ca-nav-group">
                <div class="ca-nav-label">Administration</div>
                <a href="{{ route('admin.users.index') }}"
                   class="ca-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <svg class="ca-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                        <path d="M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                    Admin Staff
                </a>
            </div>
        </nav>

        <div class="ca-sidebar-foot">
            <a href="{{ route('home') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                    <polyline points="15 3 21 3 21 9"/>
                    <line x1="10" y1="14" x2="21" y2="3"/>
                </svg>
                Public Site
            </a>
        </div>

    </aside>

    {{-- ═══ MAIN ═══ --}}
    <div class="ca-main">

        <header class="ca-topbar">
            <div class="ca-topbar-left">
                <span class="ca-topbar-title">@yield('topbar-title', 'Dashboard')</span>
            </div>
            <div class="ca-topbar-right">
                {{ now()->format('l, M j') }}
            </div>
        </header>

        <div class="ca-content ca-fade-up">
            @if(session('success'))
                <div class="ca-flash ca-flash-ok">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="ca-flash ca-flash-err">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </div>

    </div>

</div>

<script>
/* Auto-dismiss flash messages */
document.querySelectorAll('.ca-flash').forEach(el => {
    setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity .4s'; }, 4000);
    setTimeout(() => el.remove(), 4400);
});
</script>
@stack('scripts')
</body>
</html>
