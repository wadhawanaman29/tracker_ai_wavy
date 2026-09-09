@component('mail::layout')

{{-- Header --}}
@slot('header')
    @component('mail::header', ['url' => config('app.url')])
        <span style="font-size: 24px; font-weight: bold; color: #4F46E5;">
            Tracker
        </span>
    @endcomponent
@endslot

<div style="padding: 10px 0;">

<h2 style="color:#111827; margin-bottom: 10px;">
    Hello Admin,
</h2>

<p style="font-size:15px; color:#4B5563; line-height:24px;">
    A new leave request has been submitted by
    <strong>{{ $userName }}</strong>.
</p>

<div style="
    background:#F9FAFB;
    border-left:4px solid #4F46E5;
    padding:20px;
    margin:25px 0;
    border-radius:8px;
">

<h3 style="margin-top:0; color:#111827;">
    Leave Request Details
</h3>

@php
    $leaveType = '';

    if ($leaveRequest->leave_type == 1) {
        $leaveType = 'Full Day';
    } elseif ($leaveRequest->leave_type == 2) {
        $leaveType = 'Half Day';
    } elseif ($leaveRequest->leave_type == 3) {
        $leaveType = 'Short Leave';
    }
@endphp

<p style="margin:8px 0;">
    <strong>Leave Type:</strong> {{ $leaveType }}
</p>

@if ($leaveRequest->leave_type == 2 || $leaveRequest->leave_type == 3)
<p style="margin:8px 0;">
    <strong>Time:</strong>
    {{ date('h:i A', strtotime($leaveRequest->from_time)) }}
    -
    {{ date('h:i A', strtotime($leaveRequest->to_time)) }}
</p>
@endif

@if ($leaveRequest->leave_type == 1)
<p style="margin:8px 0;">
    <strong>Total Days:</strong>
    {{ $leaveRequest->total_leave_days }} Day(s)
</p>
@endif

<p style="margin:8px 0;">
    <strong>Reason:</strong> {{ $leaveRequest->reason }}
</p>

@if (!empty($leaveRequest->leave_time))
<p style="margin:8px 0;">
    <strong>Leave Time:</strong> {{ $leaveRequest->leave_time }}
</p>
@endif

<p style="margin:8px 0;">
    <strong>Start Date:</strong> {{ $leaveRequest->start_date }}
</p>

<p style="margin:8px 0;">
    <strong>End Date:</strong> {{ $leaveRequest->end_date }}
</p>

</div>

<p style="margin-top:25px;">
    Best regards,<br>
    <strong>{{ $userName }}</strong>
</p>

<hr style="margin:30px 0; border:none; border-top:1px solid #E5E7EB;">

<p style="font-size:13px; color:#6B7280; line-height:22px;">
    This email was sent from your website
    <strong>"Wavy Informatics"</strong> by the tracker at
    {{ \Carbon\Carbon::now()->format('l, jS \of F Y \at h:i:s A') }}.
</p>

<p style="font-size:13px; color:#6B7280;">
    To manage security settings, visit:
    <a href="https://tracker.wavyinformatics.com/" style="color:#4F46E5;">
        tracker.wavyinformatics.com
    </a>
</p>

</div>

@component('mail::subcopy')
This email contains important information regarding recent activities on your site.
@endcomponent

{{-- Footer --}}
@slot('footer')
    @component('mail::footer')
        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        <br>
        This is an automated email. Please do not reply.
    @endcomponent
@endslot

@endcomponent