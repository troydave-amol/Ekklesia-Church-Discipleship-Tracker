<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Central app') — Ekklesia</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #0d1117; color: #e6edf3; line-height: 1.5; min-height: 100vh; }
        a { color: #58a6ff; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .shell { display: flex; min-height: 100vh; }
        .side { width: 220px; background: #161b22; border-right: 1px solid #30363d; padding: 1.25rem; flex-shrink: 0; }
        .side h1 { font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: #8b949e; margin-bottom: 1rem; }
        .side nav { display: flex; flex-direction: column; gap: 0.25rem; }
        .side nav a { padding: 0.5rem 0.65rem; border-radius: 6px; color: #c9d1d9; font-size: 0.9rem; }
        .side nav a:hover { background: #21262d; text-decoration: none; }
        .side nav a.active { background: #21262d; color: #fff; }
        main { flex: 1; padding: 2rem; max-width: 1100px; }
        .flash { padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.9rem; }
        .flash.ok { background: #1a3a20; border: 1px solid #3fb950; color: #aff5b4; }
        .flash.err { background: #3a1a1a; border: 1px solid #f85149; color: #ffb4b0; }
        h2 { font-size: 1.35rem; font-weight: 600; margin-bottom: 1rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .card { background: #161b22; border: 1px solid #30363d; border-radius: 10px; padding: 1rem 1.15rem; }
        .card .label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #8b949e; margin-bottom: 0.35rem; }
        .card .value { font-size: 1.5rem; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        th, td { text-align: left; padding: 0.65rem 0.75rem; border-bottom: 1px solid #30363d; }
        th { color: #8b949e; font-weight: 500; font-size: 0.75rem; text-transform: uppercase; }
        tr:hover td { background: #161b22; }
        .btn { display: inline-block; padding: 0.4rem 0.75rem; border-radius: 6px; font-size: 0.85rem; border: none; cursor: pointer; font-weight: 500; }
        .btn-primary { background: #238636; color: #fff; }
        .btn-primary:hover { background: #2ea043; }
        .btn-danger { background: #da3633; color: #fff; }
        .btn-danger:hover { background: #f85149; }
        .btn-ghost { background: transparent; border: 1px solid #30363d; color: #c9d1d9; }
        .pill { display: inline-block; padding: 0.15rem 0.5rem; border-radius: 999px; font-size: 0.75rem; font-weight: 500; }
        .pill-pending { background: #3d2a00; color: #d4a72c; }
        .pill-ok { background: #1a3a20; color: #3fb950; }
        .pill-no { background: #3a1a1a; color: #f85149; }
        form.inline { display: inline; }
        .muted { color: #8b949e; font-size: 0.85rem; }
        .stack { display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; }
    </style>
    @stack('styles')
</head>
<body>
<div class="shell">
    <aside class="side">
        <h1>Central app</h1>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Monitoring</a>
            <a href="{{ route('admin.signups.index') }}" class="{{ request()->routeIs('admin.signups.*') ? 'active' : '' }}">Approvals</a>
            <a href="{{ route('admin.tenants.index') }}" class="{{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}">Tenants</a>
            <a href="{{ route('home') }}">Public site</a>
        </nav>
    </aside>
    <main>
        @if(session('success'))
            <div class="flash ok">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash err">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
</div>
@stack('scripts')
</body>
</html>
