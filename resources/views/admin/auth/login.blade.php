@extends('layouts.admin-auth')

@section('title', 'Admin Login')

@section('content')
<div class="ca-login-shell">
    <div class="ca-login-container">
        {{-- Branding Section --}}
        <div class="ca-login-brand">
            <div class="ca-login-logo">
                <span class="ca-login-logo-text">Ekk<em>l</em>esia</span>
                <span class="ca-login-logo-tag">Control Plane</span>
            </div>
            <p class="ca-login-desc">Central Administration</p>
        </div>

        {{-- Login Card --}}
        <div class="ca-login-card">
            <h2 class="ca-login-title">Welcome Back</h2>
            <p class="ca-login-subtitle">Sign in to access the admin dashboard</p>

            {{-- Error Messages --}}
            @if($errors->any())
                <div class="ca-login-error">
                    <svg class="ca-error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('admin.login.submit') }}" class="ca-login-form">
                @csrf

                <div class="ca-form-group">
                    <label for="email" class="ca-form-label">Email Address</label>
                    <input type="email" name="email" id="email" 
                           class="ca-form-input @error('email') ca-form-input-error @enderror" 
                           value="{{ old('email') }}" 
                           placeholder="admin@church.local"
                           required autofocus>
                    @error('email')
                        <p class="ca-form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="ca-form-group">
                    <label for="password" class="ca-form-label">Password</label>
                    <input type="password" name="password" id="password" 
                           class="ca-form-input @error('password') ca-form-input-error @enderror"
                           placeholder="Enter your password"
                           required>
                    @error('password')
                        <p class="ca-form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="ca-form-checkbox">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Keep me signed in</label>
                </div>

                <button type="submit" class="ca-login-button">
                    <span>Sign In</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>

            {{-- Footer --}}
            <p class="ca-login-footer">
                <a href="mailto:support@ekklesia.app">Need help?</a>
            </p>
        </div>
    </div>
</div>
@endsection
