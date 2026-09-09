<?php

namespace App\Http\Controllers\Web;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    //
    public function addSelfAttendance(){
        return view('pages/self_attendence');
    }

   

    // public function office(Request $request)
    // {
    //     $action = $request->input('action');
    //     $user = Auth::user();
        
    //     EmployeeAttendance::create([
    //         'user_id' => $user->id,
    //         'action' => $action,
    //         'timestamp' => now(),
    //     ]);

    //     return response()->json(['message' => 'Office ' . ucfirst($action) . ' recorded successfully!']);
    // }

    // public function lunch(Request $request)
    // {
    //     $action = $request->input('action');
    //     $user = Auth::user();

    //     EmployeeAttendance::create([
    //         'user_id' => $user->id,
    //         'action' => $action,
    //         'timestamp' => now(),
    //     ]);

    //     return response()->json(['message' => 'Lunch ' . ucfirst($action) . ' recorded successfully!']);
    // }

    public function office(Request $request)
    {
        $action = $request->input('action');
        $user = Auth::user();
        $ipAddress = getPublicIp(); // Get the public IP address


        EmployeeAttendance::create([
            'user_id' => $user->id,
            'action' => $action,
            'timestamp' => now(),
            'ip_address' => $ipAddress, // Store the IP address
        ]);

        return response()->json(['message' =>  ucfirst($action) . ' recorded successfully!']);
    }

    public function lunch(Request $request)
    {
        $action = $request->input('action');
        $user = Auth::user();
        $ipAddress = getPublicIp(); // Get the public IP address

        EmployeeAttendance::create([
            'user_id' => $user->id,
            'action' => $action,
            'timestamp' => now(),
            'ip_address' => $ipAddress, // Store the IP address
        ]);

        return response()->json(['message' => ucfirst($action) . ' recorded successfully!']);
    }


    public function getTodayAttendanceByUser($userId)
    {
        $today = Carbon::today();

        $attendance = EmployeeAttendance::where('user_id', $userId)
                                ->whereDate('timestamp', $today)
                                ->get();

        return $attendance;
    }

    public function showAttendanceForm()
    {
        $userId = Auth::id();
        $attendance = $this->getTodayAttendanceByUser($userId);

        $officeInTime = $attendance->where('action', 'office-in')->first();
        $officeOutTime = $attendance->where('action', 'office-out')->first();
        $lunchInTime = $attendance->where('action', 'lunch-in')->first();
        $lunchOutTime = $attendance->where('action', 'lunch-out')->first();

        return view('pages/self_attendence', compact('attendance', 'officeInTime', 'officeOutTime', 'lunchInTime', 'lunchOutTime'));

    }


    // public function edit_user_attendance(Request $request) {
    //     $id = $request->input('record_id');
    //     $date = $request->input('date');
    //     $office_in = $request->input('office_in');
    //     $office_out = $request->input('office_out');
    //     $lunch_in = $request->input('lunch_in');
    //     $lunch_out = $request->input('lunch_out');
    
    //     // Convert the input date to the correct format for comparison
    //     $formatted_date = date('Y-m-d', strtotime($date));
    
    //     // Retrieve attendance records for the user and date
    //     $attendance = EmployeeAttendance::where('user_id', $id)
    //                                     ->whereDate('timestamp', 'LIKE', "%$formatted_date%")
    //                                     ->get();
    
    //     // Iterate through each attendance record and update as per action type
    //     foreach ($attendance as $record) {
    //         switch ($record->action) {
    //             case 'office-in':
    //                 //echo "office-in";
    //                 $record->update(['timestamp' => "$formatted_date $office_in:00"]);
    //                 break;
    //             case 'office-out':
    //                 // echo "office-out";
    //                 $record->update(['timestamp' => "$formatted_date $office_out:00"]);
    //                 break;
    //             case 'lunch-in':
    //                  //echo "lunch-in";
    //                 $record->update(['timestamp' => "$formatted_date $lunch_in:00"]);
    //                 break;
    //             case 'lunch-out':
    //                 // echo "lunch-out";
    //                 $record->update(['timestamp' => "$formatted_date $lunch_out:00"]);
    //                 break;
    //             default:
    //                 // Handle any other action types if necessary
    //                 break;
    //         }
    //     }
    
    //     // Optionally, you can return a response indicating success or failure
    //     // return response()->json(['message' => 'Attendance updated successfully']);
    // }
    public function edit_user_attendance(Request $request) {
        $id = $request->input('record_id');
        $date = $request->input('date');
        $office_in = $request->input('office_in');
        $office_out = $request->input('office_out');
        $lunch_in = $request->input('lunch_in');
        $lunch_out = $request->input('lunch_out');
    
        // Convert the input date to the correct format for comparison
        $formatted_date = date('Y-m-d', strtotime($date));
    
        // Retrieve attendance records for the user and date
        $attendance = EmployeeAttendance::where('user_id', $id)
                                        ->whereDate('timestamp', 'LIKE', "%$formatted_date%")
                                        ->get();

        $new_created_at = date('Y-m-d H:i:s', strtotime("$formatted_date $office_in"));

        $success = true;
        // Iterate through each attendance record and update as per action type
        foreach ($attendance as $record) {
            switch ($record->action) {
                case 'office-in':
                    $new_created_at = date('Y-m-d H:i:s', strtotime("$formatted_date $office_in"));
                    EmployeeAttendance::where('id', $record->id)
                        ->update(['timestamp' => $new_created_at]);
                    break;
                case 'office-out':
                    $new_created_at = date('Y-m-d H:i:s', strtotime("$formatted_date $office_out"));
                    EmployeeAttendance::where('id', $record->id)
                        ->update(['timestamp' => $new_created_at]);
                    break;
                case 'lunch-in':
                    $new_created_at = date('Y-m-d H:i:s', strtotime("$formatted_date $lunch_in"));
                    EmployeeAttendance::where('id', $record->id)
                        ->update(['timestamp' => $new_created_at]);
                    break;
                case 'lunch-out':
                    $new_created_at = date('Y-m-d H:i:s', strtotime("$formatted_date $lunch_out"));
                    EmployeeAttendance::where('id', $record->id)
                        ->update(['timestamp' => $new_created_at]);
                    break;
                default:
                    // Handle any other action types if necessary
                    break;
            }
        }
        
        // Optionally, return a response indicating success or failure
        if ($attendance->isEmpty()) {
            return response()->json(['message' => 'Failed to update attendance', 'status' => 'error']);
        } else {
            return response()->json(['message' => 'Attendance updated successfully', 'status' => 'success']);
           
        }
    }
    
    
    
    

    
    /*public function attendenceReport()
    {
        $userId = Auth::id();
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        // Get attendance records for the current month
        $attendanceRecords = EmployeeAttendance::where('user_id', $userId)
                                    ->whereBetween('timestamp', [$startOfMonth, $endOfMonth])
                                    ->orderBy('timestamp', 'asc')
                                    ->get()
                                    ->groupBy(function($date) {
                                        return Carbon::parse($date->timestamp)->format('Y-m-d');
                                    });

        $data = [];
        $totalWorkMinutes = 0;

        // Loop through each day of the current month
        for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $formattedDateEmployee = $date->format('d M Y');

            $records = $attendanceRecords->get($formattedDate, collect());

            $officeIn = $records->where('action', 'office-in')->first();
            $officeOut = $records->where('action', 'office-out')->first();
            $lunchIn = $records->where('action', 'lunch-in')->first();
            $lunchOut = $records->where('action', 'lunch-out')->first();

            $workHours = null;
            if ($officeIn && $officeOut) {
                $officeInTime = Carbon::parse($officeIn->timestamp);
                $officeOutTime = Carbon::parse($officeOut->timestamp);
                $lunchDuration = 0;
                
                if ($lunchIn && $lunchOut) {
                    $lunchInTime = Carbon::parse($lunchIn->timestamp);
                    $lunchOutTime = Carbon::parse($lunchOut->timestamp);
                    $lunchDuration = $lunchOutTime->diffInMinutes($lunchInTime);
                }
                
                $workMinutes = $officeOutTime->diffInMinutes($officeInTime) - $lunchDuration;
                $totalWorkMinutes += $workMinutes;
                $workHours = gmdate('H:i', $workMinutes * 60); // convert to HH:MM format
            }

            $data[] = [
                'date' => $formattedDateEmployee,
                'office_in' => $officeIn ? Carbon::parse($officeIn->timestamp)->format('h:i A') : 'N/A',
                'office_out' => $officeOut ? Carbon::parse($officeOut->timestamp)->format('h:i A') : 'N/A',
                'lunch_in' => $lunchIn ? Carbon::parse($lunchIn->timestamp)->format('h:i A') : 'N/A',
                'lunch_out' => $lunchOut ? Carbon::parse($lunchOut->timestamp)->format('h:i A') : 'N/A',
                'work_hours' => $workHours ?? 'N/A',
            ];
        }

        $totalWorkHours = gmdate('H:i', $totalWorkMinutes * 60);

        return view('pages.attendence_report', [
            'data' => $data,
            'totalWorkHours' => $totalWorkHours
        ]);
    }*/

    // public function attendenceReport()
    // {
    //     $userId = Auth::id();
    //     $today = Carbon::today();
    //     $startOfMonth = $today->copy()->startOfMonth();
    //     $endOfMonth = $today->copy()->endOfMonth();

    //     // Get attendance records for the current month
    //     $attendanceRecords = EmployeeAttendance::where('user_id', $userId)
    //                                 ->whereBetween('timestamp', [$startOfMonth, $endOfMonth])
    //                                 ->orderBy('timestamp', 'asc')
    //                                 ->get()
    //                                 ->groupBy(function($date) {
    //                                     return Carbon::parse($date->timestamp)->format('Y-m-d');
    //                                 });

    //     $data = [];
    //     $totalWorkMinutes = 0;

    //     // Loop through each day of the current month
    //     for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
    //         $formattedDate = $date->format('Y-m-d');
    //         $formattedDateEmployee = $date->format('d M Y');

    //         $records = $attendanceRecords->get($formattedDate, collect());

    //         $officeIn = $records->where('action', 'office-in')->first();
    //         $officeOut = $records->where('action', 'office-out')->first();
    //         $lunchIn = $records->where('action', 'lunch-in')->first();
    //         $lunchOut = $records->where('action', 'lunch-out')->first();

    //         $workHours = null;
    //         if ($officeIn && $officeOut) {
    //             $officeInTime = Carbon::parse($officeIn->timestamp);
    //             $officeOutTime = Carbon::parse($officeOut->timestamp);
    //             $lunchDuration = 0;

    //             if ($lunchIn && $lunchOut) {
    //                 $lunchInTime = Carbon::parse($lunchIn->timestamp);
    //                 $lunchOutTime = Carbon::parse($lunchOut->timestamp);
    //                 $lunchDuration = $lunchOutTime->diffInMinutes($lunchInTime);
    //             }

    //             $workMinutes = $officeOutTime->diffInMinutes($officeInTime) - $lunchDuration;
    //             $totalWorkMinutes += $workMinutes;
    //             $workHours = gmdate('H:i', $workMinutes * 60); // convert to HH:MM format
    //         }

    //         $data[] = [
    //             'date' => $formattedDateEmployee,
    //             'office_in' => $officeIn ? Carbon::parse($officeIn->timestamp)->format('h:i A') : 'N/A',
    //             'office_in_ip' => $officeIn ? $officeIn->ip_address : 'N/A',
    //             'office_out' => $officeOut ? Carbon::parse($officeOut->timestamp)->format('h:i A') : 'N/A',
    //             'office_out_ip' => $officeOut ? $officeOut->ip_address : 'N/A',
    //             'lunch_in' => $lunchIn ? Carbon::parse($lunchIn->timestamp)->format('h:i A') : 'N/A',
    //             'lunch_in_ip' => $lunchIn ? $lunchIn->ip_address : 'N/A',
    //             'lunch_out' => $lunchOut ? Carbon::parse($lunchOut->timestamp)->format('h:i A') : 'N/A',
    //             'lunch_out_ip' => $lunchOut ? $lunchOut->ip_address : 'N/A',
    //             'work_hours' => $workHours ?? 'N/A',
    //         ];
    //     }

    //     $totalWorkHours = gmdate('H:i', $totalWorkMinutes * 60);

    //     return view('pages.attendence_report', [
    //         'data' => $data,
    //         'totalWorkHours' => $totalWorkHours
    //     ]);
    // }

    public function attendenceReport()
    {
        
        $userId = Auth::id();
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        // Get attendance records for the current month
        $attendanceRecords = EmployeeAttendance::where('user_id', $userId)
                                    ->whereBetween('timestamp', [$startOfMonth, $endOfMonth])
                                    ->orderBy('timestamp', 'asc')
                                    ->get()
                                    ->groupBy(function($date) {
                                        return Carbon::parse($date->timestamp)->format('Y-m-d');
                                    });

        $data = [];
        $totalWorkMinutes = 0;

        // Loop through each day of the current month
        for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $formattedDateEmployee = $date->format('d M Y');

            $records = $attendanceRecords->get($formattedDate, collect());

            $officeIn = $records->where('action', 'office-in')->first();
            $officeOut = $records->where('action', 'office-out')->first();
            $lunchIn = $records->where('action', 'lunch-in')->first();
            $lunchOut = $records->where('action', 'lunch-out')->first();

            $workHours = null;
            if ($officeIn && $officeOut) {
                $officeInTime = Carbon::parse($officeIn->timestamp);
                $officeOutTime = Carbon::parse($officeOut->timestamp);
                $lunchDuration = 0;

                if ($lunchIn && $lunchOut) {
                    $lunchInTime = Carbon::parse($lunchIn->timestamp);
                    $lunchOutTime = Carbon::parse($lunchOut->timestamp);
                    $lunchDuration = $lunchOutTime->diffInMinutes($lunchInTime);
                }

                $workMinutes = $officeOutTime->diffInMinutes($officeInTime) - $lunchDuration;
                $totalWorkMinutes += $workMinutes;
                $workHours = gmdate('H:i', $workMinutes * 60); // convert to HH:MM format
            }

            // Check if the current date is a Sunday
            $isSunday = $date->isSunday();

            $data[] = [
                'date' => $formattedDateEmployee,
                'office_in' => $officeIn ? Carbon::parse($officeIn->timestamp)->format('h:i A') : 'N/A',
                'office_in_ip' => $officeIn ? $officeIn->ip_address : 'N/A',
                'office_out' => $officeOut ? Carbon::parse($officeOut->timestamp)->format('h:i A') : 'N/A',
                'office_out_ip' => $officeOut ? $officeOut->ip_address : 'N/A',
                'lunch_in' => $lunchIn ? Carbon::parse($lunchIn->timestamp)->format('h:i A') : 'N/A',
                'lunch_in_ip' => $lunchIn ? $lunchIn->ip_address : 'N/A',
                'lunch_out' => $lunchOut ? Carbon::parse($lunchOut->timestamp)->format('h:i A') : 'N/A',
                'lunch_out_ip' => $lunchOut ? $lunchOut->ip_address : 'N/A',
                'work_hours' => $workHours ?? 'N/A',
                'is_sunday' => $isSunday,
            ];
        }

        $totalWorkHours = gmdate('H:i', $totalWorkMinutes * 60);

        return view('pages.attendence_report', [
            'data' => $data,
            'totalWorkHours' => $totalWorkHours
        ]);
    }




    /* ==================== Working =================== */

    // public function todayAttendanceReport() {
    //     $today = Carbon::today();
    
    //     // Get attendance records for today and join with users table
    //     $attendanceRecords = EmployeeAttendance::whereDate('timestamp', $today)
    //                         ->join('users', 'employee_attendance.user_id', '=', 'users.id')
    //                         ->select('users.id as user_id', 'users.name', 'users.email', 'employee_attendance.*')
    //                         ->orderBy('employee_attendance.timestamp', 'asc')
    //                         ->get();
    
    //     $data = [];
    //     $totalWorkMinutes = 0;
    
    //     // Group records by user
    //     $recordsByUser = $attendanceRecords->groupBy('user_id');
    
    //     // Loop through each user's records for today
    //     foreach ($recordsByUser as $userId => $records) {
    //         $officeIn = $records->where('action', 'office-in')->first();
    //         $officeOut = $records->where('action', 'office-out')->first();
    //         $lunchIn = $records->where('action', 'lunch-in')->first();
    //         $lunchOut = $records->where('action', 'lunch-out')->first();
    
    //         $workHours = null;
    //         if ($officeIn && $officeOut) {
    //             $officeInTime = Carbon::parse($officeIn->timestamp);
    //             $officeOutTime = Carbon::parse($officeOut->timestamp);
    //             $lunchDuration = 0;
    
    //             if ($lunchIn && $lunchOut) {
    //                 $lunchInTime = Carbon::parse($lunchIn->timestamp);
    //                 $lunchOutTime = Carbon::parse($lunchOut->timestamp);
    //                 $lunchDuration = $lunchOutTime->diffInMinutes($lunchInTime);
    //             }
    
    //             $workMinutes = $officeOutTime->diffInMinutes($officeInTime) - $lunchDuration;
    //             $totalWorkMinutes += $workMinutes;
    //             $workHours = gmdate('H:i', $workMinutes * 60); // convert to HH:MM format
    //         }
    
    //         $data[] = [
    //             'user_id' => $userId,
    //             'name' => $records->first()->name,
    //             'email' => $records->first()->email,
    //             'date' => $today->format('Y-m-d'),
    //             'office_in' => $officeIn ? Carbon::parse($officeIn->timestamp)->format('h:i A') : 'N/A',
    //             'office_out' => $officeOut ? Carbon::parse($officeOut->timestamp)->format('h:i A') : 'N/A',
    //             'lunch_in' => $lunchIn ? Carbon::parse($lunchIn->timestamp)->format('h:i A') : 'N/A',
    //             'lunch_out' => $lunchOut ? Carbon::parse($lunchOut->timestamp)->format('h:i A') : 'N/A',
    //             'work_hours' => $workHours ?? 'N/A',
    //         ];
    //     }
    
    //     $totalWorkHours = gmdate('H:i', $totalWorkMinutes * 60);
    
    //     return view('pages.today_attendence_report', [
    //         'data' => $data,
    //         'totalWorkHours' => $totalWorkHours
    //     ]);
    // }

    /*public function todayAttendanceReport() {
        $today = Carbon::today();
    
        // Get all users
        $users = User::all();
    
        // Get attendance records for today and join with users table
        $attendanceRecords = EmployeeAttendance::whereDate('timestamp', $today)
                            ->join('users', 'employee_attendance.user_id', '=', 'users.id')
                            ->select('users.id as user_id', 'users.name', 'users.email', 'employee_attendance.*')
                            ->orderBy('employee_attendance.timestamp', 'asc')
                            ->get();
    
        $data = [];
        $totalWorkMinutes = 0;
    
        // Group records by user
        $recordsByUser = $attendanceRecords->groupBy('user_id');
    
        // Loop through each user
        foreach ($users as $user) {
            $records = $recordsByUser->get($user->id, collect());
            
            $officeIn = $records->where('action', 'office-in')->first();
            $officeOut = $records->where('action', 'office-out')->first();
            $lunchIn = $records->where('action', 'lunch-in')->first();
            $lunchOut = $records->where('action', 'lunch-out')->first();
    
            $workHours = null;
            if ($officeIn && $officeOut) {
                $officeInTime = Carbon::parse($officeIn->timestamp);
                $officeOutTime = Carbon::parse($officeOut->timestamp);
                $lunchDuration = 0;
    
                if ($lunchIn && $lunchOut) {
                    $lunchInTime = Carbon::parse($lunchIn->timestamp);
                    $lunchOutTime = Carbon::parse($lunchOut->timestamp);
                    $lunchDuration = $lunchOutTime->diffInMinutes($lunchInTime);
                }
    
                $workMinutes = $officeOutTime->diffInMinutes($officeInTime) - $lunchDuration;
                $totalWorkMinutes += $workMinutes;
                $workHours = gmdate('H:i', $workMinutes * 60); // convert to HH:MM format
            }
    
            $data[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'date' => $today->format('Y-m-d'),
                'office_in' => $officeIn ? Carbon::parse($officeIn->timestamp)->format('h:i A') : 'N/A',
                'office_out' => $officeOut ? Carbon::parse($officeOut->timestamp)->format('h:i A') : 'N/A',
                'lunch_in' => $lunchIn ? Carbon::parse($lunchIn->timestamp)->format('h:i A') : 'N/A',
                'lunch_out' => $lunchOut ? Carbon::parse($lunchOut->timestamp)->format('h:i A') : 'N/A',
                'work_hours' => $workHours ?? 'N/A',
            ];
        }
    
        $totalWorkHours = gmdate('H:i', $totalWorkMinutes * 60);
    
        return view('pages.today_attendence_report', [
            'data' => $data,
            'totalWorkHours' => $totalWorkHours
        ]);
    }*/

    public function todayAttendanceReport() {
        $today = Carbon::today();
    
        // Get all users
        $users = User::all();
    
        // Get attendance records for today and join with users table
        $attendanceRecords = EmployeeAttendance::whereDate('timestamp', $today)
                            ->join('users', 'employee_attendance.user_id', '=', 'users.id')
                            ->select('users.id as user_id', 'users.name', 'users.email', 'employee_attendance.*')
                            ->orderBy('employee_attendance.timestamp', 'asc')
                            ->get();
    
        $data = [];
        $totalWorkMinutes = 0;
    
        // Group records by user
        $recordsByUser = $attendanceRecords->groupBy('user_id');
    
        // Loop through each user
        foreach ($users as $user) {
            $records = $recordsByUser->get($user->id, collect());
            
            $officeIn = $records->where('action', 'office-in')->first();
            $officeOut = $records->where('action', 'office-out')->first();
            $lunchIn = $records->where('action', 'lunch-in')->first();
            $lunchOut = $records->where('action', 'lunch-out')->first();
    
            $workHours = null;
            if ($officeIn && $officeOut) {
                $officeInTime = Carbon::parse($officeIn->timestamp);
                $officeOutTime = Carbon::parse($officeOut->timestamp);
                $lunchDuration = 0;
    
                if ($lunchIn && $lunchOut) {
                    $lunchInTime = Carbon::parse($lunchIn->timestamp);
                    $lunchOutTime = Carbon::parse($lunchOut->timestamp);
                    $lunchDuration = $lunchOutTime->diffInMinutes($lunchInTime);
                }
    
                $workMinutes = $officeOutTime->diffInMinutes($officeInTime) - $lunchDuration;
                $totalWorkMinutes += $workMinutes;
                $workHours = gmdate('H:i', $workMinutes * 60); // convert to HH:MM format
            }
    
            $data[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'date' => $today->format('Y-m-d'),
                'office_in' => $officeIn ? Carbon::parse($officeIn->timestamp)->format('h:i A') : 'N/A',
                'office_in_ip' => $officeIn ? $officeIn->ip_address : 'N/A',
                'office_out' => $officeOut ? Carbon::parse($officeOut->timestamp)->format('h:i A') : 'N/A',
                'office_out_ip' => $officeOut ? $officeOut->ip_address : 'N/A',
                'lunch_in' => $lunchIn ? Carbon::parse($lunchIn->timestamp)->format('h:i A') : 'N/A',
                'lunch_in_ip' => $lunchIn ? $lunchIn->ip_address : 'N/A',
                'lunch_out' => $lunchOut ? Carbon::parse($lunchOut->timestamp)->format('h:i A') : 'N/A',
                'lunch_out_ip' => $lunchOut ? $lunchOut->ip_address : 'N/A',
                'work_hours' => $workHours ?? 'N/A',
            ];
        }
    
        $totalWorkHours = gmdate('H:i', $totalWorkMinutes * 60);
    
        return view('pages.today_attendence_report', [
            'data' => $data,
            'totalWorkHours' => $totalWorkHours
        ]);
    }

    

 

    // public function dailyAttendanceReport(Request $request) {
    //     // Get the current year and month
    //     $currentYear = date('Y');
    //     $currentMonth = date('m');
    
    //     // Get the filter inputs
    //     $userId = $request->input('user_id');
    //     $month = $request->input('month', $currentMonth); // Default to the current month
    //     $year = $request->input('year', $currentYear); // Default to the current year
    
    //     // Fetch filtered attendance records from the database
    //     $query = EmployeeAttendance::join('users', 'employee_attendance.user_id', '=', 'users.id')
    //                 ->select('users.id as user_id', 'users.name', 'users.email', 'employee_attendance.*')
    //                 ->whereYear('employee_attendance.timestamp', $year)
    //                 ->whereMonth('employee_attendance.timestamp', $month);
    
    //     if (!empty($userId)) {
    //         $query->where('employee_attendance.user_id', $userId);
    //     }
    
    //     $records = $query->orderBy('employee_attendance.timestamp', 'asc')->get();
    
    //     // Group the records by user and date
    //     $groupedRecords = $records->groupBy(function($record) {
    //         return $record->user_id . '-' . Carbon::parse($record->timestamp)->format('Y-m-d');
    //     });
    
    //     // Process and format data
    //     $data = [];
    //     foreach ($groupedRecords as $key => $records) {
    //         $firstRecord = $records->first();
    //         $user = User::find($firstRecord->user_id);
    //         $date = Carbon::parse($firstRecord->timestamp)->format('Y-m-d');
    
    //         $officeIn = $records->firstWhere('action', 'office-in');
    //         $officeOut = $records->firstWhere('action', 'office-out');
    //         $lunchIn = $records->firstWhere('action', 'lunch-in');
    //         $lunchOut = $records->firstWhere('action', 'lunch-out');
    
    //         $workHours = $this->calculateWorkHours($officeIn, $officeOut, $lunchIn, $lunchOut);
    
    //         $data[] = [
    //             'user_id' => $user->id,
    //             'name' => $user->name,
    //             'email' => $user->email,
    //             'date' => $date,
    //             'office_in' => $officeIn ? Carbon::parse($officeIn->timestamp)->format('h:i A') : 'N/A',
    //             'office_in_ip' => $officeIn ? $officeIn->ip_address : 'N/A',
    //             'office_out' => $officeOut ? Carbon::parse($officeOut->timestamp)->format('h:i A') : 'N/A',
    //             'office_out_ip' => $officeOut ? $officeOut->ip_address : 'N/A',
    //             'lunch_in' => $lunchIn ? Carbon::parse($lunchIn->timestamp)->format('h:i A') : 'N/A',
    //             'lunch_in_ip' => $lunchIn ? $lunchIn->ip_address : 'N/A',
    //             'lunch_out' => $lunchOut ? Carbon::parse($lunchOut->timestamp)->format('h:i A') : 'N/A',
    //             'lunch_out_ip' => $lunchOut ? $lunchOut->ip_address : 'N/A',
    //             'work_hours' => $workHours,
    //         ];
    //     }
    
    //     return view('pages.daily_attendence_report', compact('data'));
    // }

    /*public function dailyAttendanceReport(Request $request) {
        // Get the current year and month
        $currentYear = date('Y');
        $currentMonth = date('m');
    
        // Get the filter inputs
        $userId = $request->input('user_id');
        $month = $request->input('month', $currentMonth); // Default to the current month
        $year = $request->input('year', $currentYear); // Default to the current year
    
        // Fetch all users or filter by user_id if provided
        $users = User::when($userId, function($query) use ($userId) {
            return $query->where('id', $userId);
        })->get();
    
        // Set the start and end of the month
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();
    
        $data = [];
    
        foreach ($users as $user) {
            // Get attendance records for the user in the selected month
            $attendanceRecords = EmployeeAttendance::where('user_id', $user->id)
                                    ->whereYear('timestamp', $year)
                                    ->whereMonth('timestamp', $month)
                                    ->orderBy('timestamp', 'asc')
                                    ->get()
                                    ->groupBy(function($date) {
                                        return Carbon::parse($date->timestamp)->format('Y-m-d');
                                    });
    
            $totalWorkMinutes = 0;
    
            // Loop through each day of the selected month
            for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
                $formattedDate = $date->format('Y-m-d');
                $formattedDateEmployee = $date->format('d M Y');
    
                $records = $attendanceRecords->get($formattedDate, collect());
    
                $officeIn = $records->where('action', 'office-in')->first();
                $officeOut = $records->where('action', 'office-out')->first();
                $lunchIn = $records->where('action', 'lunch-in')->first();
                $lunchOut = $records->where('action', 'lunch-out')->first();
    
                $workHours = null;
                if ($officeIn && $officeOut) {
                    $officeInTime = Carbon::parse($officeIn->timestamp);
                    $officeOutTime = Carbon::parse($officeOut->timestamp);
                    $lunchDuration = 0;
    
                    if ($lunchIn && $lunchOut) {
                        $lunchInTime = Carbon::parse($lunchIn->timestamp);
                        $lunchOutTime = Carbon::parse($lunchOut->timestamp);
                        $lunchDuration = $lunchOutTime->diffInMinutes($lunchInTime);
                    }
    
                    $workMinutes = $officeOutTime->diffInMinutes($officeInTime) - $lunchDuration;
                    $totalWorkMinutes += $workMinutes;
                    $workHours = gmdate('H:i', $workMinutes * 60); // convert to HH:MM format
                }
    
                $data[] = [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'date' => $formattedDateEmployee,
                    'office_in' => $officeIn ? Carbon::parse($officeIn->timestamp)->format('h:i A') : 'N/A',
                    'office_in_ip' => $officeIn ? $officeIn->ip_address : 'N/A',
                    'office_out' => $officeOut ? Carbon::parse($officeOut->timestamp)->format('h:i A') : 'N/A',
                    'office_out_ip' => $officeOut ? $officeOut->ip_address : 'N/A',
                    'lunch_in' => $lunchIn ? Carbon::parse($lunchIn->timestamp)->format('h:i A') : 'N/A',
                    'lunch_in_ip' => $lunchIn ? $lunchIn->ip_address : 'N/A',
                    'lunch_out' => $lunchOut ? Carbon::parse($lunchOut->timestamp)->format('h:i A') : 'N/A',
                    'lunch_out_ip' => $lunchOut ? $lunchOut->ip_address : 'N/A',
                    'work_hours' => $workHours ?? 'N/A',
                    'is_sunday' => $date->isSunday(),
                ];
            }
        }
    
        //return view('pages.daily_attendence_report', compact('data'), );

        $totalWorkHours = gmdate('H:i', $totalWorkMinutes * 60);

        return view('pages.daily_attendence_report', [
            'data' => $data,
            'totalWorkHours' => $totalWorkHours
        ]);
    }*/
    public function dailyAttendanceReport(Request $request) {
        // Get the current year and month
        $currentYear = date('Y');
        $currentMonth = date('m');
    
        // Get the filter inputs
        $userId = $request->input('user_id');
        $selectedMonthYear = $request->input('month', $currentMonth . '-' . $currentYear); // Default to the current month and year
    
        // Split month and year
        list($monthNumber, $yearNumber) = explode('-', $selectedMonthYear);
        $month = (int) $monthNumber;
        $year = (int) $yearNumber;
    

        // Fetch all users or filter by user_id if provided
        $users = User::when($userId, function($query) use ($userId) {
            return $query->where('id', $userId);
        })->get();
    
        // Set the start and end of the month
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();
    
        $data = [];
    
        foreach ($users as $user) {
            // Get attendance records for the user in the selected month
            $attendanceRecords = EmployeeAttendance::where('user_id', $user->id)
                                    ->whereYear('timestamp', $year)
                                    ->whereMonth('timestamp', $month)
                                    ->orderBy('timestamp', 'asc')
                                    ->get()
                                    ->groupBy(function($date) {
                                        return Carbon::parse($date->timestamp)->format('Y-m-d');
                                    });
    
            $totalWorkMinutes = 0;
    
            // Loop through each day of the selected month
            for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
                $formattedDate = $date->format('Y-m-d');
                $formattedDateEmployee = $date->format('d M Y');
    
                $records = $attendanceRecords->get($formattedDate, collect());
    
                $officeIn = $records->where('action', 'office-in')->first();
                $officeOut = $records->where('action', 'office-out')->first();
                $lunchIn = $records->where('action', 'lunch-in')->first();
                $lunchOut = $records->where('action', 'lunch-out')->first();
    
                $workHours = null;
                if ($officeIn && $officeOut) {
                    $officeInTime = Carbon::parse($officeIn->timestamp);
                    $officeOutTime = Carbon::parse($officeOut->timestamp);
                    $lunchDuration = 0;
    
                    if ($lunchIn && $lunchOut) {
                        $lunchInTime = Carbon::parse($lunchIn->timestamp);
                        $lunchOutTime = Carbon::parse($lunchOut->timestamp);
                        $lunchDuration = $lunchOutTime->diffInMinutes($lunchInTime);
                    }
    
                    $workMinutes = $officeOutTime->diffInMinutes($officeInTime) - $lunchDuration;
                    $totalWorkMinutes += $workMinutes;
                    $workHours = gmdate('H:i', $workMinutes * 60); // convert to HH:MM format
                }
    
                $data[] = [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'date' => $formattedDateEmployee,
                    'office_in' => $officeIn ? Carbon::parse($officeIn->timestamp)->format('h:i A') : 'N/A',
                    'office_in_ip' => $officeIn ? $officeIn->ip_address : 'N/A',
                    'office_out' => $officeOut ? Carbon::parse($officeOut->timestamp)->format('h:i A') : 'N/A',
                    'office_out_ip' => $officeOut ? $officeOut->ip_address : 'N/A',
                    'lunch_in' => $lunchIn ? Carbon::parse($lunchIn->timestamp)->format('h:i A') : 'N/A',
                    'lunch_in_ip' => $lunchIn ? $lunchIn->ip_address : 'N/A',
                    'lunch_out' => $lunchOut ? Carbon::parse($lunchOut->timestamp)->format('h:i A') : 'N/A',
                    'lunch_out_ip' => $lunchOut ? $lunchOut->ip_address : 'N/A',
                    'work_hours' => $workHours ?? 'N/A',
                    'is_sunday' => $date->isSunday(),
                ];
            }
        }
    
        //return view('pages.daily_attendence_report', compact('data'), );

        $totalWorkHours = gmdate('H:i', $totalWorkMinutes * 60);

        return view('pages.daily_attendence_report', [
            'data' => $data,
            'totalWorkHours' => $totalWorkHours
        ]);
    }
    
    

    private function calculateWorkHours($officeIn, $officeOut, $lunchIn, $lunchOut) {
        if ($officeIn && $officeOut) {
            $officeInTime = Carbon::parse($officeIn->timestamp);
            $officeOutTime = Carbon::parse($officeOut->timestamp);
    
            if ($lunchIn && $lunchOut) {
                $lunchInTime = Carbon::parse($lunchIn->timestamp);
                $lunchOutTime = Carbon::parse($lunchOut->timestamp);
    
                $workHours = $officeOutTime->diffInMinutes($officeInTime) - $lunchOutTime->diffInMinutes($lunchInTime);
            } else {
                $workHours = $officeOutTime->diffInMinutes($officeInTime);
            }
    
            return gmdate('H:i', $workHours * 60);
        }
    
        return 'N/A';
    }

}
