@extends('layouts.auth')
@section('title', 'Register Your Church')

@section('content')
<h1 class="auth-title">Register your church</h1>
<p class="auth-subtitle">Start your free account. No credit card required.</p>

<form action="{{ route('register') }}" method="POST">
    @csrf

    {{-- Church Info --}}
    <div class="church-info-section">
        <div class="church-info-header">
            <i class="fas fa-church"></i> Church Information
        </div>
        <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Church Name</label>
            <div class="input-wrap">
                <i class="fas fa-building input-icon"></i>
                <input type="text" name="church_name" class="form-control" placeholder="Grace Community Church" value="{{ old('church_name') }}" required>
            </div>
        </div>
    </div>

    {{-- Pastor Info --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
        <div class="form-group" style="margin-bottom:0">
            <label class="form-label">First Name</label>
            <div class="input-wrap">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="first_name" class="form-control" placeholder="Juan" value="{{ old('first_name') }}" required>
            </div>
        </div>
        <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control no-icon" placeholder="dela Cruz" value="{{ old('last_name') }}" required>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Email Address</label>
        <div class="input-wrap">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" class="form-control" placeholder="admin@library.com" value="{{ old('email') }}" required>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:18px;">
        <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password" id="reg-password" class="form-control" placeholder="••••••••" required>
                <button type="button" class="password-toggle" onclick="togglePassword('reg-password', this)" aria-label="Toggle password visibility">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>
        <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Confirm Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password_confirmation" id="reg-password-confirm" class="form-control" placeholder="••••••••" required>
                <button type="button" class="password-toggle" onclick="togglePassword('reg-password-confirm', this)" aria-label="Toggle password visibility">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Plan Selection --}}
    <div style="margin-bottom:20px;">
        <div class="form-label">Choose a Plan</div>
        <div class="plan-cards">
            <label class="plan-card selected" id="plan-free" onclick="selectPlan(this,'free')">
                <input type="radio" name="subscription_plan" value="free" checked>
                <div class="plan-card-name">Free</div>
                <div class="plan-card-price">₱0</div>
                <div class="plan-card-desc">1 program · 20 disciples</div>
            </label>
            <label class="plan-card" id="plan-basic" onclick="selectPlan(this,'basic')">
                <input type="radio" name="subscription_plan" value="basic">
                <div class="plan-card-name">Basic</div>
                <div class="plan-card-price">₱800<span style="font-size:11px;font-weight:400;color:var(--muted)">/mo</span></div>
                <div class="plan-card-desc">4 programs · 50 disciples</div>
            </label>
            <label class="plan-card" id="plan-standard" onclick="selectPlan(this,'standard')">
                <input type="radio" name="subscription_plan" value="standard">
                <div class="plan-card-name">Standard</div>
                <div class="plan-card-price">₱1,500<span style="font-size:11px;font-weight:400;color:var(--muted)">/mo</span></div>
                <div class="plan-card-desc">Unlimited disciples · Exports</div>
            </label>
            <label class="plan-card" id="plan-premium" onclick="selectPlan(this,'premium')">
                <input type="radio" name="subscription_plan" value="premium">
                <div class="plan-card-name" style="color:var(--amber)">⭐ Premium</div>
                <div class="plan-card-price">₱2,500<span style="font-size:11px;font-weight:400;color:var(--muted)">/mo</span></div>
                <div class="plan-card-desc">All features · Priority support</div>
            </label>
        </div>
    </div>

    @if($errors->any())
    <div class="alert-error" style="margin-bottom:14px">
        <i class="fas fa-exclamation-circle"></i>
        <div>{{ $errors->first() }}</div>
    </div>
    @endif

    <button type="submit" class="btn-submit">Create Church Account</button>
</form>

<div class="auth-links" style="margin-top:18px">
    Already have an account? <a href="{{ route('login') }}">Sign in</a>
</div>
@endsection

@push('scripts')
<script>
function selectPlan(el, plan) {
    document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
}
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
