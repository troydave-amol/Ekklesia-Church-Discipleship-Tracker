{{-- resources/views/partials/sidebar.blade.php --}}
@php
    $user   = auth()->user();
    $church = $user->church ?? null;
    $rawRole = $user->role;

    $navFor = function (string $key) use ($rawRole, $user): bool {
        if ($user->isChurchAdmin()) {
            return $key === 'church_admin';
        }
        if ($user->isRestrictedLeader()) {
            return $key === 'leader';
        }

        return $key === 'disciple';
    };

    $navGroups = [
        'church_admin' => [
            'Overview' => [
                ['route' => 'tenant.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
            ],
            'Church' => [
                ['route' => 'programs.index',     'label' => 'Programs',    'icon' => 'cross'],
                ['route' => 'lessons.index',      'label' => 'Lessons',     'icon' => 'book'],
                ['route' => 'users.index',        'label' => 'Members',     'icon' => 'users'],
            ],
            'Tracking' => [
                ['route' => 'attendance.index',   'label' => 'Attendance',  'icon' => 'calendar'],
                ['route' => 'devotionals.index',  'label' => 'Devotionals', 'icon' => 'heart'],
            ],
            'Analytics' => [
                ['route' => 'reports.index',      'label' => 'Reports',     'icon' => 'chart'],
            ],
            'Support' => [
                ['route' => 'support.index',      'label' => 'Support',     'icon' => 'message'],
                ['route' => 'support.admin',      'label' => 'All Tickets', 'icon' => 'inbox'],
                ['route' => 'announcements.index', 'label' => 'Announcements', 'icon' => 'bell'],
            ],
            'System' => [
                ['route' => 'system.info',        'label' => 'System Info',    'icon' => 'info'],
                ['route' => 'system.migrations',  'label' => 'Migrations',     'icon' => 'database'],
                ['route' => 'system.changelog',   'label' => 'Changelog',      'icon' => 'history'],
            ],
            'Admin' => [
                ['route' => 'settings.church',    'label' => 'Settings',    'icon' => 'settings'],
            ],
        ],
        'leader' => [
            'Overview' => [
                ['route' => 'tenant.dashboard',           'label' => 'Dashboard',    'icon' => 'home'],
            ],
            'My Work' => [
                ['route' => 'attendance.index',   'label' => 'Attendance',   'icon' => 'calendar'],
                ['route' => 'devotionals.index',  'label' => 'Devotionals',  'icon' => 'heart'],
                ['route' => 'users.disciples',    'label' => 'My Disciples', 'icon' => 'users'],
            ],
            'Support' => [
                ['route' => 'support.index',      'label' => 'Support',     'icon' => 'message'],
                ['route' => 'announcements.index', 'label' => 'Announcements', 'icon' => 'bell'],
            ],
        ],
        'disciple' => [
            'My Journey' => [
                ['route' => 'tenant.dashboard',           'label' => 'Dashboard',     'icon' => 'home'],
                ['route' => 'lessons.my',          'label' => 'My Lessons',    'icon' => 'book'],
                ['route' => 'devotionals.mine',   'label' => 'Devotionals',  'icon' => 'heart'],
            ],
            'Support' => [
                ['route' => 'support.index',      'label' => 'Support',     'icon' => 'message'],
                ['route' => 'announcements.index', 'label' => 'Announcements', 'icon' => 'bell'],
            ],
        ],
    ];

    if ($navFor('church_admin')) {
        $items     = $navGroups['church_admin'];
        $roleLabel = 'Church Admin';
        $roleColor = '#D4A853';
    } elseif ($navFor('leader')) {
        $items     = $navGroups['leader'];
        $roleLabel = 'Leader';
        $roleColor = '#58A6FF';
    } else {
        $items     = $navGroups['disciple'];
        $roleLabel = 'Disciple';
        $roleColor = '#BC8CFF';
    }
@endphp

<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="brand-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0D1117" stroke-width="2">
                <line x1="12" y1="2" x2="12" y2="22"/><line x1="2" y1="9" x2="22" y2="9"/>
            </svg>
        </div>
        <div class="brand-text">
            <strong>Discipleship</strong>
            <span>TRACKER</span>
        </div>
    </div>

    {{-- Role chip --}}
    <div class="sidebar-role">
        <div class="role-chip" style="border-color:{{ $roleColor }}33; background:{{ $roleColor }}11;">
            <div class="role-dot" style="background:{{ $roleColor }};"></div>
            <span class="role-label" style="color:{{ $roleColor }};">{{ $roleLabel }}</span>
            <span class="role-church">{{ Str::limit($church->name ?? 'Church', 14) }}</span>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">
        @foreach($items as $groupLabel => $links)
            <div class="nav-section-label">{{ $groupLabel }}</div>
            @foreach($links as $item)
                @php
                    $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*');
                    $href = rescue(fn () => route($item['route']), '#', false);
                @endphp
                <a href="{{ $href }}" class="nav-item {{ $isActive ? 'active' : '' }}">
                    @include('partials.icon', ['name' => $item['icon'], 'size' => 17, 'color' => $isActive ? '#D4A853' : '#8B949E'])
                    <span>{{ $item['label'] }}</span>
                    @if(isset($item['badge']))
                        <span class="badge-count">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        @endforeach
    </nav>

    {{-- User row --}}
    <div class="sidebar-user" style="padding: 12px 10px;">
        <div class="profile-dropdown-wrapper" id="profileDropdownWrapper">

            <div class="profile-dropdown" id="profileDropdown">
                <div class="pd-header">
                    <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                        @php $initials = strtoupper(substr($user->first_name ?? 'U', 0, 1) . substr($user->last_name ?? '', 0, 1)); @endphp
                        <div class="avatar avatar-sm" style="flex-shrink: 0; width: 32px; height: 32px; border-radius: 50%; background:{{ $roleColor }}22; border:1px solid {{ $roleColor }}44; color:{{ $roleColor }};">
                            {{ $initials }}
                        </div>
                        <div style="display: flex; flex-direction: column; justify-content: center; min-width: 0; gap: 3px;">
                            <span style="font-size: 13.5px; font-weight: 500; color: #ececf1; line-height: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $user->first_name ?? 'User' }} {{ $user->last_name ?? '' }}</span>
                            @if($church)
                            <span style="font-size: 11.5px; font-weight: 400; color: #9ca3af; line-height: 1;">
                                {{ ucfirst(strtolower($church->subscription_plan ?? 'Free')) }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pd-divider"></div>

                <div class="pd-item-wrapper">
                    <a href="{{ route('users.show', $user->id) }}" class="pd-item">
                        @include('partials.icon',['name'=>'user','size'=>15])
                        <span>My Profile</span>
                    </a>
                </div>

                @if($user->isChurchAdmin())
                <div class="pd-item-wrapper">
                    <a href="{{ route('settings.church') }}" class="pd-item">
                        @include('partials.icon',['name'=>'settings','size'=>15])
                        <span>Church Settings</span>
                    </a>
                </div>

                <div class="pd-item-wrapper">
                    <a href="{{ route('settings.church') }}#subscription" class="pd-item">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <span>Manage Subscription</span>
                    </a>
                </div>
                @endif

                <div class="pd-divider"></div>

                <div class="pd-item-wrapper">
                    <div class="pd-item" style="justify-content: space-between;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            <span>Help & Support</span>
                        </div>
                        @include('partials.icon',['name'=>'arrow-right','size'=>14])
                    </div>
                </div>

                <div class="pd-item-wrapper">
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="pd-item" style="width:100%; border:none; background:transparent; font-family:inherit; outline:none;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            <span>Log out</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="user-row" id="profileTrigger" style="display: flex; align-items: center; gap: 10px; padding: 6px 8px; border-radius: 8px; cursor: pointer;">
                <div class="avatar avatar-sm" style="flex-shrink: 0; width: 32px; height: 32px; border-radius: 50%; background:{{ $roleColor }}22; border:1px solid {{ $roleColor }}44; color:{{ $roleColor }};">
                    {{ $initials }}
                </div>

                <div class="user-info" style="display: flex; flex-direction: column; justify-content: center; min-width: 0; flex: 1; gap: 3px;">
                    <span class="user-name" style="display: block; font-size: 13.5px; font-weight: 500; color: var(--text); line-height: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $user->first_name ?? 'User' }} {{ $user->last_name ?? '' }}</span>
                    @if($church)
                        <span style="display: block; font-size: 11.5px; font-weight: 400; color: var(--text-muted); opacity: 0.85; line-height: 1;">
                            {{ ucfirst(strtolower($church->subscription_plan ?? 'Free')) }}
                        </span>
                    @endif
                </div>

                @if($user->isChurchAdmin())
                <a href="{{ route('settings.church') }}#subscription" class="btn btn-ghost btn-sm" style="flex-shrink: 0; padding: 4px 12px; height: auto; border-radius: 9999px; font-size: 11.5px; font-weight: 600; color: var(--text); background: transparent; border-color: var(--border);" onclick="event.stopPropagation();">Upgrade</a>
                @endif
            </div>
        </div>
    </div>
</aside>
