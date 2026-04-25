@extends('layouts.auth')
@section('title', 'Join this Church')

@section('content')
<h1 class="auth-title">Join this church</h1>
<p class="auth-subtitle">Request to become a member. Your pastor will review and approve your registration.</p>

@if($errors->any())
<div class="alert-error">
    <i class="fas fa-exclamation-circle"></i>
    {{ $errors->first() }}
</div>
@endif

@if(session('success'))
<div style="background:rgba(90,122,95,.08);color:var(--sage);border:1px solid rgba(90,122,95,.18);border-radius:10px;padding:12px 14px;font-size:13px;margin-bottom:16px;display:flex;gap:10px;align-items:flex-start;animation:fadeUp .3s ease;">
    <i class="fas fa-check-circle" style="margin-top:1px;flex-shrink:0;"></i>
    {{ session('success') }}
</div>
@endif

<form action="{{ route('member.register.submit') }}" method="POST">
    @csrf

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="form-group">
            <label class="form-label">First Name</label>
            <div class="input-wrap">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="first_name" class="form-control"
                    placeholder="Juan" value="{{ old('first_name') }}" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Last Name</label>
            <div class="input-wrap">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="last_name" class="form-control"
                    placeholder="Dela Cruz" value="{{ old('last_name') }}" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Email Address</label>
        <div class="input-wrap">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" class="form-control"
                placeholder="you@example.com" value="{{ old('email') }}" required>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Password</label>
        <div class="input-wrap">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password" id="password" class="form-control"
                placeholder="••••••••" required minlength="8">
            <button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="Toggle password visibility">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <div class="input-wrap">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                placeholder="••••••••" required>
            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)" aria-label="Toggle password visibility">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">I want to join as</label>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <label class="plan-card {{ old('requested_role', 'disciple') === 'disciple' ? 'selected' : '' }}"
                   onclick="selectRole(this, 'disciple')" style="padding:12px 14px;cursor:pointer;">
                <input type="radio" name="requested_role" value="disciple"
                    {{ old('requested_role', 'disciple') === 'disciple' ? 'checked' : '' }}>
                <div class="plan-card-name" style="font-size:13px;">
                    <i class="fas fa-seedling" style="color:var(--sage);margin-right:4px;"></i> Disciple
                </div>
                <div class="plan-card-desc" style="margin-top:4px;">Join a discipleship group and grow in faith</div>
            </label>
            <label class="plan-card {{ old('requested_role') === 'program_leader' ? 'selected' : '' }}"
                   onclick="selectRole(this, 'program_leader')" style="padding:12px 14px;cursor:pointer;">
                <input type="radio" name="requested_role" value="program_leader"
                    {{ old('requested_role') === 'program_leader' ? 'checked' : '' }}>
                <div class="plan-card-name" style="font-size:13px;">
                    <i class="fas fa-hands-helping" style="color:var(--amber);margin-right:4px;"></i> Leader
                </div>
                <div class="plan-card-desc" style="margin-top:4px;">Serve as a program or cell group leader</div>
            </label>
        </div>
    </div>

    <button type="submit" class="btn-submit">
        <i class="fas fa-paper-plane" style="margin-right:8px"></i> Request to Join
    </button>
</form>

<div class="auth-links" style="margin-top:24px">
    Already have an account? <a href="{{ route('login') }}">Sign in</a>
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

function selectRole(el, role) {
    document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input[type=radio]').checked = true;
}
</script>
@endpush
