@component('mail::layout')
{{-- Header --}}
@slot('header')
    @component('mail::header', ['url' => config('app.url')])
        Tracker
    @endcomponent
@endslot

Hello,

This email is to inform you about recent activity on your website.

---

**Details:**

- **Action:** A non-admin user with username <strong>{{ $userName }}</strong> signed in to your Tracker site.
- **IP Address:** {{ get_client_ip() }}
- **Hostname:** {{ get_client_ip() }}
- **Location:** Chandigarh, India

---

This email was sent from your website "Wavy Informatics" by the tracker at {{ \Carbon\Carbon::now()->format('l, jS \of F Y \at h:i:s A') }}.<br>
To manage security settings, visit the tracker URL:(https://tracker.wavyinformatics.com/)

@component('mail::subcopy')
    This email contains important information regarding recent activities on your site.
@endcomponent

@slot('footer')
    @component('mail::footer')
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        <br>
        This is an automated email. Please do not reply.
    @endcomponent
@endslot

@endcomponent
