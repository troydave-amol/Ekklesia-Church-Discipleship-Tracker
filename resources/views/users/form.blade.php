@extends('layouts.app')
@section('title', ($mode === 'edit') ? 'Edit member' : 'Add member')

@section('content')
<div class="mb-20">
    <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm">← Back to members</a>
</div>
<h1 class="page-title">{{ $mode === 'edit' ? 'Edit member' : 'Add member' }}</h1>
<p class="page-subtitle">Contact, assignment, and program details</p>

<div class="card mt-20" style="max-width:720px;">
    <div class="card-body">
        <form method="POST" action="{{ $mode === 'edit' ? route('users.update', $User) : route('users.store') }}">
            @csrf
            @if($mode === 'edit')
                @method('PUT')
            @endif

            <div class="grid-2" style="gap:16px;">
                <div class="form-group">
                    <label class="form-label">First name</label>
                    <input name="first_name" class="form-control" value="{{ old('first_name', $User?->first_name ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Last name</label>
                    <input name="last_name" class="form-control" value="{{ old('last_name', $User?->last_name ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $User?->email ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password {{ $mode === 'edit' ? '(leave blank to keep)' : '' }}</label>
                    <input type="password" name="password" class="form-control" {{ $mode === 'create' ? '' : '' }}>
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control">
                        @php $r = old('role', $User?->role ?? 'disciple'); @endphp
                        <option value="disciple" {{ $r === 'disciple' ? 'selected' : '' }}>Disciple</option>
                        <option value="program_leader" {{ in_array($r, ['program_leader','leader'], true) ? 'selected' : '' }}>Program leader</option>
                        <option value="cell_leader" {{ $r === 'cell_leader' ? 'selected' : '' }}>Cell leader</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Assigned leader</label>
                    <select name="leader_id" class="form-control">
                        <option value="">—</option>
                        @foreach($leaders as $l)
                            <option value="{{ $l->id }}" {{ (string)old('leader_id', $User?->membership?->leader_id ?? '') === (string)$l->id ? 'selected' : '' }}>
                                {{ $l->first_name }} {{ $l->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    @php $st = old('status', $User?->membership?->status ?? 'new_member'); @endphp
                    <select name="status" class="form-control">
                        @foreach(['active','inactive','visitor','new_member'] as $opt)
                            <option value="{{ $opt }}" {{ $st === $opt ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ', $opt)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Training level</label>
                    @php $lv = old('destiny_training_level', $User?->membership?->destiny_training_level ?? 'life_class');
                        $lv = is_object($lv) && property_exists($lv,'value') ? $lv->value : $lv;
                    @endphp
                    <select name="destiny_training_level" class="form-control">
                        @foreach(['life_class','sol1','sol2','sol3','graduate'] as $opt)
                            <option value="{{ $opt }}" {{ (string)$lv === $opt ? 'selected' : '' }}>{{ strtoupper(str_replace('_',' ', $opt)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr style="margin:24px 0;border-color:var(--border-light);">
            <div class="grid-2" style="gap:16px;">
                <div class="form-group">
                    <label class="form-label">Mobile</label>
                    <input name="mobile_number" class="form-control" value="{{ old('mobile_number', $User?->profile?->mobile_number ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Satellite</label>
                    <input name="satellite" class="form-control" value="{{ old('satellite', $User?->membership?->satellite ?? '') }}">
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-warning mt-16">{{ $errors->first() }}</div>
            @endif

            <div class="mt-20">
                <button type="submit" class="btn btn-primary">{{ $mode === 'edit' ? 'Save changes' : 'Create member' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
