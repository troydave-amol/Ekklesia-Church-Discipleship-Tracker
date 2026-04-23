<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Discipleship Tracker') — {{ auth()->user()->church->name ?? 'Church' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        /* ── RESET & BASE ─────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:           #0D1117;
            --surface:      #161B22;
            --surface-hover:#1C2333;
            --border:       #30363D;
            --border-light: #21262D;
            --gold:         #D4A853;
            --gold-light:   #E8C47A;
            --gold-dim:     #8B6914;
            --cream:        #F5EDD6;
            --text:         #E6EDF3;
            --text-muted:   #8B949E;
            --text-dim:     #6E7681;
            --green:        #3FB950;
            --green-dim:    #1A3A20;
            --red:          #F85149;
            --red-dim:      #3A1A1A;
            --blue:         #58A6FF;
            --blue-dim:     #1A2A3A;
            --purple:       #BC8CFF;
            --purple-dim:   #2A1A3A;
            --orange:       #FF7B54;
            --sidebar-w:    240px;
            --topbar-h:     56px;
            --radius:       10px;
            --radius-lg:    14px;
        }

        html, body { height: 100%; background: var(--bg); color: var(--text); font-family: 'DM Sans', sans-serif; font-size: 14px; line-height: 1.5; }

        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--gold-dim); border-radius: 2px; }

        a { color: inherit; text-decoration: none; }
        button { cursor: pointer; font-family: inherit; }

        @keyframes fadeUp   { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
        @keyframes slideIn  { from { opacity:0; transform:translateX(-10px); } to { opacity:1; transform:translateX(0); } }
        @keyframes glow     { 0%,100%{ box-shadow:0 0 8px rgba(212,168,83,.3);} 50%{ box-shadow:0 0 22px rgba(212,168,83,.6);} }
        @keyframes pulse    { 0%,100%{ opacity:1; } 50%{ opacity:.5; } }

        .fade-up  { animation: fadeUp  .35s ease both; }
        .slide-in { animation: slideIn .3s  ease both; }

        /* ── LAYOUT ───────────────────────────────────────── */
        .app-shell { display: flex; height: 100vh; overflow: hidden; }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex; flex-direction: column;
            flex-shrink: 0;
            transition: width .3s ease;
            overflow: hidden;
            z-index: 50;
            position: relative;
        }

        .sidebar-brand {
            padding: 18px 16px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 12px;
        }
        .brand-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, var(--gold-dim), var(--gold));
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            animation: glow 4s ease-in-out infinite;
        }
        .brand-icon svg { color: #0D1117; }
        .brand-text { white-space: nowrap; overflow: hidden; }
        .brand-text strong { font-family: 'Playfair Display', serif; font-size: 14px; display: block; line-height: 1.2; }
        .brand-text span   { font-size: 10px; color: var(--text-muted); letter-spacing: .08em; }

        .sidebar-role {
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
        }
        .role-chip {
            display: flex; align-items: center; gap: 8px;
            background: rgba(212,168,83,.08);
            border: 1px solid rgba(212,168,83,.2);
            border-radius: 8px; padding: 7px 12px;
            white-space: nowrap; overflow: hidden;
        }
        .role-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--gold); flex-shrink: 0; }
        .role-label { font-size: 12px; color: var(--gold-light); font-weight: 600; }
        .role-church { font-size: 11px; color: var(--text-muted); margin-left: auto; }

        .sidebar-nav { flex: 1; padding: 10px 8px; overflow-y: auto; }
        .nav-section-label { font-size: 10px; color: var(--text-dim); letter-spacing: .1em; text-transform: uppercase; padding: 10px 10px 4px; white-space: nowrap; overflow: hidden; }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 10px; border-radius: 8px;
            color: var(--text-muted); font-size: 14px; font-weight: 400;
            transition: all .18s; cursor: pointer;
            margin-bottom: 2px;
            border-left: 2px solid transparent;
            white-space: nowrap; overflow: hidden;
        }
        .nav-item svg { flex-shrink: 0; }
        .nav-item:hover { background: var(--surface-hover); color: var(--text); }
        .nav-item.active { background: rgba(212,168,83,.1); color: var(--gold-light); font-weight: 500; border-left-color: var(--gold); }
        .nav-item .badge-count { margin-left: auto; font-size: 10px; background: var(--red); color: #fff; border-radius: 10px; padding: 1px 6px; font-family: 'DM Mono', monospace; }

        .sidebar-user {
            padding: 10px 8px;
            border-top: 1px solid var(--border);
        }
        .user-row {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 10px; border-radius: 8px;
            cursor: pointer; transition: background .18s;
            overflow: hidden;
        }
        .user-row:hover { background: var(--surface-hover); }
        .user-info { flex: 1; min-width: 0; }
        .user-name { font-size: 13px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-email { font-size: 11px; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* ── PROFILE DROPDOWN ─────────────────────────────── */
        .profile-dropdown-wrapper { position: relative; }
        .profile-dropdown {
            position: fixed; min-width: 270px;
            background: #171717; border: 1px solid var(--border-light);
            border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.5), 0 0 1px rgba(255,255,255,0.1) inset;
            opacity: 0; visibility: hidden; transform: translateY(10px);
            transition: all .15s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 9999; display: flex; flex-direction: column;
            color: #d1d5db; padding: 8px 0;
        }
        .profile-dropdown.open { opacity: 1; visibility: visible; transform: translateY(0); }
        .pd-header { display: flex; align-items: center; justify-content: space-between; padding: 6px 14px 10px 14px; }
        .pd-divider { height: 1px; background: rgba(255,255,255,0.1); margin: 6px 0; }
        .pd-item-wrapper { padding: 0 8px; }
        .pd-item { display: flex; align-items: center; gap: 12px; padding: 10px 12px; font-size: 13.5px; cursor: pointer; color: #ececf1; transition: background .1s; text-decoration: none; border-radius: 8px; }
        .pd-item:hover { background: #2f2f2f; }
        .pd-item svg { color: #9ca3af; }

        /* Main area */
        .main-area { flex: 1; display: flex; flex-direction: column; min-width: 0; overflow: hidden; }

        .topbar {
            height: var(--topbar-h);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 12px;
            padding: 0 24px;
            flex-shrink: 0;
        }
        .topbar-search {
            display: flex; align-items: center; gap: 8px;
            background: var(--bg); border: 1px solid var(--border);
            border-radius: 8px; padding: 0 14px; height: 42px;
            flex: 1; max-width: 320px;
            transition: border-color .2s, box-shadow .2s;
        }
        .topbar-search:focus-within {
            border-color: var(--gold); box-shadow: 0 0 0 3px rgba(212,168,83,.1);
        }
        .topbar-search input { 
            background: none; border: none; outline: none; color: var(--text); 
            font-family: inherit; font-size: 13px; width: 100%;
            height: auto; padding: 0; line-height: normal;
        }
        .topbar-search input:focus { box-shadow: none; border-color: transparent; }
        .topbar-search input::placeholder { color: var(--text-dim); }
        .topbar-actions { display: flex; align-items: center; gap: 10px; margin-left: auto; }

        .page-content { flex: 1; padding: 24px; overflow-y: auto; }

        /* ── TYPOGRAPHY ───────────────────────────────────── */
        .page-title { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; }
        .page-subtitle { font-size: 13px; color: var(--text-muted); margin-top: 3px; }
        .section-title { font-size: 15px; font-weight: 600; }

        /* ── BUTTONS ──────────────────────────────────────── */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 0 18px; height: 38px; border-radius: 8px; font-size: 13px; font-weight: 500; font-family: inherit; transition: all .2s; border: none; white-space: nowrap; }
        .btn:hover { transform: translateY(-1px); }
        .btn-sm { padding: 0 14px; font-size: 12px; border-radius: 6px; height: 32px; }
        .btn-icon { padding: 0; width: 32px; height: 32px; border-radius: 6px; }

        .btn-primary { background: linear-gradient(135deg, var(--gold), var(--gold-light)); color: #0D1117; font-weight: 600; }
        .btn-primary:hover { box-shadow: 0 4px 16px rgba(212,168,83,.4); }
        .btn-secondary { background: transparent; color: var(--gold); border: 1px solid var(--gold-dim); }
        .btn-secondary:hover { background: rgba(212,168,83,.08); }
        .btn-ghost { background: transparent; color: var(--text-muted); border: 1px solid var(--border); }
        .btn-ghost:hover { color: var(--text); border-color: var(--text-muted); }
        .btn-danger { background: transparent; color: var(--red); border: 1px solid rgba(248,81,73,.3); }
        .btn-danger:hover { background: rgba(248,81,73,.08); }

        /* ── CARDS ────────────────────────────────────────── */
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); transition: border-color .2s; }
        .card:hover { border-color: var(--gold-dim); }
        .card-body { padding: 20px; }
        .card-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .card-footer { padding: 12px 20px; border-top: 1px solid var(--border); }

        /* Stat card */
        .stat-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 20px;
            position: relative; overflow: hidden;
        }
        .stat-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent-color, var(--gold)), transparent);
        }
        .stat-label { font-size: 11px; font-weight: 600; color: var(--text-muted); letter-spacing: .08em; text-transform: uppercase; margin-bottom: 10px; }
        .stat-value { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 700; line-height: 1; }
        .stat-sub { font-size: 12px; color: var(--text-muted); margin-top: 6px; }
        .stat-icon { position: absolute; top: 18px; right: 18px; opacity: .6; }

        /* ── BADGES ───────────────────────────────────────── */
        .badge { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 20px; font-size: 10px; font-weight: 700; letter-spacing: .06em; font-family: 'DM Mono', monospace; white-space: nowrap; }
        .badge-gold   { background: rgba(212,168,83,.12); color: var(--gold-light); border: 1px solid rgba(212,168,83,.3); }
        .badge-green  { background: rgba(63,185,80,.1);  color: var(--green);      border: 1px solid rgba(63,185,80,.25); }
        .badge-red    { background: rgba(248,81,73,.1);  color: var(--red);        border: 1px solid rgba(248,81,73,.25); }
        .badge-blue   { background: rgba(88,166,255,.1); color: var(--blue);       border: 1px solid rgba(88,166,255,.25); }
        .badge-purple { background: rgba(188,140,255,.1);color: var(--purple);     border: 1px solid rgba(188,140,255,.25); }
        .badge-orange { background: rgba(255,123,84,.1); color: var(--orange);     border: 1px solid rgba(255,123,84,.25); }

        /* ── FORMS ────────────────────────────────────────── */
        .form-group { margin-bottom: 16px; }
        .form-grid { display: grid; gap: 14px; }
        .form-grid-2 { grid-template-columns: 1fr 1fr; }
        .form-grid-3 { grid-template-columns: 1fr 1fr 1fr; }
        label.field-label { display: block; font-size: 11px; font-weight: 600; color: var(--text-muted); letter-spacing: .07em; text-transform: uppercase; margin-bottom: 6px; }
        input, select, textarea {
            width: 100%; background: var(--bg); border: 1px solid var(--border);
            border-radius: 8px; color: var(--text); font-family: inherit; font-size: 13px;
            padding: 8px 12px; transition: border-color .2s, box-shadow .2s; outline: none;
        }
        input:not([type="checkbox"]):not([type="radio"]), select {
            height: 38px;
        }
        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%238B949E' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 36px;
        }
        textarea { resize: vertical; padding: 12px; }
        input:focus, select:focus, textarea:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(212,168,83,.1); }
        input::placeholder, textarea::placeholder { color: var(--text-dim); }

        /* Unified Top Controls Row */
        .table-controls { display: flex; align-items: center; gap: 8px; }
        .table-controls input, .table-controls select, .table-controls .btn { height: 36px; font-size: 13px; line-height: normal; }
        
        /* Input Group Search Layout */
        .input-group { position: relative; display: flex; align-items: center; }
        .input-group > svg, .input-group .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--text-dim); }
        .input-group input { padding-left: 36px; padding-right: 12px; }
        
        .is-invalid { border-color: var(--red) !important; }
        .invalid-feedback { font-size: 12px; color: var(--red); margin-top: 4px; }

        /* ── TABLE ────────────────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { font-size: 11px; font-weight: 600; color: var(--text-muted); letter-spacing: .08em; text-transform: uppercase; padding: 10px 14px; text-align: left; border-bottom: 1px solid var(--border); white-space: nowrap; }
        td { padding: 12px 14px; border-bottom: 1px solid var(--border-light); font-size: 14px; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tbody tr { transition: background .12s; }
        tbody tr:hover td { background: rgba(255,255,255,.02); }

        /* ── PROGRESS ─────────────────────────────────────── */
        .progress { height: 6px; background: var(--border-light); border-radius: 3px; overflow: hidden; }
        .progress-bar { height: 100%; border-radius: 3px; transition: width .5s ease; }

        /* ── AVATAR ───────────────────────────────────────── */
        .avatar {
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 600; flex-shrink: 0; font-family: 'DM Sans', sans-serif;
        }
        .avatar-sm  { width:28px; height:28px; font-size:11px; }
        .avatar-md  { width:36px; height:36px; font-size:13px; }
        .avatar-lg  { width:48px; height:48px; font-size:17px; }

        /* ── TOGGLE ───────────────────────────────────────── */
        .toggle-wrap { display: flex; align-items: center; gap: 8px; }
        .toggle-switch { position: relative; width: 40px; height: 22px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
        .toggle-slider {
            position: absolute; inset: 0; background: var(--border);
            border-radius: 11px; cursor: pointer; transition: background .2s;
        }
        .toggle-slider::after {
            content:''; position:absolute; width:16px; height:16px; background:#fff;
            border-radius:50%; top:3px; left:3px; transition: transform .2s;
        }
        input:checked + .toggle-slider { background: var(--green); }
        input:checked + .toggle-slider::after { transform: translateX(18px); }

        /* ── TABS ─────────────────────────────────────────── */
        .tabs { display: flex; border-bottom: 1px solid var(--border); gap: 0; overflow-x: auto; }
        .tab { padding: 10px 18px; font-size: 13px; font-weight: 500; color: var(--text-muted); cursor: pointer; border-bottom: 2px solid transparent; transition: all .2s; white-space: nowrap; background: none; border-top: none; border-left: none; border-right: none; font-family: inherit; }
        .tab:hover { color: var(--text); }
        .tab.active { color: var(--gold-light); border-bottom-color: var(--gold); }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeUp .25s ease; }

        /* ── ALERTS ───────────────────────────────────────── */
        .alert { display: flex; gap: 14px; align-items: flex-start; padding: 16px 20px; border-radius: var(--radius-lg); border: 1px solid; margin-bottom: 20px; }
        .alert-warning { background: rgba(212,168,83,.08); border-color: rgba(212,168,83,.3); color: var(--gold-light); }
        .alert-success { background: rgba(63,185,80,.08);  border-color: rgba(63,185,80,.3);  color: var(--green); }
        .alert-danger  { background: rgba(248,81,73,.08);  border-color: rgba(248,81,73,.3);  color: var(--red); }
        .alert-info    { background: rgba(88,166,255,.08); border-color: rgba(88,166,255,.3); color: var(--blue); }

        /* ── MISC ─────────────────────────────────────────── */
        .divider { height: 1px; background: var(--border); margin: 16px 0; }
        .text-muted { color: var(--text-muted); }
        .text-gold   { color: var(--gold); }
        .text-green  { color: var(--green); }
        .text-red    { color: var(--red); }
        .text-blue   { color: var(--blue); }
        .mono { font-family: 'DM Mono', monospace; }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-8  { gap: 8px; }
        .gap-12 { gap: 12px; }
        .gap-16 { gap: 16px; }
        .gap-20 { gap: 20px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        .grid-4 { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; }
        .mb-4  { margin-bottom: 4px; }
        .mb-8  { margin-bottom: 8px; }
        .mb-12 { margin-bottom: 12px; }
        .mb-16 { margin-bottom: 16px; }
        .mb-20 { margin-bottom: 20px; }
        .mt-8  { margin-top: 8px; }
        .mt-12 { margin-top: 12px; }
        .mt-16 { margin-top: 16px; }
        .w-full { width: 100%; }

        /* ── RESPONSIVE ───────────────────────────────────── */
        @media (max-width: 900px) {
            .grid-4 { grid-template-columns: 1fr 1fr; }
            .grid-3 { grid-template-columns: 1fr 1fr; }
            .grid-2 { grid-template-columns: 1fr; }
            .form-grid-3 { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 640px) {
            .sidebar { position: fixed; z-index: 100; height: 100%; }
            .sidebar:not(.collapsed) { box-shadow: 0 0 40px rgba(0,0,0,.6); }
            .grid-4 { grid-template-columns: 1fr; }
            .form-grid-2 { grid-template-columns: 1fr; }
            .page-content { padding: 16px; }
        }

        @yield('extra-styles')
    </style>

    @stack('styles')
</head>
<body>
<div class="app-shell">

    {{-- ── SIDEBAR ──────────────────────────────────────────── --}}
    @include('partials.sidebar')

    {{-- ── MAIN ────────────────────────────────────────────── --}}
    <div class="main-area">

        {{-- Topbar --}}
        @include('partials.topbar')

        {{-- Flash messages --}}
        <div style="padding: 0 24px;">
            @if(session('success'))
                <div class="alert alert-success" style="margin-top:16px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <div>{{ session('success') }}</div>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger" style="margin-top:16px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div>{{ session('error') }}</div>
                </div>
            @endif
        </div>

        {{-- Page content --}}
        <div class="page-content fade-up">
            @yield('content')
        </div>
    </div>
</div>

{{-- ── GLOBAL JS ────────────────────────────────────────────── --}}
<script>
// Profile Dropdown Setup
const profileWrapper = document.getElementById('profileDropdownWrapper');
const profileTrigger = document.getElementById('profileTrigger');
const profileMenu = document.getElementById('profileDropdown');

function positionDropdown() {
    if (!profileTrigger || !profileMenu) return;
    const rect = profileTrigger.getBoundingClientRect();
    profileMenu.style.left = rect.left + 'px';
    profileMenu.style.bottom = (window.innerHeight - rect.top + 8) + 'px';
}

if (profileTrigger && profileMenu) {
    profileTrigger.addEventListener('click', (e) => {
        positionDropdown();
        profileMenu.classList.toggle('open');
        e.stopPropagation();
    });
    document.addEventListener('click', (e) => {
        if (!profileWrapper.contains(e.target) && !profileMenu.contains(e.target)) {
            profileMenu.classList.remove('open');
        }
    });
    window.addEventListener('resize', () => {
        if (profileMenu.classList.contains('open')) positionDropdown();
    });
}

// Tab switching
document.querySelectorAll('[data-tab-target]').forEach(btn => {
    btn.addEventListener('click', () => {
        const group = btn.closest('[data-tab-group]') || document;
        const target = btn.dataset.tabTarget;
        group.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        group.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(target)?.classList.add('active');
    });
});

// Auto-hide flash
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => el.style.opacity = '0', 4000);
    setTimeout(() => el.remove(), 4400);
    el.style.transition = 'opacity .4s';
});

// CSRF for fetch
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

// Notification Dropdown
const notifTrigger = document.getElementById('notifTrigger');
const notifDropdown = document.getElementById('notifDropdown');
const notifWrapper = document.getElementById('notifDropdownWrapper');

if (notifTrigger && notifDropdown) {
    notifTrigger.addEventListener('click', (e) => {
        const isOpen = notifDropdown.style.display === 'flex';
        notifDropdown.style.display = isOpen ? 'none' : 'flex';
        e.stopPropagation();
    });
    document.addEventListener('click', (e) => {
        if (!notifWrapper.contains(e.target)) {
            notifDropdown.style.display = 'none';
        }
    });
}
</script>

@stack('scripts')
</body>
</html>
