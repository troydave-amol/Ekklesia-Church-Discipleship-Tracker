@extends('layouts.central-admin')

@section('title', 'Product Releases')
@section('topbar-title', 'Product Releases')

@section('content')
<div class="ca-page-head" style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
    <div>
        <h1 class="ca-page-title">Product Releases</h1>
        <p class="ca-page-desc">Manage app versions and notify tenants of updates.</p>
    </div>
    <a href="{{ route('admin.changelog.create') }}" class="ca-btn ca-btn-primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Release
    </a>
</div>

{{-- Stats Row --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    @foreach([
        ['label'=>'Total Releases',      'value'=>$totalReleases,  'color'=>'var(--ca-ink-2)'],
        ['label'=>'Published',           'value'=>$publishedCount, 'color'=>'var(--ca-sage)'],
        ['label'=>'Drafts',              'value'=>$draftCount,     'color'=>'var(--ca-amber)'],
        ['label'=>'Pushed to Tenants',   'value'=>$pushedCount,    'color'=>'var(--ca-blue)'],
    ] as $stat)
    <div class="ca-card" style="padding:18px 20px;">
        <div style="font-size:11px;font-weight:600;color:var(--ca-ink-4);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;">{{ $stat['label'] }}</div>
        <div style="font-size:28px;font-weight:700;color:{{ $stat['color'] }};font-family:'DM Mono',monospace;line-height:1;">{{ $stat['value'] }}</div>
    </div>
    @endforeach
</div>

{{-- Releases Table --}}
<div class="ca-card">
    <div class="ca-card-head" style="padding:14px 20px;">
        <span class="ca-card-title">All Releases</span>
        @if($latestRelease)
            <span style="font-size:12px;color:var(--ca-ink-4);">
                Latest published: <strong style="color:var(--ca-ink-2);">v{{ $latestRelease->version }}</strong>
            </span>
        @endif
    </div>

    @if($releases->isEmpty())
        <div style="text-align:center;padding:56px 24px;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--ca-ink-4)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:12px;">
                <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>
                <line x1="7" y1="7" x2="7.01" y2="7"/>
            </svg>
            <div style="font-size:14px;font-weight:500;color:var(--ca-ink-3);">No releases yet</div>
            <div style="font-size:12.5px;color:var(--ca-ink-4);margin-top:4px;">Create your first release using the "New Release" button above.</div>
        </div>
    @else
        <div class="ca-table-wrap">
            <table class="ca-table">
                <thead>
                    <tr>
                        <th>Version</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Published</th>
                        <th>Acknowledged</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($releases as $release)
                    <tr id="release-row-{{ $release->id }}" x-data="{
                        status: '{{ $release->status }}',
                        pushed: {{ $release->isPushed() ? 'true' : 'false' }},
                        publishing: false,
                        pushing: false,
                        pushMsg: '',
                        csrfToken: document.querySelector('meta[name=csrf-token]').content,

                        async publishRelease() {
                            if (this.publishing) return;
                            this.publishing = true;
                            try {
                                const res = await fetch('{{ route('admin.changelog.publish', $release->id) }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
                                });
                                const data = await res.json();
                                if (data.success) {
                                    this.status = 'published';
                                } else {
                                    alert(data.message);
                                }
                            } catch(e) { alert('Error publishing.'); }
                            finally { this.publishing = false; }
                        },

                        async broadcastRelease() {
                            if (this.pushing) return;
                            if (!confirm('Push v{{ $release->version }} to all tenants?')) return;
                            this.pushing = true;
                            try {
                                const res = await fetch('{{ route('admin.changelog.broadcast', $release->id) }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
                                });
                                const data = await res.json();
                                if (data.success) {
                                    this.pushed = true;
                                    this.pushMsg = 'Pushed to ' + data.tenant_count + ' tenants ✓';
                                } else {
                                    alert(data.message);
                                }
                            } catch(e) { alert('Error broadcasting.'); }
                            finally { this.pushing = false; }
                        }
                    }">
                        {{-- Version --}}
                        <td>
                            <span style="display:inline-flex;align-items:center;gap:6px;">
                                <span class="ca-pill {{ $release->is_major ? 'ca-pill-info' : '' }}"
                                      style="{{ $release->is_major ? '' : 'background:var(--ca-surface-dim);color:var(--ca-ink-3);' }}">
                                    v{{ $release->version }}
                                </span>
                                @if($release->is_major)
                                    <span style="font-size:10px;font-weight:700;color:var(--ca-amber);background:var(--ca-amber-dim);padding:2px 6px;border-radius:4px;letter-spacing:.04em;">MAJOR</span>
                                @endif
                            </span>
                        </td>

                        {{-- Title --}}
                        <td style="max-width:280px;">
                            <span style="font-size:13.5px;color:var(--ca-ink-2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">{{ $release->title }}</span>
                            <span style="font-size:11.5px;color:var(--ca-ink-4);">
                                {{ count($release->features ?? []) }} feature(s) · {{ count($release->bug_fixes ?? []) }} fix(es)
                            </span>
                        </td>

                        {{-- Status Badge (reactive) --}}
                        <td>
                            <template x-if="pushed">
                                <span class="ca-pill ca-pill-info"><span class="ca-pill-dot"></span> Pushed</span>
                            </template>
                            <template x-if="!pushed && status === 'published'">
                                <span class="ca-pill ca-pill-ok"><span class="ca-pill-dot"></span> Published</span>
                            </template>
                            <template x-if="!pushed && status === 'draft'">
                                <span class="ca-pill ca-pill-pending"><span class="ca-pill-dot"></span> Draft</span>
                            </template>
                        </td>

                        {{-- Published Date --}}
                        <td class="ca-table-muted">
                            {{ $release->published_at?->format('M d, Y') ?? '—' }}
                        </td>

                        {{-- Acknowledgment Count --}}
                        <td>
                            @if($release->isPushed())
                                <span style="font-size:13px;color:var(--ca-ink-2);">
                                    <strong>{{ $release->acknowledgments->count() }}</strong>
                                    <span class="ca-table-muted">/ {{ $totalTenants }}</span>
                                </span>
                            @else
                                <span class="ca-table-muted">—</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">

                                {{-- Publish button (draft only) --}}
                                <template x-if="status === 'draft'">
                                    <button @click="publishRelease()"
                                            :disabled="publishing"
                                            class="ca-btn ca-btn-approve"
                                            style="font-size:12px;padding:5px 12px;">
                                        <span x-text="publishing ? 'Publishing…' : 'Publish'"></span>
                                    </button>
                                </template>

                                {{-- Broadcast button (published, not yet pushed) --}}
                                <template x-if="status === 'published' && !pushed">
                                    <button @click="broadcastRelease()"
                                            :disabled="pushing"
                                            class="ca-btn ca-btn-approve"
                                            style="font-size:12px;padding:5px 12px;background:var(--ca-blue);color:#fff;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 010 14.14"/><path d="M15.54 8.46a5 5 0 010 7.07"/></svg>
                                        <span x-text="pushing ? 'Pushing…' : 'Push to all'"></span>
                                    </button>
                                </template>

                                {{-- Inline "pushed" confirmation --}}
                                <template x-if="pushMsg">
                                    <span style="font-size:12px;color:var(--ca-sage);font-weight:600;" x-text="pushMsg"></span>
                                </template>

                                {{-- View tenant status --}}
                                @if($release->isPushed())
                                <a href="{{ route('admin.changelog.tenants', $release->id) }}"
                                   class="ca-btn" style="font-size:12px;padding:5px 12px;background:var(--ca-surface-dim);color:var(--ca-ink-3);">
                                    Tenant status
                                </a>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($releases->hasPages())
            <div style="padding:14px 20px;border-top:1px solid var(--ca-border);">
                {{ $releases->links('partials.pagination') }}
            </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
// Auto-expand Alpine.js components on load
document.addEventListener('alpine:init', () => {});
</script>
@endpush
