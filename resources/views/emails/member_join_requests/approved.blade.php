@component('mail::message')
# Your join request was approved

Hi {{ $memberName }},

Your request to join this church as a **{{ $roleLabel }}** has been approved. You can now sign in using the email address below.

@component('mail::table')
| Detail | Value |
|:-------|:------|
| **Email** | {{ $email }} |
| **Role** | {{ $roleLabel }} |
@endcomponent

@component('mail::button', ['url' => $loginUrl, 'color' => 'success'])
Sign In to Your Church
@endcomponent

If you did not expect this message, please contact the church administrator directly.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
