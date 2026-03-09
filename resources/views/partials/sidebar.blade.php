{{-- resources/views/partials/sidebar.blade.php --}}
@php
    $role = auth()->user()->role ?? 'disciple';
    $church = auth()->user()->church ?? null;
    $user = auth()->user();

    $navGroups = [
        'pastor' => [
            'Overview' => [
                ['route' => 'dashboard',          'label' => 'Dashboard',   'icon' => 'home'],
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
            'Admin' => [
                ['route' => 'settings.church',    'label' => 'Settings',    'icon' => 'settings'],
            ],
        ],
        'leader' => [
            'Overview' => [
                ['route' => 'dashboard',          'label' => 'Dashboard',   'icon' => 'home'],
            ],
            'My Work' => [
                ['route' => 'attendance.index',   'label' => 'Attendance',  'icon' => 'calendar'],
                ['route' => 'devotionals.index',  'label' => 'Devotionals', 'icon' => 'heart'],
                ['route' => 'users.disciples',    'label' => 'My Disciples','icon' => 'users'],
            ],
        ],
        'disciple' => [
            'My Journey' => [
                ['route' => 'dashboard',          'label' => 'Dashboard',   'icon' => 'home'],
                ['route' => 'lessons.my',         'label' => 'My Lessons',  'icon' => 'book'],
                ['route' => 'devotionals.mine',   'label' => 'Devotionals', 'icon' => 'heart'],
            ],
            'Profile' => [
                ['route' => 'profile.edit',       'label' => 'My Profile',  'icon' => 'user'],
            ],
        ],
    ];

    $items = $navGroups[$role] ?? $navGroups['disciple'];

    $roleColors = ['pastor' => '#D4A853', 'leader' => '#58A6FF', 'disciple' => '#BC8CFF'];
    $roleColor  = $roleColors[$role] ?? '#D4A853';
@endphp

<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="brand-icon">
            {{-- Cross icon --}}
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0D1117" stroke-width="2">
                <line x1="12" y1="2" x2="12" y2="22"/><line x1="2" y1="9" x2="22" y2="9"/>
            </svg>
        </div>
        <div class="brand-text">
            <strong>Discipleship</strong>
            <span>TRACKER</span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle" title="Toggle sidebar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
    </div>

    {{-- Role chip --}}
    <div class="sidebar-role">
        <div class="role-chip" style="border-color:{{ $roleColor }}33; background:{{ $roleColor }}11;">
            <div class="role-dot" style="background:{{ $roleColor }};"></div>
            <span class="role-label" style="color:{{ $roleColor }};">{{ ucfirst($role) }}</span>
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
                    $href = rescue(fn() => route($item['route']), '#', false);
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
    <div class="sidebar-user">
        <div class="user-row">
            @php
                $initials = strtoupper(substr($user->first_name ?? 'U', 0, 1) . substr($user->last_name ?? '', 0, 1));
            @endphp
            <div class="avatar avatar-sm" style="background:{{ $roleColor }}22; border:1.5px solid {{ $roleColor }}44; color:{{ $roleColor }};">
                {{ $initials }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ $user->first_name ?? 'User' }} {{ $user->last_name ?? '' }}</div>
                <div class="user-email">{{ $user->email ?? '' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-ghost btn-icon btn-sm" title="Logout" style="border:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
