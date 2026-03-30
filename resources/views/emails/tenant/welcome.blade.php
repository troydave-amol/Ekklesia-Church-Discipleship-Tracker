@component('mail::message')
# Welcome to Church Discipleship Tracker, {{ $tenantName }}!

Your workspace is ready. Here's how to get started:

**Your login URL:**
@component('mail::button', ['url' => $loginUrl])
Go to your workspace
@endcomponent

**Your subdomain:** `{{ $subdomain }}.{{ config('app.domain') }}`

**Trial period:** Your 14-day free trial ends on **{{ $trialEnds }}**.

If you have any questions, just reply to this email.

Thanks,
{{ config('app.name') }}
@endcomponent