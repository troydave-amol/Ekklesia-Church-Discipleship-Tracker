@extends('layouts.auth')
@section('title', 'Sign In')

@section('content')
<h1 class="auth-title">Welcome back</h1>
<p class="auth-subtitle">Sign in to your church account</p>

@if($errors->has('email'))
<div class="alert-error">
    <i class="fas fa-exclamation-circle"></i>
    {{ $errors->first('email') }}
</div>
@endif

<form action="{{ route('tenant.login') }}" method="POST">
    @csrf
    <div class="form-group">
        <label class="form-label">Email Address</label>
        <div class="input-wrap">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" id="email" class="form-control"
                placeholder="pastor@mychurch.com" value="{{ old('email') }}" required autofocus>
        </div>
    </div>
    <div class="form-group">
        <label class="form-label" style="display:flex;justify-content:space-between;align-items:center">
            Password
            <a href="{{ route('password.request') }}" style="font-weight:400;color:var(--amber);font-size:11.5px;text-decoration:none;transition:color .2s" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--amber)'">Forgot password?</a>
        </label>
        <div class="input-wrap">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            <button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="Toggle password visibility">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </div>
    <div class="form-group" style="margin-bottom:22px">
        <label class="form-check">
            <input type="checkbox" name="remember"> Keep me signed in
        </label>
    </div>
    <button type="submit" class="btn-submit">
        <i class="fas fa-arrow-right-to-bracket" style="margin-right:8px"></i> Sign In
    </button>
</form>

<div class="auth-links" style="margin-top:24px">
    Don't have a church account? <a href="{{ route('register') }}">Register your church</a>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endpush
