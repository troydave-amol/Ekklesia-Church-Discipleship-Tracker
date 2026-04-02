@extends('layouts.app')
@section('title', $user->fullName())

@section('content')
<div class="mb-20">
    <a href="{{ auth()->user()->isChurchAdmin() ? route('users.index') : route('dashboard') }}" class="btn btn-ghost btn-sm">← {{ auth()->user()->isChurchAdmin() ? 'Members' : 'Dashboard' }}</a>
    @if(auth()->user()->isChurchAdmin())
    <a href="{{ route('users.edit', $user) }}" class="btn btn-ghost btn-sm">Edit</a>
    @endif
</div>

<div class="card">
    <div class="card-body">
        <h1 class="page-title">{{ $user->first_name }} {{ $user->last_name }}</h1>
        <p class="page-subtitle">{{ $user->email }} · {{ ucfirst(str_replace('_',' ', $user->role)) }}</p>
        @if($user->membership)
        <div class="mt-16" style="font-size:14px;color:var(--text-muted);line-height:1.7;">
            <div><strong>Status:</strong> {{ strtoupper(str_replace('_',' ', $user->membership->status ?? '')) }}</div>
            <div><strong>Training:</strong> {{ strtoupper(str_replace('_',' ', (string)($user->membership->destiny_training_level ?? ''))) }}</div>
            @if($user->membership->leader)
            <div><strong>Leader:</strong> {{ $user->membership->leader->first_name }} {{ $user->membership->leader->last_name }}</div>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
