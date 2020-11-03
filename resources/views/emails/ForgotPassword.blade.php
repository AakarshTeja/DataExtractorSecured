@component('mail::message')
# Forgot Password Mail
**Hi {{strtoupper($user)}},** <br>
<p>
    Please go through the recovery process before the link gets expired<br> 
    Click the button to recover your password.If the button doesn't work,
     please click below link or copy paste the link in your browser for furthur process.<br> 
     This link expires after {{$hours}} hours.
    <br>
<a href='{{$url}}'>{{$url}}</a><br><br>
</p>

@component('mail::button', ['url' => $url])
Recover Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
