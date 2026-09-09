<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

use App\Notifications\LeaveRequestNotification;
use App\Notifications\LeaveApproveNotification;
use App\Notifications\LeaveRejectNotification;

class LeaveRequestController extends Controller
{

    public function index(Request $request)
    {
        $endDate = $request->end
            ? Carbon::parse($request->end)->addDay()
            : Carbon::now()->addDay();
        if (
            !empty($request->user_id) &&
            $request->user_id !== 'all' &&
            !empty($request->start)
        ) {

            $leaveRequests = LeaveRequest::where(
                'user_id',
                $request->user_id
            )
                ->whereBetween('created_at', [
                    $request->start,
                    $endDate
                ]);

            if (
                !empty($request->status) &&
                $request->status !== 'all'
            ) {

                $leaveRequests->where(
                    'status',
                    $request->status
                );
            }

            $leaveRequests = $leaveRequests
                ->orderBy('id', 'desc')
                ->paginate(20);
        } elseif (
            !empty($request->user_id) &&
            $request->user_id === 'all' &&
            !empty($request->start)
        ) {

            $leaveRequests = LeaveRequest::whereBetween(
                'created_at',
                [$request->start, $endDate]
            );

            if (
                !empty($request->status) &&
                $request->status !== 'all'
            ) {

                $leaveRequests->where(
                    'status',
                    $request->status
                );
            }

            $leaveRequests = $leaveRequests
                ->orderBy('id', 'desc')
                ->paginate(20);
        } else {

            $leaveRequests = LeaveRequest::orderBy(
                'id',
                'desc'
            )->paginate(20);
        }
        $filterType = $request->filter_type ?? 'this_month';

        $userleaveRequests = LeaveRequest::where(
            'user_id',
            Auth::id()
        );

        $summaryQuery = LeaveRequest::where(
            'user_id',
            Auth::id()
        )->where(
            'status',
            'approved'
        );

        if ($filterType == 'this_month') {

            $startDate = Carbon::now()
                ->startOfMonth();

            $endDate = Carbon::now()
                ->endOfMonth();

            $userleaveRequests->whereBetween(
                'start_date',
                [$startDate, $endDate]
            );

            $summaryQuery->whereBetween(
                'start_date',
                [$startDate, $endDate]
            );
        } elseif ($filterType == 'last_month') {

            $startDate = Carbon::now()
                ->subMonth()
                ->startOfMonth();

            $endDate = Carbon::now()
                ->subMonth()
                ->endOfMonth();

            $userleaveRequests->whereBetween(
                'start_date',
                [$startDate, $endDate]
            );

            $summaryQuery->whereBetween(
                'start_date',
                [$startDate, $endDate]
            );
        } elseif ($filterType == 'custom_range') {

            if (
                !empty($request->start_date) &&
                !empty($request->end_date)
            ) {

                $startDate = Carbon::parse(
                    $request->start_date
                )->startOfDay();

                $endDate = Carbon::parse(
                    $request->end_date
                )->endOfDay();

                $userleaveRequests->whereBetween(
                    'start_date',
                    [$startDate, $endDate]
                );

                $summaryQuery->whereBetween(
                    'start_date',
                    [$startDate, $endDate]
                );
            }
        }
        // Permanent employees get ONE combined monthly quota of 1.5
        // leave-days — 1 full day + 0.5 day usable as either a half day
        // or 2 short leaves (each short leave = 0.25 day). This replaces
        // the old three-independent-allowance model (1 leave AND 2 short
        // leaves AND 2 half days, all separately available), which didn't
        // match how leave is actually granted. Employees on probation get
        // no leave allowance at all.
        $monthlyLeaveDayQuota = optional(Auth::user())->employment_status === 'probation'
            ? 0
            : 1.5;

        $usedLeaveDays = (clone $summaryQuery)->sum('total_leave_days');

        $usedShortLeaves = (clone $summaryQuery)
            ->where('leave_type', 3)
            ->count();

        $usedHalfDays = (clone $summaryQuery)
            ->where('leave_type', 2)
            ->count();

        $usedFullDays = (clone $summaryQuery)
            ->where('leave_type', 1)
            ->count();

        $remainingLeaveDays = max(0, $monthlyLeaveDayQuota - $usedLeaveDays);

        // Kept for view backward-compatibility — these all now draw from
        // the single shared quota above rather than being independent.
        $remainingLeaves = $remainingLeaveDays;
        $remainingShortLeaves = $remainingLeaveDays;
        $remainingHalfDays = $remainingLeaveDays;
        $usedLeaves = $usedLeaveDays;

        $userleaveRequests = $userleaveRequests
            ->with('user')
            ->orderBy('id', 'desc')
            ->get();

        // echo"<pre>";
        // print_r($userleaveRequests->toArray());
        // die("popo");
        return view('leave-requests.index', compact(
            'leaveRequests',
            'userleaveRequests',
            'remainingLeaves',
            'remainingShortLeaves',
            'remainingHalfDays',
            'usedLeaves',
            'usedShortLeaves',
            'usedHalfDays',
            'usedFullDays',
            'monthlyLeaveDayQuota',
            'usedLeaveDays',
            'remainingLeaveDays',
            'filterType'
        ));
    }


