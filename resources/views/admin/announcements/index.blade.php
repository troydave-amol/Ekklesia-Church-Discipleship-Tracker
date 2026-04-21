@extends('layouts.central-admin')

@section('title', 'Announcements')
@section('topbar-title', 'Announcements')

@section('content')
<div class="ca-page-head">
    <h1 class="ca-page-title">System Announcements</h1>
    <p class="ca-page-desc">Broadcast messages to admins and tenants. Create, edit, and track delivery.</p>
    <a href="{{ route('admin.announcements.create') }}" class="ca-btn ca-btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        New Announcement
    </a>
</div>

{{-- ═══ TABLE ═══ --}}
<div class="ca-table-card">
    <table class="ca-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Audience</th>
                <th>Status</th>
                <th>Published</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($announcements as $a)
                <tr>
                    <td class="ca-table-strong">{{ $a->title }}</td>
                    <td>
                        @php
                            $audienceLabel = match($a->audience ?? 'all') {
                                'all' => 'All Users',
                                'admins' => 'Admins Only',
                                'tenants' => 'Tenants Only',
                                default => ucfirst($a->audience ?? 'all')
                            };
                        @endphp
                        <span class="ca-badge">{{ $audienceLabel }}</span>
                    </td>
                    <td>
                        @if($a->published_at)
                            <span class="ca-pill ca-pill-ok">
                                <span class="ca-pill-dot"></span> Published
                            </span>
                        @else
                            <span class="ca-pill ca-pill-pending">
                                <span class="ca-pill-dot"></span> Draft
                            </span>
                        @endif
                    </td>
                    <td class="ca-table-muted">
                        @if($a->published_at)
                            {{ $a->published_at->format('M j, Y H:i') }}
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        <div class="ca-actions">
                            <a href="{{ route('admin.announcements.edit', $a) }}" class="ca-btn ca-btn-link">Edit</a>
                            @if(!$a->published_at)
                                <form action="{{ route('admin.announcements.publish', $a) }}" method="post" class="ca-inline">
                                    @csrf
                                    <button type="submit" class="ca-btn ca-btn-approve" style="font-size:12px;">Publish</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="ca-table-empty">No announcements yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="ca-pagination">
    {{ $announcements->links() }}
</div>
@endsection
