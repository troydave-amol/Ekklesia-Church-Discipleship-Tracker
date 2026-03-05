{{-- resources/views/partials/pagination.blade.php --}}
@if ($paginator->hasPages())
<nav style="display:flex;gap:4px;align-items:center;">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border-light);color:var(--text-dim);font-size:12px;cursor:not-allowed;">‹</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--text-muted);font-size:12px;transition:all .15s;">‹</a>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="padding:5px 8px;color:var(--text-dim);font-size:12px;">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="padding:5px 10px;border-radius:6px;background:var(--gold);color:#0D1117;font-size:12px;font-weight:600;font-family:'DM Mono',monospace;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--text-muted);font-size:12px;font-family:'DM Mono',monospace;transition:all .15s;">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--text-muted);font-size:12px;transition:all .15s;">›</a>
    @else
        <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border-light);color:var(--text-dim);font-size:12px;cursor:not-allowed;">›</span>
    @endif
</nav>
@endif
