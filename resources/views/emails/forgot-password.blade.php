@component('mail::message')
    # Rafineg Password Reset

    Hello dear user,
    You requested a password reset from your mobile client.
    Here is the reset code: *{{ $verification_code }}*

    If you did not request this, feel free to ignore this message.

    Thank you,<br>
    {{ config('app.name') }} team.
@endcomponent
