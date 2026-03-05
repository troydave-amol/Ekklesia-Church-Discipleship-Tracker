@extends('layouts.auth')
@section('title', 'Sign In')

@section('content')
<h1 class="auth-title">Welcome back</h1>
<p class="auth-subtitle">Sign in to your church account. <a href="{{ route('register') }}">Create one</a></p>

@if($errors->has('email'))
<div class="alert-error">
    <i class="fas fa-exclamation-circle" style="margin-top:1px;flex-shrink:0"></i>
    {{ $errors->first('email') }}
</div>
@endif

<form action="{{ route('login') }}" method="POST">
    @csrf
    <div class="form-group">
        <label class="form-label">Email Address</label>
        <div class="input-wrap">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" class="form-control"
                placeholder="pastor@mychurch.com" value="{{ old('email') }}" required autofocus>
        </div>
    </div>
    <div class="form-group">
        <label class="form-label" style="display:flex;justify-content:space-between">
            Password
            <a href="{{ route('password.request') }}" style="font-weight:400;color:var(--amber);font-size:11.5px;text-decoration:none">Forgot password?</a>
        </label>
        <div class="input-wrap">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
    </div>
    <div class="form-group" style="margin-bottom:20px">
        <label class="form-check">
            <input type="checkbox" name="remember"> Keep me signed in
        </label>
    </div>
    <button type="submit" class="btn-submit">
        <i class="fas fa-arrow-right-to-bracket" style="margin-right:8px"></i> Sign In
    </button>
</form>

<div class="auth-links">
    Don't have a church account? <a href="{{ route('register') }}">Register your church</a>
</div>
@endsection
