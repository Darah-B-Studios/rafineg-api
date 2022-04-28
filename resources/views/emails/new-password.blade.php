@component('mail::message')
    # New password

    Hello {{ $username }},
    You have successfully requested for a new password. You can find the password below.
    > *{{ $newPassword }}*

    We encourage to go in and reset this password so that it will not be compromized.
    Thank you,
    {{ config('app.name') }} team.
@endcomponent
