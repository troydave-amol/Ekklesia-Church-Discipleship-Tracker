@component('mail::message')
# Welcome to Ekklesia, {{ $tenantName }}! 🎉

Your church's dedicated discipleship platform has been approved and is now **live**.

---

## Your Workspace Details

@component('mail::table')
| Detail | Value |
|:-------|:------|
| **Church Name** | {{ $tenantName }} |
| **Subdomain** | `{{ $subdomain }}.{{ config('app.domain') }}` |
| **Subscription Plan** | {{ $plan }} |
| **Trial Ends** | {{ $trialEnds ?? 'N/A' }} |
@endcomponent

---

## Your Login Credentials

@component('mail::table')
| | |
|:-------|:------|
| **Username (Email)** | {{ $loginEmail }} |
| **Password** | {{ $loginPassword }} |
@endcomponent

@component('mail::panel')
🔒 For your security, we recommend changing your password after your first login.
@endcomponent

---

## Getting Started

1. **Log in** to your workspace using the credentials above.
2. **Set up programs** — create your discipleship tracks (Life Class, One-to-One, etc.).
3. **Add leaders** — invite your small group leaders to start managing disciples.
4. **Track attendance** — monitor weekly attendance and spiritual growth metrics.

@component('mail::button', ['url' => $loginUrl, 'color' => 'success'])
Go to Your Workspace
@endcomponent

If you have any questions or need help getting started, simply reply to this email — our team is here for you.

God bless,<br>
**The Ekklesia Team**

@component('mail::subcopy')
Your login URL: [{{ $loginUrl }}]({{ $loginUrl }})
@endcomponent

@endcomponent