{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Members')

@section('content')

<div class="flex items-center justify-between mb-20">
    <div>
        <h1 class="page-title">{{ ($leaderScope ?? false) ? 'My disciples' : 'User Management' }}</h1>
        <p class="page-subtitle">{{ ($leaderScope ?? false) ? 'Disciples assigned to you' : 'Manage disciples, leaders, and their assignments' }}</p>
    </div>
    @unless($leaderScope ?? false)
    <div class="flex gap-8">
        @can('import-disciples')
        <button class="btn btn-ghost" onclick="document.getElementById('importModal').style.display='flex'">
            @include('partials.icon',['name'=>'upload','size'=>14])
            Import CSV
        </button>
        @endcan
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            @include('partials.icon',['name'=>'plus','size'=>14])
            Add Member
        </a>
    </div>
    @endunless
</div>

{{-- Plan limits notice --}}
@php
    $plan = auth()->user()->church->subscription_plan;
    $maxDisciples = match($plan){ 'free'=>20,'basic'=>50,default=>null };
    $maxLeaders   = match($plan){ 'free'=>5,'basic'=>5,'standard'=>10,default=>null };
@endphp
@if($maxDisciples && $totalDisciples >= $maxDisciples)
<div class="alert alert-warning mb-20">
    @include('partials.icon',['name'=>'shield','size'=>18])
    <div>
        <strong>Disciple limit reached</strong> — Your {{ ucfirst($plan) }} plan allows up to {{ $maxDisciples }} disciples.
        <a href="{{ route('settings.church') }}#subscription" style="color:var(--gold-light);text-decoration:underline;margin-left:6px;">Upgrade to add more →</a>
    </div>
</div>
@endif

{{-- Tabs --}}
<div data-tab-group="users" style="margin-bottom:20px;">
    <div class="tabs">
        <button class="tab active" data-tab-target="tab-disciples">
            Disciples <span class="badge badge-blue" style="margin-left:6px;">{{ ($leaderScope ?? false) ? $disciples->total() : $totalDisciples }}</span>
        </button>
        @unless($leaderScope ?? false)
        <button class="tab" data-tab-target="tab-leaders">
            Leaders <span class="badge badge-blue" style="margin-left:6px;">{{ $totalLeaders }}</span>
        </button>
        <button class="tab" data-tab-target="tab-add">
            @include('partials.icon',['name'=>'plus','size'=>13,'color'=>'currentColor'])
            Add Member
        </button>
        @endunless
    </div>

    {{-- Disciples Tab --}}
    <div id="tab-disciples" class="tab-content active">
        <div class="card" style="margin-top:16px;">
            <div class="card-header">
                <form method="GET" action="{{ route('users.index') }}" class="table-controls" style="flex:1;">
                    <div class="input-group" style="max-width:260px;">
                        @include('partials.icon',['name'=>'search','size'=>14,'color'=>'var(--text-dim)'])
                        <input name="q" value="{{ request('q') }}" placeholder="Search disciples…">
                    </div>
                    <select name="program" style="max-width:160px;">
                        <option value="">All Programs</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}" {{ request('program') == $p->id ? 'selected':'' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <select name="leader_id" style="max-width:170px;">
                        <option value="">All Leaders</option>
                        @foreach($leaders as $l)
                            <option value="{{ $l->id }}" {{ request('leader_id') == $l->id ? 'selected':'' }}>{{ $l->first_name }} {{ $l->last_name }}</option>
                        @endforeach
                    </select>
                    <select name="status" style="max-width:130px;">
                        <option value="">All Status</option>
                        <option value="active"   {{ request('status')=='active'?'selected':'' }}>Active</option>
                        <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
                        <option value="visitor"  {{ request('status')=='visitor'?'selected':'' }}>Visitor</option>
                    </select>
                    <button type="submit" class="btn btn-ghost">Filter</button>
                    @if(request()->hasAny(['q','program','leader_id','status']))
                        <a href="{{ route('users.index') }}" class="btn btn-ghost">Clear</a>
                    @endif
                </form>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Program Level</th>
                            <th>Assigned Leader</th>
                            <th>Satellite</th>
                            <th style="min-width:120px;">Attendance</th>
                            <th style="min-width:120px;">Devotional</th>
                            <th>Status</th>
                            @unless($leaderScope ?? false)
                            <th>Actions</th>
                            @endunless
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($disciples as $d)
                        <tr>
                            <td>
                                <div class="flex items-center gap-12">
                                    @php $ini = strtoupper(substr($d->first_name,0,1).substr($d->last_name,0,1)); @endphp
                                    <div class="avatar avatar-sm" style="background:rgba(212,168,83,.15);border:1.5px solid rgba(212,168,83,.3);color:var(--gold);">{{ $ini }}</div>
                                    <div>
                                        <div style="font-weight:500;font-size:14px;">{{ $d->first_name }} {{ $d->last_name }}</div>
                                        <div style="font-size:11px;color:var(--text-muted);">{{ $d->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php $levelColors = ['life_class'=>'badge-blue','sol1'=>'badge-purple','sol2'=>'badge-orange','sol3'=>'badge-green','graduate'=>'badge-gold']; @endphp
                                <span class="badge {{ $levelColors[$d->membership->destiny_training_level ?? 'life_class'] ?? 'badge-blue' }}">
                                    {{ strtoupper(str_replace('_',' ',$d->membership->destiny_training_level ?? 'Life Class')) }}
                                </span>
                            </td>
                            <td>
                                @if($d->membership->leader)
                                    <div class="flex items-center gap-8">
                                        <div class="avatar avatar-sm" style="background:rgba(88,166,255,.15);border:1.5px solid rgba(88,166,255,.3);color:var(--blue);font-size:10px;">
                                            {{ strtoupper(substr($d->membership->leader->first_name,0,1).substr($d->membership->leader->last_name,0,1)) }}
                                        </div>
                                        <span style="font-size:13px;">{{ $d->membership->leader->first_name }} {{ $d->membership->leader->last_name }}</span>
                                    </div>
                                @else
                                    <button class="btn btn-ghost btn-sm" onclick="openAssignLeader({{ $d->id }})">Assign</button>
                                @endif
                            </td>
                            <td><span style="font-size:13px;color:var(--text-muted);">{{ $d->membership->satellite ?? '—' }}</span></td>
                            <td>
                                @php $att = $d->attendance_rate ?? 0; $ac = $att>=80?'var(--green)':($att>=60?'var(--orange)':'var(--red)'); @endphp
                                <div class="flex items-center gap-8">
                                    <div class="progress" style="flex:1;"><div class="progress-bar" style="width:{{ $att }}%;background:{{ $ac }};"></div></div>
                                    <span class="mono" style="font-size:12px;color:{{ $ac }};">{{ $att }}%</span>
                                </div>
                            </td>
                            <td>
                                @php $dev = $d->devotional_rate ?? 0; $dc = $dev>=80?'var(--purple)':($dev>=60?'var(--orange)':'var(--red)'); @endphp
                                <div class="flex items-center gap-8">
                                    <div class="progress" style="flex:1;"><div class="progress-bar" style="width:{{ $dev }}%;background:{{ $dc }};"></div></div>
                                    <span class="mono" style="font-size:12px;color:{{ $dc }};">{{ $dev }}%</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ match($d->membership->status??'inactive'){ 'active'=>'badge-green','visitor'=>'badge-blue','new_member'=>'badge-purple',default=>'badge-red' } }}">
                                    {{ strtoupper(str_replace('_',' ',$d->membership->status ?? 'inactive')) }}
                                </span>
                            </td>
                            @unless($leaderScope ?? false)
                            <td>
                                <div class="flex gap-8">
                                    <a href="{{ route('users.show', $d) }}" class="btn btn-ghost btn-icon btn-sm" title="View">@include('partials.icon',['name'=>'user','size'=>13])</a>
                                    <a href="{{ route('users.edit', $d) }}" class="btn btn-ghost btn-icon btn-sm" title="Edit">@include('partials.icon',['name'=>'edit','size'=>13])</a>
                                    <form method="POST" action="{{ route('users.destroy', $d) }}" onsubmit="return confirm('Remove this disciple?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-icon btn-sm" title="Remove">@include('partials.icon',['name'=>'trash','size'=>13,'color'=>'var(--red)'])</button>
                                    </form>
                                </div>
                            </td>
                            @endunless
                        </tr>
                        @empty
                        <tr><td colspan="{{ ($leaderScope ?? false) ? 7 : 8 }}" style="text-align:center;padding:48px;color:var(--text-muted);">
                            No disciples found. <a href="{{ route('users.create') }}" style="color:var(--gold);">Add your first disciple →</a>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($disciples->hasPages())
            <div class="card-footer flex justify-between items-center">
                <span class="text-muted" style="font-size:13px;">{{ $disciples->firstItem() }}–{{ $disciples->lastItem() }} of {{ $disciples->total() }}</span>
                {{ $disciples->withQueryString()->links('partials.pagination') }}
            </div>
            @endif
        </div>
    </div>

    {{-- Leaders Tab --}}
    <div id="tab-leaders" class="tab-content" @if($leaderScope ?? false) style="display:none;" @endif>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-top:16px;">
            @forelse($leaders as $index => $leader)
            @php $lColors=['var(--gold)','var(--blue)','var(--purple)','var(--green)','var(--orange)']; $lc=$lColors[$index%5]; @endphp
            <div class="card">
                <div class="card-body">
                    <div class="flex gap-12 mb-16">
                        <div class="avatar avatar-lg" style="background:{{ $lc }}22;border:1.5px solid {{ $lc }}44;color:{{ $lc }};">
                            {{ strtoupper(substr($leader->first_name,0,1).substr($leader->last_name,0,1)) }}
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:15px;">{{ $leader->first_name }} {{ $leader->last_name }}</div>
                            <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">{{ $leader->email }}</div>
                            <span class="badge badge-blue" style="margin-top:6px;">LEADER</span>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:16px;">
                        <div class="flex justify-between" style="font-size:13px;">
                            <span class="text-muted">Assigned Disciples</span>
                            <span style="font-weight:600;">{{ $leader->assigned_disciples_count }}</span>
                        </div>
                        <div>
                            <div class="flex justify-between" style="font-size:12px;margin-bottom:4px;">
                                <span class="text-muted">Attendance Rate</span>
                                <span class="mono" style="color:var(--green);">{{ $leader->attendance_rate ?? 0 }}%</span>
                            </div>
                            <div class="progress"><div class="progress-bar" style="width:{{ $leader->attendance_rate ?? 0 }}%;background:var(--green);"></div></div>
                        </div>
                        <div>
                            <div class="flex justify-between" style="font-size:12px;margin-bottom:4px;">
                                <span class="text-muted">Devotional Rate</span>
                                <span class="mono" style="color:var(--purple);">{{ $leader->devotional_rate ?? 0 }}%</span>
                            </div>
                            <div class="progress"><div class="progress-bar" style="width:{{ $leader->devotional_rate ?? 0 }}%;background:var(--purple);"></div></div>
                        </div>
                    </div>
                    <div class="flex gap-8">
                        <a href="{{ route('users.show', $leader) }}?tab=disciples" class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;">View Disciples</a>
                        <a href="{{ route('users.edit', $leader) }}" class="btn btn-ghost btn-icon btn-sm">@include('partials.icon',['name'=>'edit','size'=>13])</a>
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column:1/-1;text-align:center;padding:48px;color:var(--text-muted);">
                No leaders yet. <a href="{{ route('users.create') }}?role=leader" style="color:var(--gold);">Add a leader →</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Add Member Tab --}}
    <div id="tab-add" class="tab-content" @if($leaderScope ?? false) style="display:none;" @endif>
        <div class="grid-2 mt-16">
            @include('users.partials.create-form')
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openAssignLeader(userId) {
    // You'd open a modal here
    window.location = '/users/' + userId + '/assign-leader';
}
</script>
@endpush
