<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Dashboard</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --blue-600: #2563eb;
            --emerald-600: #059669;
            --amber-600: #d97706;
            --rose-600: #e11d48;
            --slate-900: #0f172a;
            --slate-50: #f8fafc;
        }

        .admin-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
            background-color: var(--slate-50);
        }

        .sidebar {
            background-color: var(--slate-900);
            color: white;
            padding: 1.5rem;
            overflow-y: auto;
            border-right: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-title {
            font-size: 0.875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(255,255,255,0.5);
            margin: 1.5rem 0 0.75rem 0;
        }

        .sidebar-link {
            display: block;
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.5rem;
            text-decoration: none;
            color: rgba(255,255,255,0.7);
            transition: all 0.2s;
            font-size: 0.9375rem;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }

        .main-content {
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: white;
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--slate-900);
        }

        .user-menu {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .logout-btn {
            padding: 0.5rem 1rem;
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 0.875rem;
            text-decoration: none;
            color: #374151;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background-color: #e5e7eb;
        }

        .content {
            padding: 2rem;
            flex: 1;
        }

        .card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            border-left: 4px solid var(--blue-600);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .stat.pending {
            border-left-color: var(--amber-600);
        }

        .stat.active {
            border-left-color: var(--emerald-600);
        }

        .stat.alert {
            border-left-color: var(--rose-600);
        }

        .stat-label {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--slate-900);
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-btn {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            color: var(--slate-900);
            font-weight: 500;
        }

        .action-btn:hover {
            border-color: var(--blue-600);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        }

        .action-btn-icon {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        {{-- Sidebar --}}
        <aside class="sidebar">
            <div style="margin-bottom: 2rem;">
                <h1 style="font-size: 1.25rem; font-weight: 700; color: white;">Admin Panel</h1>
                <p style="font-size: 0.875rem; color: rgba(255,255,255,0.5); margin-top: 0.25rem;">Central Management</p>
            </div>

            <nav>
                <div class="sidebar-title">Main</div>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">📊 Dashboard</a>
                <a href="{{ route('admin.tenants.index') }}" class="sidebar-link {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}">🏘️ Tenants</a>
                <a href="{{ route('admin.signups.index') }}" class="sidebar-link {{ request()->routeIs('admin.signups.*') ? 'active' : '' }}">✍️ Signups</a>

                <div class="sidebar-title">Support & Communication</div>
                <a href="{{ route('admin.tickets.index') }}" class="sidebar-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">🎫 Tickets</a>
                <a href="{{ route('admin.announcements.index') }}" class="sidebar-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">📢 Announcements</a>

                @if(auth('admin')->user()->canManageAdmins())
                    <div class="sidebar-title">Administration</div>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">👥 Admin Users</a>
                    <a href="#" class="sidebar-link" onclick="alert('RBAC management coming soon')">🔐 RBAC Management</a>
                @endif

                <div class="sidebar-title">Account</div>
                <form method="POST" action="{{ route('admin.logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="sidebar-link" style="width: 100%; text-align: left; border: none; background: none; padding: 0.75rem 1rem; margin: 0.25rem 0; cursor: pointer; font-size: 0.9375rem;">🚪 Logout</button>
                </form>
            </nav>
        </aside>

        {{-- Main Content --}}
        <div class="main-content">
            {{-- Topbar --}}
            <div class="topbar">
                <h2 class="topbar-title">@yield('topbar-title', 'Dashboard')</h2>
                <div class="user-menu">
                    <span style="color: #6b7280; font-size: 0.875rem;">{{ auth('admin')->user()->name }} ({{ auth('admin')->user()->role }})</span>
                </div>
            </div>

            {{-- Content --}}
            <div class="content">
                @if(session('success'))
                    <div style="background-color: #ecfdf5; border: 1px solid #86efac; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1.5rem; color: #166534;">
                        ✓ {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
