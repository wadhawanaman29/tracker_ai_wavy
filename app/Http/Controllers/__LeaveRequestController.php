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

class LeaveRequestController extends Controller
{
    public function index()
    {
        
        // $leaveRequests = LeaveRequest::orderBy('id','desc') 
        //                         ->paginate(20);
                                    // ->get();

        $currentDateTime = Carbon::now();
        $leaveRequests = LeaveRequest::whereDate('end_date', '>=', $currentDateTime)
                             ->orderBy('id', 'desc')
                             ->paginate(20);
                            //  echo "<pre>";
                            //  print_r($leaveRequests);
                            //  echo "</pre>";
                            //  die();

        $userleaveRequests = LeaveRequest::where('user_id', Auth::id())
            ->with('user') 
            ->orderBy('id','desc')
            // ->paginate(5)
            ->get();
            
        return view('leave-requests.index', compact('leaveRequests', 'userleaveRequests'));

    }

    public function create()
    {
        return view('leave-requests.create');
    }

    

    public function store(Request $request)
    {
  
        // Validate the incoming request data
        $request->validate([
            'reason' => 'required|string',
            'start_date' => 'required|date',
            
        ]);

        
        // Calculate total leave days
        if (!empty($request->end_date)) {
            $start = new \DateTime($request->start_date);
            $end = new \DateTime($request->end_date);
            $total_leave_days = $start->diff($end)->days + 1; // Include both start and end dates
        } else {
            // If end_date is not provided, default total_leave_days to 1 (only start_date)
            $total_leave_days = 0.5;
        }

        // Create a new leave request
        $leaveRequest = LeaveRequest::create([
            'user_id' => Auth::user()->id,
            'reason' => $request->reason,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date ?: $request->start_date, // Use start_date if end_date is blank
            'leave_time' => $request->leave_time,
            'total_leave_days' => $total_leave_days,
            'status' => 'pending', // Default status
        ]);

        // Send email notification to the user
        $userName = check_user_name(Auth::user()->id); // Get username
        $emails = GeneralSetting::where('setting_key', 'email')->pluck('setting_value')->toArray();

        $json_emails = json_decode($emails[0]);

        foreach ($json_emails as $email) {
           Notification::route('mail', $email)
                    ->notify(new LeaveRequestNotification($leaveRequest, $userName));
        }
       
        return redirect()->route('leave-requests')->with('success', 'Leave request created successfully.');
    }

    public function approve($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->status = 'approved';
        $leaveRequest->save();
        return redirect()->back()->with('success', 'Leave request approved.');
    }

    public function reject($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->status = 'rejected';
        $leaveRequest->save();

        return redirect()->back()->with('success', 'Leave request rejected.');
    }

    public function delete($id)
    {
        // Attempt to delete the LeaveRequest with the specified ID
        $leaveRequest = LeaveRequest::find($id);
        if ($leaveRequest) {
            $leaveRequest->delete();
            return redirect()->back()->with('success', 'Leave request deleted.');
        } else {
            return redirect()->back()->with('success', 'Leave request not delete.');
        }
    }

}


