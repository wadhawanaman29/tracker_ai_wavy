<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeAttendance;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    //
    public function addSelfAttendance()
    {
        return view('pages/self_attendence');
    }

    public function office(Request $request)
    {
        $action = $request->input('action');
        $user = Auth::user();
        $ipAddress = get_client_ip(); // Get the user's IP address from the request

        // Validate the request action
        $request->validate([
            'action' => 'required|in:office-in,office-out',
        ]);

        // Perform action based on the request
        if ($action === 'office-in') {
            // An employee may take at most 1 mid-day short leave per day —
            // i.e. at most 2 office-in sessions today (the normal arrival,
            // plus one out-and-back). Reject any further attempt.
            $todaySessionCount = Attendance::where('user_id', $user->id)
                ->whereDate('created_at', now()->toDateString())
                ->count();

            if ($todaySessionCount >= 2) {
                return response()->json([
                    'error' => 'You have already used your one mid-day short leave for today.',
                ], 422);
            }

            Attendance::create([
                'user_id' => $user->id,
                'office_in_time' => Carbon::now(),
                'ip' => $ipAddress,
            ]);
        } elseif ($action === 'office-out') {
            // Find the latest office-in record for the user
            $attendance = Attendance::where('user_id', $user->id)
                ->whereNull('office_out_time')
                ->latest()
                ->first();

            if ($attendance) {
                $attendance->update([
                    'office_out_time' => Carbon::now(),
                    'ip' => $ipAddress,
                ]);
            } else {
                // Handle error when no office-in record found for office-out action
                return response()->json(['error' => 'No matching office-in record found.'], 404);
            }
        }

        return response()->json(['message' => ucfirst($action) . ' recorded successfully!']);
    }

    public function lunch(Request $request)
    {
        $action = $request->input('action');
        $user = Auth::user();
        $ipAddress = get_client_ip(); // Get the user's IP address from the request

        // Perform action based on the request
        if ($action === 'lunch-in') {
            // Find the latest lunch-in record for the user
            $attendance = Attendance::where('user_id', $user->id)
                ->whereNull('lunch_out_time')
                ->latest()
                ->first();

            if ($attendance) {
                // Update the existing lunch-in record with current created_at and IP
                $attendance->update([
                    'lunch_in_time' => Carbon::now(),
                    'ip' => $ipAddress,
                ]);

                return response()->json(['message' => 'Lunch-in updated successfully!', 'attendance' => $attendance]);
            } else {
                return response()->json(['error' => 'No ongoing lunch break found.'], 404);
            }
        } elseif ($action === 'lunch-out') {
            // Find the latest lunch-in record for the user
            $attendance = Attendance::where('user_id', $user->id)
                ->whereNull('lunch_out_time')
                ->latest()
                ->first();

            if ($attendance) {
                // Update the existing lunch-in record with current created_at and IP
                $attendance->update([
                    'lunch_out_time' => Carbon::now(),
                    'ip' => $ipAddress,
                ]);

                return response()->json(['message' => 'Lunch-out updated successfully!', 'attendance' => $attendance]);
            } else {
                return response()->json(['error' => 'No ongoing lunch break found.'], 404);
            }
        }
    }

    public function breaktime(Request $request)
    {
        $action = $request->input('action');
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', now()->toDateString())
            ->whereNull('Break_out_time')
            ->latest()
            ->first();

        // // Perform action based on the request
        if ($action === 'break') {
            $attendance->update([
                'break_in_time' => Carbon::now(),
            ]);

            return response()->json(['message' => ucfirst($action) . ' recorded successfully!', 'attendance' => $attendance]);
        } elseif ($action === 'stop') {
            $attendance->update([
                'break_out_time' => Carbon::now(),
            ]);

            return response()->json(['message' => ucfirst($action) . ' recorded successfully!', 'attendance' => $attendance]);
        } else {
            return response()->json(['error' => 'Invalid action.']);
        }
    }



    public function getTodayAttendanceByUser($userId)
    {

        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->get();

        return $attendance;
    }

    public function showAttendanceForm()
    {
        $userId = Auth::id();
        $attendance = $this->getTodayAttendanceByUser($userId);

        $officeInTime = $attendance->where('office_in_time', '!=', '')->first();
        $officeOutTime = $attendance->where('office_out_time', '!=', '')->first();
        $lunchInTime = $attendance->where('lunch_in_time', '!=', '')->first();
        $lunchOutTime = $attendance->where('lunch_out_time', '!=', '')->first();
        $breakInTime = $attendance->where('break_in_time', '!=', '')->first();
        $breakOutTime = $attendance->where('break_out_time', '!=', '')->first();

        return view('pages/self_attendence', compact('attendance', 'officeInTime', 'officeOutTime', 'lunchInTime', 'lunchOutTime', 'breakInTime', 'breakOutTime'));
    }


