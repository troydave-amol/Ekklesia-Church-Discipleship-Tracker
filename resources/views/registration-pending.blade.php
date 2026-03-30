<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration received — Ekklesia</title>
    @vite(['resources/css/app.css', 'resources/css/base.css', 'resources/css/landing.css'])
</head>
<body style="margin:0;font-family:system-ui,sans-serif;background:#f7f3ee;color:#1a1a1a;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;">
    <div style="max-width:520px;background:#fff;border:1px solid #e8e0d5;border-radius:16px;padding:2rem;box-shadow:0 8px 32px rgba(0,0,0,0.06);">
        <div style="font-size:2rem;margin-bottom:0.5rem;">✉️</div>
        <h1 style="font-size:1.35rem;margin:0 0 0.75rem;">Registration received</h1>
        <p style="line-height:1.6;color:#444;margin:0 0 1rem;">
            Thank you for registering your church. The <strong>central app</strong> team will review your request before creating your subdomain and database.
            You will receive an email when your workspace is ready.
        </p>
        <p style="line-height:1.6;color:#666;font-size:0.9rem;margin:0 0 1.5rem;">
            Monthly plans (Free, Basic ₱800, Standard ₱1,500, Premium ₱2,500) are billed per church after approval, according to the plan you selected.
        </p>
        <a href="{{ route('home') }}" style="display:inline-block;padding:0.65rem 1.25rem;background:#4a6741;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">Back to home</a>
    </div>
</body>
</html>
