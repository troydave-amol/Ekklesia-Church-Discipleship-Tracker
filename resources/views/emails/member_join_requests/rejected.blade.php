@component('mail::message')
# Your join request was not approved

Hi {{ $memberName }},

Your recent request to join this church was not approved at this time.

@if($rejectionNote)
**Note from the church administrator:**

> {{ $rejectionNote }}
@endif

You can submit a new request whenever you're ready.

@component('mail::button', ['url' => $reapplyUrl])
Submit a New Join Request
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