public function edit_user_attendance(Request $request)
{
    $id = $request->input('record_id');
    $date = $request->input('date');

    // Format date properly
    $formatted_date = Carbon::parse($date)->format('Y-m-d');

    // Prepare datetime values (date + time)
    $office_in_time = $request->office_in && $request->office_in !== 'N/A'
        ? Carbon::parse($formatted_date . ' ' . $request->office_in)
        : null;

    $office_out_time = $request->office_out && $request->office_out !== 'N/A'
        ? Carbon::parse($formatted_date . ' ' . $request->office_out)
        : null;

    $lunch_in_time = $request->lunch_in && $request->lunch_in !== 'N/A'
        ? Carbon::parse($formatted_date . ' ' . $request->lunch_in)
        : null;

    $lunch_out_time = $request->lunch_out && $request->lunch_out !== 'N/A'
        ? Carbon::parse($formatted_date . ' ' . $request->lunch_out)
        : null;

    // Check if attendance record exists for this user and date
    $attendanceRecord = Attendance::where('user_id', $id)
        ->whereBetween('created_at', [
            $formatted_date . ' 00:00:00',
            $formatted_date . ' 23:59:59'
        ])
        ->first();

    if ($attendanceRecord) {
        // Update existing record
        $attendanceRecord->update(array_filter([
            'office_in_time' => $office_in_time,
            'office_out_time' => $office_out_time,
            'lunch_in_time' => $lunch_in_time,
            'lunch_out_time' => $lunch_out_time,
        ]));
    } else {
        // Create new attendance record for absent day
        $attendanceRecord = Attendance::create([
            'user_id' => $id,
            'office_in_time' => $office_in_time,
            'office_out_time' => $office_out_time,
            'lunch_in_time' => $lunch_in_time,
            'lunch_out_time' => $lunch_out_time,
            'created_at' => $formatted_date,
            'updated_at' => now(),
        ]);
    }

    // Calculate work hours if both office in and out are present
    if ($office_in_time && $office_out_time) {
        $workHours = $office_in_time->diffInHours($office_out_time);
        // You can save this to your attendance record if you have a work_hours field
        // $attendanceRecord->work_hours = $workHours;
        // $attendanceRecord->save();
    }

    // Return JSON response
    return response()->json([
        'message' => $attendanceRecord->wasRecentlyCreated ? 'Attendance record created successfully' : 'Attendance updated successfully',
        'status' => 'success'
    ]);
}


    public function attendenceReport()
    {
        $userId = Auth::id();
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();
        $holidays = DB::table('holidays')
            ->pluck('name', 'date')
            ->mapWithKeys(function ($title, $date) {
                return [
                    Carbon::parse($date)->format('Y-m-d') => $title
                ];
            })
            ->toArray();

        $companyLeaveDates = DB::table('company_leaves')
            ->whereDate('leave_date', '>=', $startOfMonth->format('Y-m-d'))
            ->whereDate('leave_date', '<=', $endOfMonth->format('Y-m-d'))
            ->pluck('leave_date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->unique()
            ->values()
            ->toArray();

        $attendanceRecords = Attendance::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(function ($row) {
                return Carbon::parse($row->created_at)->format('Y-m-d');
            });

        $settings = AttendanceSetting::first();
        $lateAllow = $settings->late_allow ?? 3;
        $lateGraceUsed = 0;

        $data = [];
        $totalWorkMinutes = 0;
        for (
            $date = $startOfMonth->copy();
            $date->lte($endOfMonth);
            $date->addDay()
        ) {
            $formattedDate = $date->format('Y-m-d');
            $formattedDateEmployee = $date->format('d M Y');
            $records = $attendanceRecords->get($formattedDate, collect());

            $officeIn = $records->first(fn($r) => $r->office_in_time !== null);
            $officeOut = $records->sortByDesc('office_out_time')
                ->first(fn($r) => $r->office_out_time !== null);
            $lunchIn = $records->first(fn($r) => $r->lunch_in_time !== null);
            $lunchOut = $records->first(fn($r) => $r->lunch_out_time !== null);
            $breakIn = $records->first(fn($r) => $r->break_in_time !== null);
            $breakOut = $records->first(fn($r) => $r->break_out_time !== null);

            $breakDuration = ($breakIn && $breakOut)
                ? Carbon::parse($breakOut->break_out_time)
                    ->diffInMinutes(Carbon::parse($breakIn->break_in_time))
                : null;

            $isSunday = $date->isSunday();
            $isSaturday = $date->isSaturday();
            $isHoliday = array_key_exists($formattedDate, $holidays);
            $isCompanyLeave = in_array($formattedDate, $companyLeaveDates);
            $holidayName = $holidays[$formattedDate] ?? null;

            $workHours = null;

            if ($date->isWeekend()) {
                $status = 'weekend';
            } elseif ($isHoliday) {
                $status = 'holiday';
            } elseif ($isCompanyLeave) {
                $status = 'company_leave';
            } else {
                $daySummary = $this->summarizeDaySessions($records);

                if (!$daySummary) {
                    $status = 'absent';
                } else {
                    $totalWorkMinutes += $daySummary['worked_minutes'];
                    $workHours = gmdate('H:i', $daySummary['worked_minutes'] * 60);
                    $status = $this->classifyDayStatus(
                        $daySummary['worked_minutes'],
                        480,
                        $daySummary['office_in'],
                        $lateGraceUsed,
                        $lateAllow
                    );
                }
            }

            $latestTrackerLog = DB::table('tracker_users_logs')
                ->where('user_id', $userId)
                ->whereDate('created_at', $formattedDate)
                ->orderBy('id', 'desc')
                ->first();

            $activeMinutes = $latestTrackerLog->user_active ?? 0;

            $inactiveMinutes = $latestTrackerLog->user_inactive ?? 0;

            $activeTime = $activeMinutes > 0
                ? gmdate('H:i:s', $activeMinutes)
                : 'N/A';

            $inactiveTime = $inactiveMinutes > 0
                ? gmdate('H:i:s', $inactiveMinutes)
                : 'N/A';

            $data[] = [
                'date' => $formattedDateEmployee,
                'office_in' => $officeIn
                    ? Carbon::parse(
                        $officeIn->office_in_time
                    )->format('h:i A')
                    : 'N/A',
                'office_out' => $officeOut
                    ? Carbon::parse(
                        $officeOut->office_out_time
                    )->format('h:i A')
                    : 'N/A',

                'lunch_in' => $lunchIn
                    ? Carbon::parse(
                        $lunchIn->lunch_in_time
                    )->format('h:i A')
                    : 'N/A',

                'lunch_out' => $lunchOut
                    ? Carbon::parse(
                        $lunchOut->lunch_out_time
                    )->format('h:i A')
                    : 'N/A',

                'work_hours' => $workHours ?? 'N/A',
                'status' => $status,
                'break' => $breakDuration ?? 'N/A',
                'active_time' => $activeTime,
                'inactive_time' => $inactiveTime,
                'is_sunday' => $isSunday,
                'is_saturday' => $isSaturday,
                'is_holiday' => $isHoliday,
                'is_company_leave' => $isCompanyLeave,
                'holiday_name' => $holidayName,
            ];
        }
        $hours = floor($totalWorkMinutes / 60);
        $minutes = $totalWorkMinutes % 60;
        $totalWorkHours = sprintf(
            '%d:%02d',
            $hours,
            $minutes
        );
        return view(
            'pages.attendence_report',
            [
                'data' => $data,
                'totalWorkHours' => $totalWorkHours
            ]
        );
    }







    public function todayAttendanceReport()
    {
        $today = Carbon::today()->toDateString(); // Y-m-d

        // Get only employees
        $users = User::where('user_type', '=', '1')->where('user_status', '!=', '0')->get();

        // Attendance records for today
        $attendanceRecords = Attendance::whereDate('attendance.office_in_time', $today)
            ->join('users', 'attendance.user_id', '=', 'users.id')
            ->select('users.id as user_id', 'users.name', 'users.email', 'attendance.*')
            ->get()
            ->groupBy('user_id');

        $data = [];
        $totalWorkMinutes = 0;

        foreach ($users as $user) {

            $records = $attendanceRecords->get($user->id, collect());

            // Attendance events — earliest office-in / latest office-out
            // across ALL of today's sessions (a mid-day short leave
            // creates a second office-in/office-out pair for the day).
            $officeIn  = $records->whereNotNull('office_in_time')->sortBy('office_in_time')->first();
            $officeOut = $records->whereNotNull('office_out_time')->sortByDesc('office_out_time')->first();
            $lunchIn   = $records->whereNotNull('lunch_in_time')->first();
            $lunchOut  = $records->whereNotNull('lunch_out_time')->first();
            $breakIn   = $records->whereNotNull('break_in_time')->first();
            $breakOut  = $records->whereNotNull('break_out_time')->first();

            $workHours     = 'N/A';
            $breakDuration = 'N/A';

            $daySummary = $this->summarizeDaySessions($records);

            if ($daySummary) {
                $workMinutes = $daySummary['worked_minutes'];
                $totalWorkMinutes += $workMinutes;
                $workHours = gmdate('H:i', $workMinutes * 60);

                $breakDuration = ($breakIn && $breakOut)
                    ? Carbon::parse($breakOut->break_out_time)
                        ->diffInMinutes(Carbon::parse($breakIn->break_in_time))
                    : 'N/A';
            }

            $lastTrackerLog = DB::table('tracker_users_logs')
                ->where('user_id', $user->id)
                ->whereDate('tracker_start_time', $today)
                ->orderByDesc('id')
                ->first();
            // echo"<pre>";
            // print_r($lastTrackerLog);
            // die("poopop");
            // dd($lastTrackerLog);

            $activeSeconds   = $lastTrackerLog->user_active   ?? 0;
            $inactiveSeconds = $lastTrackerLog->user_inactive ?? 0;

            $mouse_clicks = $lastTrackerLog->mouse_clicks ?? 0;
            $key_presses = $lastTrackerLog->key_presses ?? 0;
            // dd($mouse_clicks);


            $activeTime   = gmdate('H:i:s', $activeSeconds);
            $inactiveTime = gmdate('H:i:s', $inactiveSeconds);
            $ipAddress = 'N/A';
            if ($officeIn && !empty($officeIn->ip_address)) {
                $ipAddress = $officeIn->ip_address;
            } elseif ($officeOut && !empty($officeOut->ip_address)) {
                $ipAddress = $officeOut->ip_address;
            }

            $data[] = [
                'user_id' => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'date'    => Carbon::parse($today)->format('d M Y'),

                'office_in'     => $officeIn ? Carbon::parse($officeIn->office_in_time)->format('h:i A') : 'N/A',
                'office_in_ip'  => $officeIn ? $officeIn->ip_address : 'N/A',

                'office_out'    => $officeOut ? Carbon::parse($officeOut->office_out_time)->format('h:i A') : 'N/A',
                'office_out_ip' => $officeOut ? $officeOut->ip_address : 'N/A',

                'lunch_in'  => $lunchIn ? Carbon::parse($lunchIn->lunch_in_time)->format('h:i A') : 'N/A',
                'lunch_out' => $lunchOut ? Carbon::parse($lunchOut->lunch_out_time)->format('h:i A') : 'N/A',

                'break_in_time'  => $breakIn ? Carbon::parse($breakIn->break_in_time)->format('h:i A') : 'N/A',
                'break_out_time' => $breakOut ? Carbon::parse($breakOut->break_out_time)->format('h:i A') : 'N/A',

                'work_hours' => $workHours,
                'break'      => $breakDuration,

                'user_active_time'   => $activeTime,
                'user_inactive_time' => $inactiveTime,
                'mouse_clicks'  => $mouse_clicks,
                'key_presses' =>  $key_presses,

                'ip' => $ipAddress,
            ];
        }
        // echo"<pre>";
        // print_r($data);
        // die("ooo");

        $hours   = floor($totalWorkMinutes / 60);
        $minutes = $totalWorkMinutes % 60;
        $totalWorkHours = sprintf('%d:%02d', $hours, $minutes);

        return view('pages.today_attendence_report', [
            'data' => $data,
            'totalWorkHours' => $totalWorkHours
        ]);
    }

    public function todayTrackerHistory($userId)
    {
        $today = Carbon::today()->toDateString();
        $today = Carbon::today()->toDateString();  // e.g. 2026-02-16
        $formattedDate = Carbon::today()->format('F d, Y');
        $logs = DB::table('tracker_users_logs')
            ->where('user_id', $userId)
            ->whereDate('tracker_start_time', $today)
            ->orderBy('tracker_start_time', 'asc')
            ->get();

        $data = [];

        foreach ($logs as $log) {

            $startTime = Carbon::parse($log->tracker_start_time);

            if ($log->tracker_stop_time) {
                $stopTime = Carbon::parse($log->tracker_stop_time);
            } else {
                $stopTime = Carbon::now();
            }
            $totalSeconds = $stopTime->diffInSeconds($startTime);
            $data[] = [
                'start' => $startTime->format('h:i:s A'),
                'stop'  => $log->tracker_stop_time
                    ? $stopTime->format('h:i:s A')
                    : 'Running',
                'active' => gmdate('H:i:s', $totalSeconds),
                'inactive' => gmdate('H:i:s', $log->user_inactive),
                'reason_stop' => $log->reason_stop,
                'mouse_clicks' => $log->mouse_clicks,
                'key_presses' => $log->key_presses
            ];
        }
        $latestLog = DB::table('tracker_users_logs')
            ->where('user_id', $userId)
            ->whereDate('tracker_start_time', $today)
            ->orderBy('tracker_start_time', 'desc')
            ->first();

        $latestActive = '00:00:00';
        $latestInactive = '00:00:00';

        if ($latestLog) {
            $latestActive = gmdate('H:i:s', $latestLog->user_active);
            $latestInactive = gmdate('H:i:s', $latestLog->user_inactive);
        }
        return response()->json([
            'rows' => $data,
            'latest_active' => $latestActive,
            'latest_inactive' => $latestInactive,
            'today_date' => $formattedDate
        ]);
    }


    public function dailyAttendanceReport(Request $request)
    {
        $currentYear  = date('Y');
        $currentMonth = date('m');

        $userId = $request->input('user_id');

        $selectedMonthYear = $request->input(
            'month',
            $currentMonth . '-' . $currentYear
        );

        if (!$selectedMonthYear && $request->start_date) {
            $selectedMonthYear = Carbon::parse($request->start_date)->format('m-Y');
        }

        [$monthNumber, $yearNumber] = explode('-', $selectedMonthYear);

        $month = (int) $monthNumber;
        $year  = (int) $yearNumber;

        $users = User::when($userId, function ($q) use ($userId) {
            return $q->where('id', $userId);
        })->get();

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth   = Carbon::create($year, $month, 1)->endOfMonth();

        // company leaves
        $companyLeaveDates = DB::table('company_leaves')
            ->whereDate('leave_date', '>=', $startOfMonth->toDateString())
            ->whereDate('leave_date', '<=', $endOfMonth->toDateString())
            ->pluck('leave_date')
            ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
            ->unique()
            ->values()
            ->toArray();

        $holidayDates = DB::table('holidays')
            ->whereDate('date', '>=', $startOfMonth->toDateString())
            ->whereDate('date', '<=', $endOfMonth->toDateString())
            ->pluck('date')
            ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
            ->toArray();

        $settings = AttendanceSetting::first();
        $lateAllow = $settings->late_allow ?? 3;

        $data = [];
        $totalWorkMinutes = 0;

        foreach ($users as $user) {

            $attendanceRecords = Attendance::where('user_id', $user->id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->orderBy('created_at', 'asc')
                ->get()
                ->groupBy(fn($row) => Carbon::parse($row->created_at)->format('Y-m-d'));

            $trackerLogs = DB::table('tracker_users_logs as t1')
                ->select(
                    DB::raw('DATE(t1.tracker_start_time) as log_date'),
                    't1.user_active',
                    't1.user_inactive'
                )
                ->where('t1.user_id', $user->id)
                ->whereYear('t1.tracker_start_time', $year)
                ->whereMonth('t1.tracker_start_time', $month)
                ->whereRaw("
                t1.id = (
                    SELECT MAX(t2.id)
                    FROM tracker_users_logs t2
                    WHERE t2.user_id = t1.user_id
                    AND DATE(t2.tracker_start_time) = DATE(t1.tracker_start_time)
                )
            ")
                ->get()
                ->keyBy('log_date');

            $lateGraceUsed = 0;

            for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {

                $formattedDate = $date->format('Y-m-d');
                $displayDate   = $date->format('d M Y');

                $records = $attendanceRecords->get($formattedDate, collect());

                $officeIn = $records->sortBy('office_in_time')->first(fn($r) => $r->office_in_time !== null);
                $officeOut = $records->sortByDesc('office_out_time')->first(fn($r) => $r->office_out_time !== null);
                $lunchIn = $records->first(fn($r) => $r->lunch_in_time !== null);
                $lunchOut = $records->first(fn($r) => $r->lunch_out_time !== null);

                $workHours = 'N/A';

                if ($date->isWeekend()) {
                    $status = 'weekend';
                } elseif (in_array($formattedDate, $holidayDates)) {
                    $status = 'holiday';
                } elseif (in_array($formattedDate, $companyLeaveDates)) {
                    $status = 'company_leave';
                } else {
                    $daySummary = $this->summarizeDaySessions($records);

                    if (!$daySummary) {
                        $status = 'absent';
                    } else {
                        $workMinutes = $daySummary['worked_minutes'];
                        $totalWorkMinutes += $workMinutes;
                        $workHours = gmdate('H:i', $workMinutes * 60);

                        $status = $this->classifyDayStatus(
                            $workMinutes,
                            480,
                            $daySummary['office_in'],
                            $lateGraceUsed,
                            $lateAllow
                        );
                    }
                }

                // tracker
                $tracker = $trackerLogs->get($formattedDate);

                $data[] = [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,

                    'date' => $displayDate,
                    'date_raw' => $formattedDate,   // ✅ IMPORTANT FIX

                    'office_in' => $officeIn
                        ? Carbon::parse($officeIn->office_in_time)->format('h:i A')
                        : 'N/A',

                    'office_out' => $officeOut
                        ? Carbon::parse($officeOut->office_out_time)->format('h:i A')
                        : 'N/A',

                    'lunch_in' => $lunchIn
                        ? Carbon::parse($lunchIn->lunch_in_time)->format('h:i A')
                        : 'N/A',

                    'lunch_out' => $lunchOut
                        ? Carbon::parse($lunchOut->lunch_out_time)->format('h:i A')
                        : 'N/A',

                    'user_active_time' => gmdate('H:i:s', (int)($tracker->user_active ?? 0)),
                    'user_inactive_time' => gmdate('H:i:s', (int)($tracker->user_inactive ?? 0)),

                    'work_hours' => $workHours,
                    'status' => $status,

                    'is_sunday' => $date->isSunday(),
                    'is_saturday' => $date->isSaturday(),
                ];
            }
        }

        $hours = floor($totalWorkMinutes / 60);
        $minutes = $totalWorkMinutes % 60;
        $totalWorkHours = sprintf('%d:%02d', $hours, $minutes);

        return view('pages.daily_attendence_report', compact('data', 'totalWorkHours'));
    }
    private function calculateWorkHours($officeIn, $officeOut, $lunchIn, $lunchOut)
    {
        if ($officeIn && $officeOut) {
            $officeInTime = Carbon::parse($officeIn->office_in_time);
            $officeOutTime = Carbon::parse($officeOut->office_out_time);

            if ($lunchIn && $lunchOut) {
                $lunchInTime = Carbon::parse($lunchIn->lunch_in_time);
                $lunchOutTime = Carbon::parse($lunchOut->lunch_out_time);

                $workHours = $officeOutTime->diffInMinutes($officeInTime) - $lunchOutTime->diffInMinutes($lunchInTime);
            } else {
                $workHours = $officeOutTime->diffInMinutes($officeInTime);
            }

            return gmdate('H:i', $workHours * 60);
        }

        return 'N/A';
    }

    public function dailyTrackerHistory(Request $request, $userId)
    {
        $date = $request->date;

        $formattedDate = \Carbon\Carbon::parse($date)->format('F d, Y');

        $logs = DB::table('tracker_users_logs')
            ->where('user_id', $userId)
            ->whereDate('tracker_start_time', $date)
            ->orderBy('tracker_start_time', 'asc')
            ->get();

        $data = $logs->map(function ($log) {
            return [
                'start'    => \Carbon\Carbon::parse($log->tracker_start_time)->format('h:i:s A'),
                'stop'     => $log->tracker_stop_time
                    ? \Carbon\Carbon::parse($log->tracker_stop_time)->format('h:i:s A')
                    : 'Running',
                'active'   => gmdate('H:i:s', (int) $log->user_active),
                'inactive' => gmdate('H:i:s', (int) $log->user_inactive),
                'mouse_clicks' => $log->mouse_clicks,
                'key_presses' => $log->key_presses,
                'reason_stop' => $log->reason_stop,
            ];
        });

        $latestLog = DB::table('tracker_users_logs')
            ->where('user_id', $userId)
            ->whereDate('tracker_start_time', $date)
            ->orderBy('tracker_start_time', 'desc')
            ->first();

        $latestActive = '00:00:00';
        $latestInactive = '00:00:00';

        if ($latestLog) {
            $latestActive = gmdate('H:i:s', (int) $latestLog->user_active);
            $latestInactive = gmdate('H:i:s', (int) $latestLog->user_inactive);
        }

        return response()->json([
            'rows' => $data,
            'latest_active' => $latestActive,
            'latest_inactive' => $latestInactive,
            'selected_date' => $formattedDate
        ]);
    }


    public function employee_attendence_report(Request $request)
    {
        // $today = Carbon::today()->toDateString();
        $users = User::where('user_type', '=', '1')->get();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        $attendanceRecords = Attendance::whereBetween('attendance.office_in_time', [
            $startOfMonth,
            $endOfMonth
        ])
            ->join('users', 'attendance.user_id', '=', 'users.id')
            ->select(
                'users.id as user_id',
                'users.name',
                'users.email',
                'attendance.*'
            )
            ->get();
        $data = [];
        $totalWorkMinutes = 0;
        $recordsByUser = $attendanceRecords->groupBy('user_id');
        $hours = floor($totalWorkMinutes / 60);
        $minutes = $totalWorkMinutes % 60;
        // Format to HH:MM
        $totalWorkHours = sprintf('%d:%02d', $hours, $minutes);
        return view('pages.employee_attendence_report', [
            // 'data' => $data,
            'attendanceRecords' => $attendanceRecords,
        ]);
    }



















    private function getWorkingDays($startDate, $endDate)
    {
        $workingDays = 0;

        $currentDate = $startDate->copy();

        $holidays = DB::table('holidays')
            ->whereDate('date', '>=', $startDate->format('Y-m-d'))
            ->whereDate('date', '<=', $endDate->format('Y-m-d'))
            ->pluck('date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        $companyLeaveDates = DB::table('company_leaves')
            ->whereDate('leave_date', '>=', $startDate->format('Y-m-d'))
            ->whereDate('leave_date', '<=', $endDate->format('Y-m-d'))
            ->pluck('leave_date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        while ($currentDate <= $endDate) {

            $dateKey = $currentDate->format('Y-m-d');

            if (
                !$currentDate->isWeekend() &&
                !in_array($dateKey, $holidays) &&
                !in_array($dateKey, $companyLeaveDates)
            ) {
                $workingDays++;
            }

            $currentDate->addDay();
        }

        return $workingDays;
    }

    /**
     * Sum every attendance row for a single day into one set of totals.
     * Handles mid-day short leave (multiple office-in/office-out sessions
     * on the same date) by summing worked minutes across all sessions and
     * taking the earliest office-in / latest office-out as the day's
     * displayed arrival/departure.
     */
    private function summarizeDaySessions($records)
    {
        $workedMinutes = 0;
        $firstIn = null;
        $lastOut = null;
        $hasAnyRecord = false;

        foreach ($records as $record) {
            if ($record->office_in_time) {
                $hasAnyRecord = true;
                $in = Carbon::parse($record->office_in_time);
                if (!$firstIn || $in->lt($firstIn)) {
                    $firstIn = $in;
                }
            }

            if ($record->office_out_time) {
                $hasAnyRecord = true;
                $out = Carbon::parse($record->office_out_time);
                if (!$lastOut || $out->gt($lastOut)) {
                    $lastOut = $out;
                }
            }

            if ($record->office_in_time && $record->office_out_time) {
                $sessionMinutes = max(0, Carbon::parse($record->office_in_time)
                    ->diffInMinutes(Carbon::parse($record->office_out_time)));

                if ($record->lunch_in_time && $record->lunch_out_time) {
                    $sessionMinutes -= max(0, Carbon::parse($record->lunch_in_time)
                        ->diffInMinutes(Carbon::parse($record->lunch_out_time)));
                }

                if ($record->break_in_time && $record->break_out_time) {
                    $sessionMinutes -= max(0, Carbon::parse($record->break_in_time)
                        ->diffInMinutes(Carbon::parse($record->break_out_time)));
                }

                $workedMinutes += max(0, $sessionMinutes);
            }
        }

        if (!$hasAnyRecord) {
            return null;
        }

        return [
            'office_in' => $firstIn,
            'office_out' => $lastOut,
            'worked_minutes' => $workedMinutes,
        ];
    }

    /**
     * Classify a day as present/late/short_leave/half_day/absent per the
     * confirmed policy: total worked minutes vs the required minutes for
     * the day is what matters (late arrival + early leave + mid-day
     * sessions all just combine into one total), with a separate rule for
     * arrivals beyond the 15-minute grace window. $lateGraceUsed is a
     * running per-month counter passed by reference — the 4th+ grace-window
     * late arrival in a month converts to Short Leave instead of Late.
     */
    private function classifyDayStatus($workedMinutes, $requiredMinutes, $officeIn, &$lateGraceUsed, $lateAllow)
    {
        if ($workedMinutes >= $requiredMinutes) {
            if (!$officeIn) {
                return 'present';
            }

            $officeInFormatted = $officeIn->format('H:i:s');

            if ($officeInFormatted <= '10:00:59') {
                return 'present';
            }

            if ($officeInFormatted <= '10:15:59') {
                $lateGraceUsed++;
                return $lateGraceUsed > $lateAllow ? 'short_leave' : 'late';
            }

            // Beyond the 15-minute grace window entirely — Unpaid Short
            // Leave, regardless of whether the total hours were made up.
            return 'short_leave';
        }

        $missingMinutes = $requiredMinutes - $workedMinutes;

        if ($missingMinutes <= 120) {
            return 'short_leave';
        }

        if ($missingMinutes <= 240) {
            return 'half_day';
        }

        return 'absent';
    }

    private function formatSeconds($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    private function getDateRange($filterType, $request)
    {
        switch ($filterType) {
            case 'month':
                $filterMonth = $request->filter_month
                    ? Carbon::parse($request->filter_month . '-01')
                    : Carbon::now();
                $startOfMonth = $filterMonth->copy()->startOfMonth()->startOfDay();
                $endOfMonth = $filterMonth->isSameMonth(Carbon::now())
                    ? Carbon::today()->endOfDay()
                    : $filterMonth->copy()->endOfMonth()->endOfDay();
                return [
                    $startOfMonth,
                    $endOfMonth,
                ];
            default:
        }
    }

    private function getTrackerSummary($userId, $startDate, $endDate)
    {
        $startDateFormatted = $startDate->format('Y-m-d');
        $endDateFormatted = $endDate->format('Y-m-d');
        return DB::table('tracker_users_logs as t1')
            ->join(DB::raw("
            (
                SELECT
                    user_id,
                    DATE(tracker_start_time) as log_date,
                    MAX(id) as max_id

                FROM tracker_users_logs

                WHERE DATE(tracker_start_time)
                BETWEEN '{$startDateFormatted}'
                AND '{$endDateFormatted}'

                GROUP BY user_id, DATE(tracker_start_time)

            ) as t2
                "), function ($join) {

                $join->on('t1.id', '=', 't2.max_id');
            })
            ->where('t1.user_id', $userId)
            ->selectRaw('
                    SUM(t1.user_active) as total_active,
                    SUM(t1.user_inactive) as total_inactive
                ')
            ->first();
    }



    private function calculatePerformance($late, $shortLeave, $halfDay, $absent, $sandwich = 0)
    {
        $settings = AttendanceSetting::first();
        $lateAllow = $settings->late_allow ?? 3;
        $lateDeduction = $settings->late_deduction ?? 10;
        $shortLeaveAllow = $settings->short_leave_allow ?? 2;
        $shortLeaveDeduction = $settings->short_leave_deduction ?? 5;

        $halfDayAllow = $settings->half_day_allow ?? 1;
        $halfDayDeduction = $settings->half_day_deduction ?? 10;

        $absentAllow = $settings->absent_allow ?? 1;
        $absentDeduction = $settings->absent_deduction ?? 15;

        $sandwichDeduction = $settings->sandwich_deduction ?? 15;
        $performanceScore = 100;
        $extraLate = max(0, $late - $lateAllow);
        $totalDeduction = 0;
        if ($sandwich > 0) {
            $sandwichTotalDeduction = $sandwich * $sandwichDeduction;

            $remainingAbsent = max(0, $absent);
            $remainingHalfDay = max(0, $halfDay);
            $remainingShortLeave = max(0, $shortLeave);
            $extraAbsent = max(
                0,
                $remainingAbsent - $absentAllow
            );
            $extraHalfDay = max(
                0,
                $remainingHalfDay - $halfDayAllow
            );
            $allowShortLeave = true;
            if (
                $remainingHalfDay > 0 &&
                $remainingAbsent > 0
            ) {
                $allowShortLeave = false;
            }

            if ($allowShortLeave) {
                $extraShortLeave = max(
                    0,
                    $remainingShortLeave - $shortLeaveAllow
                );
            } else {
                $extraShortLeave = $remainingShortLeave;
            }
            $totalDeduction =
                ($extraLate * $lateDeduction) +
                ($extraShortLeave * $shortLeaveDeduction) +
                ($extraHalfDay * $halfDayDeduction) +
                ($extraAbsent * $absentDeduction) +
                $sandwichTotalDeduction;
        } else {
            $extraAbsent = max(
                0,
                $absent - $absentAllow
            );
            $extraHalfDay = max(
                0,
                $halfDay - $halfDayAllow
            );
            $allowShortLeave = true;
            // absent + halfday both exist
            if ($halfDay > 0 && $absent > 0) {
                $allowShortLeave = false;
            }
            if ($allowShortLeave) {
                $extraShortLeave = max(
                    0,
                    $shortLeave - $shortLeaveAllow
                );
            } else {
                $extraShortLeave = $shortLeave;
            }
            $totalDeduction =
                ($extraLate * $lateDeduction) +
                ($extraShortLeave * $shortLeaveDeduction) +
                ($extraHalfDay * $halfDayDeduction) +
                ($extraAbsent * $absentDeduction);
        }
        $performanceScore -= $totalDeduction;

        $performanceScore = max(
            0,
            round($performanceScore, 2)
        );
        if ($performanceScore >= 90) {

            $performanceLabel = 'Outstanding ⭐';
        } elseif ($performanceScore >= 80) {

            $performanceLabel = 'Good 👍';
        } elseif ($performanceScore >= 60) {

            $performanceLabel = 'Average 🙂';
        } else {

            $performanceLabel = 'Poor ⚠️';
        }
        return [
            'score' => $performanceScore,
            'label' => $performanceLabel,
            'deduction' => $totalDeduction,
            'extra_late' => $extraLate,
            'extra_short_leave' => $extraShortLeave,
            'extra_half_day' => $extraHalfDay,
            'extra_absent' => $extraAbsent,
            'sandwich_count' => $sandwich,
        ];
    }

    public function attendenceGraphReport(Request $request)
    {
        $currentUser = Auth::user();
        $selectedUserId = $request->input('user_id', 'all');
        $filterType = $request->input('filter', 'this_month');
        $employeeSelected = (
            $selectedUserId &&
            $selectedUserId !== 'all'
        );
        // The 'month' branch already defaults to the current month when
        // filter_month isn't present, so there's no need for a separate
        // 'default_month' branch (that branch didn't exist in
        // getDateRange() and used to fall through to null).
        $filterType = 'month';
        [$startDate, $endDate] = $this->getDateRange(
            $filterType,
            $request
        );
        $settings = AttendanceSetting::first();
        $lateAllow = $settings->late_allow ?? 3;
        $lateDeduction = $settings->late_deduction ?? 10;
        $shortLeaveAllow = $settings->short_leave_allow ?? 2;
        $shortLeaveDeduction = $settings->short_leave_deduction ?? 5;
        $halfDayAllow = $settings->half_day_allow ?? 1;
        $halfDayDeduction = $settings->half_day_deduction ?? 10;
        $absentAllow = $settings->absent_allow ?? 1;
        $absentDeduction = $settings->absent_deduction ?? 15;
        $sandwichDeduction = $settings->sandwich_deduction ?? 15;
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()
                ->startOfMonth()
                ->startOfDay();
            $endDate = Carbon::today()
                ->endOfDay();
        }
        $users = User::where('user_type', '1')
            ->where('user_status', '!=', '0')
            ->get();
        $isAdmin = $currentUser->user_type == 0;
        $isAllUsers = false;
        $targetUsers = collect();
        if ($isAdmin) {
            $isAllUsers = ($selectedUserId === 'all');
            $targetUsers = $isAllUsers
                ? $users
                : $users->where('id', $selectedUserId);
        } else {
            $selectedUserId = $currentUser->id;
            $targetUsers = $users->where(
                'id',
                $currentUser->id
            );

            $employeeSelected = true;
        }

        $allData = [];
        foreach ($targetUsers as $user) {
            $companyLeaveDates = DB::table('company_leaves')
                ->whereDate('leave_date', '>=', $startDate->format('Y-m-d'))
                ->whereDate('leave_date', '<=', $endDate->format('Y-m-d'))
                ->pluck('leave_date')
                ->map(function ($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })
                ->unique()
                ->values()
                ->toArray();

            $holidayDates = DB::table('holidays')
                ->whereDate('date', '>=', $startDate->format('Y-m-d'))
                ->whereDate('date', '<=', $endDate->format('Y-m-d'))
                ->pluck('date')
                ->map(function ($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })
                ->toArray();

            $attendanceRecords = Attendance::where('user_id', $user->id)
                ->whereDate('created_at', '>=', $startDate->format('Y-m-d'))
                ->whereDate('created_at', '<=', $endDate->format('Y-m-d'))
                ->orderBy('created_at')
                ->get()
                ->groupBy(function ($row) {
                    return Carbon::parse($row->created_at)->format('Y-m-d');
                });

            $ontime = 0;
            $late = 0;
            $shortLeave = 0;
            $halfDay = 0;
            $absent = 0;
            $presentDays = 0;
            $lateGraceUsed = 0;

            $workingDays = $this->getWorkingDays(
                $startDate,
                $endDate
            );

            $dailyPoint = $workingDays > 0
                ? (100 / $workingDays)
                : 0;
            $attendanceStatusByDate = [];

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dateKey = $date->format('Y-m-d');

                // Weekends, gazetted holidays, and company-granted leave
                // days are all fully excluded from performance scoring —
                // they don't count as working days at all, whether or not
                // an attendance record happens to exist for them.
                if ($date->isWeekend()) {
                    $attendanceStatusByDate[$dateKey] = 'weekend';
                    continue;
                }

                if (in_array($dateKey, $holidayDates)) {
                    $attendanceStatusByDate[$dateKey] = 'holiday';
                    continue;
                }

                if (in_array($dateKey, $companyLeaveDates)) {
                    $attendanceStatusByDate[$dateKey] = 'company_leave';
                    continue;
                }

                $daySummary = $this->summarizeDaySessions(
                    $attendanceRecords->get($dateKey, collect())
                );

                if (!$daySummary) {
                    $attendanceStatusByDate[$dateKey] = 'absent';
                    $absent++;
                    continue;
                }

                $status = $this->classifyDayStatus(
                    $daySummary['worked_minutes'],
                    480,
                    $daySummary['office_in'],
                    $lateGraceUsed,
                    $lateAllow
                );

                $attendanceStatusByDate[$dateKey] = $status;

                switch ($status) {
                    case 'present':
                        $ontime++;
                        $presentDays++;
                        break;
                    case 'late':
                        $late++;
                        $presentDays++;
                        break;
                    case 'short_leave':
                        $shortLeave++;
                        break;
                    case 'half_day':
                        $halfDay++;
                        break;
                    case 'absent':
                        $absent++;
                        break;
                }
            }

            // Sandwich Rule: any actual leave-type day (absent/short-leave/
            // half-day) touching a run of "closed" days (weekend, holiday,
            // or company-leave) on either side pulls the whole run into one
            // sandwich block. Closed days are worth a full 1.0 day each;
            // the triggering leave day(s) keep their own true fractional
            // value (absent = 1.0, half day = 0.5, short leave = 0.25).
            // This is score-only — it does not touch the leave quota.
            $validLeaveStatuses = ['absent', 'short_leave', 'half_day'];
            $closedStatuses = ['weekend', 'holiday', 'company_leave'];
            $leaveDayValue = [
                'absent' => 1.0,
                'half_day' => 0.5,
                'short_leave' => 0.25,
            ];

            $sandwichDates = [];
            $sandwichDetails = [];
            $processedDates = [];
            $sandwichValue = 0.0;

            $current = $startDate->copy();

            while ($current <= $endDate) {
                $dateKey = $current->format('Y-m-d');
                if (isset($processedDates[$dateKey])) {
                    $current->addDay();
                    continue;
                }
                $chainDates = [];
                $hasLeaveInChain = false;
                $hasClosedInChain = false;
                $temp = $current->copy();
                while ($temp <= $endDate) {
                    $tempKey = $temp->format('Y-m-d');
                    $tempStatus = $attendanceStatusByDate[$tempKey] ?? null;
                    $isLeaveType = in_array($tempStatus, $validLeaveStatuses);
                    $isClosed = in_array($tempStatus, $closedStatuses);
                    if ($isLeaveType || $isClosed) {
                        $chainDates[] = $tempKey;
                        if ($isLeaveType) {
                            $hasLeaveInChain = true;
                        }
                        if ($isClosed) {
                            $hasClosedInChain = true;
                        }
                        $temp->addDay();
                        continue;
                    }
                    break;
                }

                // A single leave day touching a closed block on EITHER
                // side is enough — no symmetric "bookending" required.
                $isSandwich = count($chainDates) > 1 && $hasLeaveInChain && $hasClosedInChain;

                if ($isSandwich) {
                    sort($chainDates);
                    $blockValue = 0.0;
                    foreach ($chainDates as $d) {
                        if (isset($sandwichDates[$d])) {
                            continue;
                        }
                        $sandwichDates[$d] = true;
                        $processedDates[$d] = true;
                        $status = $attendanceStatusByDate[$d] ?? null;
                        $blockValue += $leaveDayValue[$status] ?? 1.0;
                        if ($status == 'absent') {
                            $absent = max(0, $absent - 1);
                        } elseif ($status == 'short_leave') {
                            $shortLeave = max(0, $shortLeave - 1);
                        } elseif ($status == 'half_day') {
                            $halfDay = max(0, $halfDay - 1);
                        }
                    }
                    $sandwichValue += $blockValue;
                    $sandwichDetails[] = [
                        'from' => reset($chainDates),
                        'to' => end($chainDates),
                        'total_days' => count($chainDates),
                        'value' => $blockValue,
                        'dates' => $chainDates
                    ];
                    $current = $temp->copy();
                    continue;
                }
                $current->addDay();
            }

            // Weighted sandwich day-value (e.g. 2.5), not a raw day count —
            // this is what should feed the score, per confirmed policy.
            $sandwich = $sandwichValue;

            $performanceData = $this->calculatePerformance(
                $late,
                $shortLeave,
                $halfDay,
                $absent,
                $sandwich
            );
            $performanceScore = $performanceData['score'];
            $performanceLabel = $performanceData['label'];
            $totalDeduction = $performanceData['deduction'];

            $trackerLogs = $this->getTrackerSummary(
                $user->id,
                $startDate,
                $endDate
            );
            $totalActiveSeconds = (int) (
                $trackerLogs->total_active ?? 0
            );
            $totalInactiveSeconds = (int) (
                $trackerLogs->total_inactive ?? 0
            );
            $allData[] = [
                'user' => $user,
                'ontime_count' => $ontime,
                'late_count' => $late,
                'short_leave_count' => $shortLeave,
                'half_day_count' => $halfDay,
                'absent_count' => $absent,
                'sandwich_count' => $sandwich,
                'sandwich_details' => $sandwichDetails,
                'working_days' => $workingDays,
                'present_days' => $presentDays,
                'daily_point' => round($dailyPoint, 2),
                'allowed_late' => $lateAllow,
                'late_deduction' => $lateDeduction,
                'allowed_short_leave' => $shortLeaveAllow,
                'short_leave_deduction' => $shortLeaveDeduction,
                'allowed_half_day' => $halfDayAllow,
                'half_day_deduction' => $halfDayDeduction,
                'allowed_absent' => $absentAllow,
                'absent_deduction' => $absentDeduction,
                'sandwich_deduction' => $sandwichDeduction,
                'performance_score' => $performanceScore,
                'performance_label' => $performanceLabel,
                'total_deduction' => $totalDeduction,
                'chart_labels' => [
                    'On Time',
                    'Late',
                    'Short Leave',
                    'Half Day',
                    'Absent',
                    'Sandwich'
                ],
                'chart_status_data' => [
                    $ontime,
                    $late,
                    $shortLeave,
                    $halfDay,
                    $absent,
                    $sandwich
                ],
                'chart_status_colors' => [
                    '#22c55e',
                    '#f59e0b',
                    '#0ea5e9',
                    '#dc2626',
                    '#991b1b',
                    '#8B4513'
                ],
                'total_active' => $this->formatSeconds(
                    $totalActiveSeconds
                ),
                'total_inactive' => $this->formatSeconds(
                    $totalInactiveSeconds
                ),
                'total_active_sec' => $totalActiveSeconds,
                'total_inactive_sec' => $totalInactiveSeconds,
            ];
        }
        return view('pages.attendence_graph_report', [
            'users' => $isAdmin
                ? $users
                : collect(),
            'allData' => $allData,
            'reportData' => $allData[0] ?? null,
            'isAllUsers' => $isAllUsers,
            'isAdmin' => $isAdmin,
            'selectedUserId' => $selectedUserId,
            'filterType' => $filterType,
            'filterMonth' => $request->input(
                'filter_month',
                Carbon::now()->format('Y-m')
            ),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'employeeSelected' => $employeeSelected,
        ]);
    }





    public function attendancesSettings()
    {
        // The attendance_policy table can legitimately have zero rows
        // (e.g. a fresh install, or before anyone has saved settings yet).
        // Fall back to the same defaults used everywhere else in this
        // controller so the form shows real numbers instead of blanks.
        $settings = AttendanceSetting::first() ?? new AttendanceSetting([
            'late_allow' => 3,
            'late_deduction' => 10,
            'short_leave_allow' => 2,
            'short_leave_deduction' => 5,
            'half_day_allow' => 1,
            'half_day_deduction' => 10,
            'absent_allow' => 1,
            'absent_deduction' => 15,
            'sandwich_deduction' => 15,
        ]);
        return view(
            'pages.attendance_setting',
            compact('settings')
        );
    }
    public function attendanceSettingsUpdate(Request $request)
    {
        $validated = $request->validate([
            'late_allow' => 'required|integer',
            'late_deduction' => 'required|integer',
            'short_leave_allow' => 'required|integer',
            'short_leave_deduction' => 'required|integer',
            'half_day_allow' => 'required|integer',
            'half_day_deduction' => 'required|integer',
            'absent_allow' => 'required|integer',
            'absent_deduction' => 'required|integer',
            'sandwich_deduction' => 'required|integer',
        ]);
        // Was a blind `where('id', 1)->update()`, which silently updates
        // zero rows (and shows a false "success" message) whenever the
        // attendance_policy table has no row yet — updateOrCreate fixes
        // that by creating the row the first time this is ever saved.
        AttendanceSetting::updateOrCreate(['id' => 1], $validated);
        return back()->with(
            'success',
            'Settings Updated Successfully'
        );
    }
}
