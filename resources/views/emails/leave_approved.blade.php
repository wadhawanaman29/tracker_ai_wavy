<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Leave Approved</title>
</head>

<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, sans-serif;">

    <!-- Container -->
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">
        <tr>
            <td align="center">

                <!-- Card -->
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background:#696cff; padding:20px; text-align:center;">
                            <h2 style="color:#ffffff; margin:0;">Tracker</h2>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px; color:#374151;">

                            <h3 style="margin-top:0;">Hello {{ $userName }},</h3>

                            <p style="font-size:15px;">
                                Your leave request has been
                                <strong style="color:#16a34a;">Approved Successfully</strong>.
                            </p>

                            <!-- Box -->
                            <div
                                style="background:#f9fafb; border-left:5px solid #696cff; padding:20px; border-radius:8px; margin:20px 0;">

                                <h4 style="margin-top:0; color:#111827;">Leave Details</h4>

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
                                        <strong>Leave Time:</strong>
                                        {{ $leaveRequest->leave_time }}
                                    </p>
                                @endif
                                <p style="margin:5px 0;">
                                    <strong>Start Date:</strong>
                                    {{ $leaveRequest->start_date }}
                                </p>

                                <p style="margin:5px 0;">
                                    <strong>End Date:</strong>
                                    {{ $leaveRequest->end_date }}
                                </p>

                                <p style="margin:5px 0;">
                                    <strong>Applied On:</strong>
                                    {{ \Carbon\Carbon::parse($leaveRequest->created_at)->format('d F Y h:i A') }}
                                </p>

                                @if (!empty($leaveRequest->action_reason))
                                    <p style="margin:5px 0;">
                                        <strong>Admin Remark:</strong>
                                        {{ $leaveRequest->action_reason }}
                                    </p>
                                @endif

                            </div>

                            <p>
                                Best regards,<br>
                                <strong>{{ config('app.name') }}</strong>
                            </p>

                            <hr style="border:none; border-top:1px solid #e5e7eb; margin:25px 0;">

                            <p style="font-size:12px; color:#6b7280;">
                                This email was sent from "Wavy Informatics" via tracker at<br>
                                {{ \Carbon\Carbon::now()->format('l, jS \\of F Y \\at h:i:s A') }}
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f9fafb; text-align:center; padding:15px; font-size:12px; color:#6b7280;">
                            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            <br>
                            This is an automated email. Please do not reply.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