    public function create()
    {
        return view('leave-requests.create');
    }

    /**
     * Store a newly created leave request in storage.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (optional(Auth::user())->employment_status === 'probation') {
            return back()->withErrors([
                'reason' => 'Employees on probation are not eligible for leave.',
            ])->withInput();
        }

        $request->validate([
            'reason' => 'required|string',
            'start_date' => 'required|date',
            'leave_type' => 'required',
        ]);

        // Time validation for Half Day & Short Leave
        if (in_array($request->leave_type, [2, 3])) {

            $request->validate([
                'from_time' => 'required',
                'to_time'   => 'required',
            ]);

            $fromTime = strtotime($request->from_time);
            $toTime   = strtotime($request->to_time);

            if ($toTime <= $fromTime) {
                return back()->withErrors([
                    'to_time' => 'To Time must be greater than From Time.'
                ])->withInput();
            }

            $hours = ($toTime - $fromTime) / 3600;

            // Half Day => Max 4 Hours
            if ($request->leave_type == 2 && $hours > 4) {
                return back()->withErrors([
                    'to_time' => 'Half Day leave cannot exceed 4 hours.'
                ])->withInput();
            }

            // Short Leave => Max 2 Hours
            if ($request->leave_type == 3 && $hours > 2) {
                return back()->withErrors([
                    'to_time' => 'Short Leave cannot exceed 2 hours.'
                ])->withInput();
            }
        }

        // Calculate leave days
        if ($request->leave_type == 1) {

            $request->validate([
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $start = new \DateTime($request->start_date);
            $end = new \DateTime($request->end_date);

            $total_leave_days = $start->diff($end)->days + 1;
        } elseif ($request->leave_type == 2) {

            // Half Day
            $total_leave_days = 0.5;
        } else {

            // Short Leave — worth 0.25 day, so 2 short leaves consume the
            // same quota as 1 half day (interchangeable, per policy).
            $total_leave_days = 0.25;
        }

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

        // Get user name
        $userName = check_user_name($leaveRequest->user_id);

        // Send Email Notification
        $emails = GeneralSetting::where('setting_key', 'email')
            ->value('setting_value');

        $json_emails = json_decode($emails, true);

        if (!empty($json_emails) && is_array($json_emails)) {

            $json_emails = array_filter($json_emails);

            try {

                foreach ($json_emails as $email) {

                    $email = trim($email);

                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                        Notification::route('mail', $email)
                            ->notify(
                                new LeaveRequestNotification(
                                    $leaveRequest,
                                    $userName
                                )
                            );
                    }
                }
            } catch (\Exception $e) {

                \Log::error(
                    'Leave request notification error: ' .
                        $e->getMessage()
                );
            }
        }

        return redirect()
            ->route('leave-requests')
            ->with('success', 'Leave request created successfully.');
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'action_reason' => 'required|string|max:1000',
        ]);

        $leaveRequest = LeaveRequest::findOrFail($id);

        $leaveRequest->status = 'approved';
        $leaveRequest->action_reason = $request->action_reason;
        $leaveRequest->save();

        $userName = check_user_name($leaveRequest->user_id);
        $userEmail = check_user_email($leaveRequest->user_id);

        try {

            Notification::route('mail', $userEmail)
                ->notify(
                    new LeaveApproveNotification(
                        $leaveRequest,
                        $userName
                    )
                );
        } catch (\Exception $e) {

            \Log::error(
                'Leave approve notification error: ' .
                    $e->getMessage()
            );
        }

        return redirect()
            ->back()
            ->with('success', 'Leave request approved successfully.');
    }


    public function reject(Request $request, $id)
    {
        $request->validate([
            'action_reason' => 'required|string|max:1000',
        ]);

        $leaveRequest = LeaveRequest::findOrFail($id);

        $leaveRequest->status = 'rejected';
        $leaveRequest->action_reason = $request->action_reason;
        $leaveRequest->save();

        $userName = check_user_name($leaveRequest->user_id);
        $userEmail = check_user_email($leaveRequest->user_id);

        try {

            Notification::route('mail', $userEmail)
                ->notify(
                    new LeaveRejectNotification(
                        $leaveRequest,
                        $userName
                    )
                );
        } catch (\Exception $e) {

            \Log::error(
                'Leave reject notification error: ' .
                    $e->getMessage()
            );
        }

        return redirect()
            ->back()
            ->with('success', 'Leave request rejected successfully.');
    }

    public function delete($id)
    {
        // Attempt to delete the LeaveRequest with the specified ID
        $leaveRequest = LeaveRequest::find($id);

        if ($leaveRequest) {
            $leaveRequest->delete();
            return redirect()->back()->with('success', 'Leave request deleted.');
        } else {
            return redirect()->back()->with('error', 'Leave request not found.');
        }
    }
}
