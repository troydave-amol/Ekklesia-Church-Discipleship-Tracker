{{-- resources/views/partials/icon.blade.php --}}
{{-- Usage: @include('partials.icon', ['name' => 'home', 'size' => 16, 'color' => '#8B949E']) --}}
@php $s = $size ?? 16; $c = $color ?? 'currentColor'; @endphp

@switch($name ?? '')
@case('home')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>@break
@case('users')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>@break
@case('book')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>@break
@case('calendar')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>@break
@case('heart')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>@break
@case('chart')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>@break
@case('settings')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M12 2v2M12 20v2M20 12h2M2 12h2M19.07 19.07l-1.41-1.41M4.93 19.07l1.41-1.41"/></svg>@break
@case('cross')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><line x1="12" y1="2" x2="12" y2="22"/><line x1="2" y1="9" x2="22" y2="9"/></svg>@break
@case('plus')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>@break
@case('edit')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>@break
@case('trash')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>@break
@case('upload')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>@break
@case('download')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>@break
@case('check')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>@break
@case('x')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>@break
@case('fire')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><path d="M8.5 14.5A2.5 2.5 0 0011 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 11-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 002.5 2.5z"/></svg>@break
@case('shield')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>@break
@case('search')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>@break
@case('user')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>@break
@case('star')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>@break
@case('arrow-right')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>@break
@case('message')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>@break
@case('bell')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>@break
@case('inbox')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><polyline points="22 12 18 12 15 21 9 21 6 12 2 12"/><path d="M9 3h6v7H9z"/></svg>@break
@case('info')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>@break
@case('database')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>@break
@case('history')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/><path d="M9.09 9a6 6 0 0 1 9.82 1m-7.72 7a6 6 0 0 1 .87-9.82"/></svg>@break
@case('palette')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><circle cx="13.5" cy="6.5" r="1.5"/><circle cx="17.5" cy="10.5" r="1.5"/><circle cx="8.5" cy="7.5" r="1.5"/><circle cx="6.5" cy="12" r="1.5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.93 0 1.5-.67 1.5-1.5 0-.38-.15-.74-.39-1.02-.23-.27-.37-.62-.37-1 0-.83.67-1.5 1.5-1.5H16c3.31 0 6-2.69 6-6 0-5.5-4.5-9.98-10-9.98z"/></svg>@break
@case('download')
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>@break
@default
    <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="{{ $c }}" stroke-width="1.8"><circle cx="12" cy="12" r="10"/></svg>
@endswitch
