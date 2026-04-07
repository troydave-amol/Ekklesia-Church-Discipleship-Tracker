@component('mail::message')
# Registration Update for {{ $churchName }}

Dear {{ $pastorName }},

Thank you for your interest in Ekklesia. After careful review, we were unable to approve your church registration at this time.

@if($rejectionNote)
**Reason provided by our team:**

> {{ $rejectionNote }}
@endif

If you believe this was a mistake, or if you'd like to re-apply with updated information, you're welcome to submit a new registration at any time.

@component('mail::button', ['url' => url('/register')])
Submit a New Registration
@endcomponent

If you have questions, simply reply to this email — our team is happy to help.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
