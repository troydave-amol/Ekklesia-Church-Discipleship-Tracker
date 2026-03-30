<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register — {{ config('app.name') }}</title>
</head>
<body>

<h1>Create your church workspace</h1>

@if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register.submit') }}">
    @csrf

    {{-- Church / Organization Name --}}
    <div>
        <label for="name">Church Name</label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name') }}"
            required
            placeholder="e.g. Grace Community Church"
        >
        @error('name')
            <span>{{ $message }}</span>
        @enderror
    </div>

    {{-- Admin Email --}}
    <div>
        <label for="email">Admin Email</label>
        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            required
            placeholder="pastor@yourdomain.com"
        >
        @error('email')
            <span>{{ $message }}</span>
        @enderror
    </div>

    {{-- Password --}}
    <div>
        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            required
            minlength="8"
        >
        @error('password')
            <span>{{ $message }}</span>
        @enderror
    </div>

    {{-- Password Confirm --}}
    <div>
        <label for="password_confirmation">Confirm Password</label>
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            required
        >
    </div>

    {{-- Subdomain --}}
    <div>
        <label for="subdomain">Your Subdomain</label>
        <div>
            <input
                type="text"
                id="subdomain"
                name="subdomain"
                value="{{ old('subdomain') }}"
                required
                minlength="3"
                maxlength="32"
                placeholder="gracechurch"
                oninput="checkSubdomain(this.value)"
            >
            <span>.{{ config('app.domain') }}</span>
        </div>
        <small id="subdomain-feedback"></small>
        @error('subdomain')
            <span>{{ $message }}</span>
        @enderror
    </div>

    <button type="submit">Create Workspace</button>

    <p>Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
</form>

<script>
    let subdomainTimer = null;

    function checkSubdomain(value) {
        const feedback = document.getElementById('subdomain-feedback');

        clearTimeout(subdomainTimer);

        if (value.length < 3) {
            feedback.textContent = '';
            return;
        }

        feedback.textContent = 'Checking...';

        subdomainTimer = setTimeout(() => {
            fetch(`/check-subdomain?subdomain=${encodeURIComponent(value)}`)
                .then(res => res.json())
                .then(data => {
                    feedback.textContent = data.message;
                    feedback.style.color  = data.available ? 'green' : 'red';
                })
                .catch(() => {
                    feedback.textContent = 'Could not check availability.';
                });
        }, 500); // debounce — waits 500ms after user stops typing
    }
</script>

</body>
</html>
```

Then visit:
```
http://127.0.0.1:8000/register