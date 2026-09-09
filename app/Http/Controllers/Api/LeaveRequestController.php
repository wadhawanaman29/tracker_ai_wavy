<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\LeaveRequest;
use App\Notifications\LeaveRequestNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::where(
            'user_id',
            Auth::id()
        );

        if ($request->filter == 'this_month') {

            $query->whereMonth(
                'start_date',
                Carbon::now()->month
            )->whereYear(
                'start_date',
                Carbon::now()->year
            );
        }

        if ($request->filter == 'last_month') {

            $query->whereMonth(
                'start_date',
                Carbon::now()->subMonth()->month
            )->whereYear(
                'start_date',
                Carbon::now()->subMonth()->year
            );
        }

        $leaveRequests = $query
            ->orderBy('id', 'desc')
            ->paginate(5);

        /*
        |--------------------------------------------------------------------------
        | Leave Summary Calculation
        |--------------------------------------------------------------------------
        */

        $currentMonthLeaves = LeaveRequest::where(
            'user_id',
            Auth::id()
        )
            ->whereMonth(
                'start_date',
                Carbon::now()->month
            )
            ->whereYear(
                'start_date',
                Carbon::now()->year
            )
            ->where('status', 'approved')
            ->get();

        // Full Leave
        $usedLeaves = $currentMonthLeaves
            ->where('total_leave_days', '>=', 1)
            ->sum('total_leave_days');

        $remainingLeaves = max(
            0,
            1 - $usedLeaves
        );

        // Short Leave
        $usedShortLeaves = $currentMonthLeaves
            ->filter(function ($leave) {
                return $leave->total_leave_days == 0.5
                    && !empty($leave->leave_time);
            })
            ->count();

        $remainingShortLeaves = max(
            0,
            2 - $usedShortLeaves
        );

        // Half Day
        $usedHalfDays = $currentMonthLeaves
            ->filter(function ($leave) {
                return $leave->total_leave_days == 0.5
                    && empty($leave->leave_time);
            })
            ->count();

        $remainingHalfDays = max(
            0,
            2 - $usedHalfDays
        );
return response()->json([
    'success' => true,

    'data' => $leaveRequests->items(),

    'pagination' => [
        'current_page' => $leaveRequests->currentPage(),
        'last_page' => $leaveRequests->lastPage(),
        'per_page' => $leaveRequests->perPage(),
        'total' => $leaveRequests->total(),
    ],

    'remainingLeaves' => $remainingLeaves,
    'usedLeaves' => $usedLeaves,

    'remainingShortLeaves' => $remainingShortLeaves,
    'usedShortLeaves' => $usedShortLeaves,

    'remainingHalfDays' => $remainingHalfDays,
    'usedHalfDays' => $usedHalfDays,
]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'reason' => 'required|string',
            'start_date' => 'required|date',
            'leave_type' => 'required',
        ]);
        if (in_array($request->leave_type, [2, 3])) {

            $request->validate([
                'from_time' => 'required',
                'to_time'   => 'required',
            ]);

            $fromTime = strtotime($request->from_time);
            $toTime   = strtotime($request->to_time);

            if ($toTime <= $fromTime) {

                return response()->json([
                    'success' => false,
                    'message' => 'To Time must be greater than From Time'
                ], 422);
            }

            $hours = ($toTime - $fromTime) / 3600;

            if ($request->leave_type == 2 && $hours > 4) {

                return response()->json([
                    'success' => false,
                    'message' => 'Half Day leave cannot exceed 4 hours'
                ], 422);
            }

            if ($request->leave_type == 3 && $hours > 2) {

                return response()->json([
                    'success' => false,
                    'message' => 'Short Leave cannot exceed 2 hours'
                ], 422);
            }
        }
 if ($request->leave_type == 1) {

    $request->validate([
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    $start = new \DateTime($request->start_date);
    $end   = new \DateTime($request->end_date);

    $total_leave_days = $start->diff($end)->days + 1;

} elseif ($request->leave_type == 2) {

    // Half Day
    $total_leave_days = 0.5;

} else {

    // Short Leave
    $total_leave_days = 0;
}

        // Create Leave
     $leaveRequest = LeaveRequest::create([
    'user_id'          => Auth::id(),
    'leave_type'       => $request->leave_type,
    'reason'           => $request->reason,
    'start_date'       => $request->start_date,
    'end_date'         => $request->end_date ?: $request->start_date,
    'from_time'        => $request->from_time,
    'to_time'          => $request->to_time,
    'leave_time'       => $request->leave_time,
    'total_leave_days' => $total_leave_days,
    'status'           => 'pending',
]);

        // Mail Notification with proper rate limiting
        $userName = check_user_name($leaveRequest->user_id);

        $emails = GeneralSetting::where(
            'setting_key',
            'email'
        )->pluck('setting_value')->toArray();

        if (!empty($emails)) {

            $json_emails = json_decode($emails[0]);

            foreach ($json_emails as $index => $email) {

                // Increased delay from 2 to 5 seconds to avoid Mailtrap rate limiting
                // Mailtrap free plan allows 1 email per second
                if ($index > 0) {
                    sleep(5);
                }

                try {
                    Notification::route('mail', $email)
                        ->notify(
                            new LeaveRequestNotification(
                                $leaveRequest,
                                $userName
                            )
                        );
                } catch (\Exception $e) {
                    \Log::error('Leave notification email failed: ' . $e->getMessage(), [
                        'email' => $email,
                        'leave_request_id' => $leaveRequest->id
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Leave request submitted successfully.',
            'data' => $leaveRequest
        ]);
    }

    public function show($id)
    {
        $leave = LeaveRequest::where(
            'user_id',
            Auth::id()
        )->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $leave
        ]);
    }

    public function delete($id)
    {
        $leave = LeaveRequest::where(
            'user_id',
            Auth::id()
        )->findOrFail($id);

        $leave->delete();

        return response()->json([
            'success' => true,
            'message' => 'Leave deleted successfully.'
        ]);
    }
}
