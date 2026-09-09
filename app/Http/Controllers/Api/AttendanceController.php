<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\TrackerUsersLog;
use App\Models\TrackerLog;
use App\Models\AttendanceSetting;


class AttendanceController extends Controller
{
    public $currentTime;
    public $currentDate;
    public $ipAddress;

    public function __construct()
    {
        $this->currentTime = Carbon::now()->format('Y-m-d H:i:s');
        $this->currentDate = Carbon::now()->format('Y-m-d');
        $this->ipAddress = get_client_ip();
        // $this->ipAddress = getPublicIp();
    }

    public function officeIn(Request $request)
    {
        try {

            $validation = Validator::make($request->all(), [
                'user_id' => 'required',
            ]);

            if ($validation->fails()) {

                return response()->json([
                    'success' => false,
                    'message' => $validation->errors()
                ], 422);
            }

            // ==================================================
            // CHECK ALREADY CHECKED IN TODAY
            // ==================================================

            $existingAttendance = Attendance::where('user_id', $request->user_id)
                ->whereDate('office_in_time', Carbon::today())
                ->whereNull('office_out_time')
                ->first();

            // ==================================================
            // IF EXISTS RETURN SAME RECORD
            // IMPORTANT FOR OFFLINE SYNC
            // ==================================================

            if ($existingAttendance) {

                return response()->json([
                    'success' => true,
                    'message' => 'Already checked in today',
                    'response' => $existingAttendance,
                ], 200);
            }

            // ==================================================
            // CREATE NEW ATTENDANCE
            // ==================================================

            $officeInTime = $request->office_in_time
                ? Carbon::parse($request->office_in_time)
                : Carbon::now();

            $officeIn = Attendance::create([
                'user_id' => $request->user_id,
                'office_in_time' => $officeInTime,
                'total_working_hours' => 0,
                'ip' => $this->ipAddress,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Office In Successful',
                'response' => $officeIn,
            ], 200);
        } catch (\Exception $e) {

            \Log::error('OfficeIn Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function lunchIn(Request $request)
    {
        // Validate user_id (attendance_id is optional for flexibility)
        $validation = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors()], 422);
        }

        // ✅ FIX: If attendance_id passed, use it directly
        if ($request->attendance_id) {
            $attendance = Attendance::find($request->attendance_id);
        } else {
            // Fallback to user_id lookup
            $attendance = Attendance::where('user_id', $request->user_id)
                ->whereDate('created_at', $this->currentDate)
                ->whereNull('lunch_in_time')
                ->whereNull('office_out_time')
                ->whereNotNull('office_in_time')
                ->first();
        }

        if ($attendance) {
            $attendance->update([
                'lunch_in_time' => Carbon::now(),
                'ip' => $this->ipAddress,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your lunch session started at ' . $this->currentTime,
                'attendance_id' => $attendance->id, // ✅ RETURN THIS
            ], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 400);
        }
    }

    public function lunchOut(Request $request)
    {
        // Validate user_id
        $validation = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors()], 422);
        }

        // ✅ FIX: If attendance_id passed, use it directly
        if ($request->attendance_id) {
            $attendance = Attendance::find($request->attendance_id);
        } else {
            // Fallback to user_id lookup
            $attendance = Attendance::where('user_id', $request->user_id)
                ->whereDate('created_at', $this->currentDate)
                ->whereNotNull('lunch_in_time')
                ->whereNull('lunch_out_time')
                ->whereNull('office_out_time')
                ->first();
        }

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Lunch out is not allowed without a prior lunch in.'
            ], 400);
        }

        // ✅ FIX: Check lunch_in_time exists & lunch_out_time is null
        if (!$attendance->lunch_in_time || $attendance->lunch_out_time) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid lunch session state.'
            ], 400);
        }

        $lunchOutTime = now();
        $attendance->update([
            'lunch_out_time' => $lunchOutTime,
            'ip' => $this->ipAddress,
        ]);

        // ✅ OPTIONAL: Update tracker logs for lunch time as inactive
        $lastTracker = DB::table('tracker_users_logs')
            ->where('attendance_id', $attendance->id)
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastTracker && !$lastTracker->tracker_stop_time) {
            $inactiveSeconds = $lunchOutTime->diffInSeconds(
                Carbon::parse($attendance->lunch_in_time)
            );

            DB::table('tracker_users_logs')
                ->where('id', $lastTracker->id)
                ->update([
                    'user_inactive' => ($lastTracker->user_inactive ?? 0) + $inactiveSeconds,
                    'updated_at' => now(),
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Your lunch session ended at ' . $this->currentTime,
            'attendance_id' => $attendance->id, // ✅ RETURN THIS
        ], 200);
    }
    public function officeOut(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()
            ], 422);
        }

        $userId = $request->user_id;
        $ipAddress = get_client_ip();
        $currentDate = now()->toDateString();

        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('created_at', $currentDate)
            ->whereNotNull('office_in_time')
            ->whereNull('office_out_time')
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No active office session found'
            ], 404);
        }

        $officeOutTime = now();


        $attendance->update([
            'office_out_time' => $officeOutTime,
            'ip' => $ipAddress,
        ]);

        $officeIn  = Carbon::parse($attendance->office_in_time);
        $officeOut = Carbon::parse($officeOutTime);


        $officeTotalSeconds = $officeOut->diffInSeconds($officeIn);

        $lunchSeconds = 0;
        if ($attendance->lunch_in_time && $attendance->lunch_out_time) {
            $lunchIn  = Carbon::parse($attendance->lunch_in_time);
            $lunchOut = Carbon::parse($attendance->lunch_out_time);

            $lunchSeconds = $lunchOut->diffInSeconds($lunchIn);
        }

        $effectiveWorkingSeconds = max($officeTotalSeconds - $lunchSeconds, 0);


        $activeSeconds = DB::table('tracker_users_logs')
            ->where('attendance_id', $attendance->id)
            ->whereNotNull('working_duration')
            ->get()
            ->sum(function ($log) {
                return Carbon::parse("00:00:00")
                    ->diffInSeconds(Carbon::parse($log->working_duration));
            });


        $inactiveSeconds = max($effectiveWorkingSeconds - $activeSeconds, 0);

        $attendance->update([
            'total_working_hours' => $officeTotalSeconds,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success'            => true,
            'message'            => 'Office session ended successfully.',
            'office_total'       => gmdate("H:i:s", $officeTotalSeconds),
            'lunch_total'        => gmdate("H:i:s", $lunchSeconds),
            'effective_working'  => gmdate("H:i:s", $effectiveWorkingSeconds),
            'user_active'        => gmdate("H:i:s", $activeSeconds),
            'user_inactive'      => gmdate("H:i:s", $inactiveSeconds),
        ], 200);
    }


    protected function timeToSeconds(string $time): int
    {
        [$hours, $minutes, $seconds] = array_map('intval', explode(':', $time));
        return ($hours * 3600) + ($minutes * 60) + $seconds;
    }

    public function userTrackerStatus(Request $request)
    {
        $userId = Auth::id();
        $currentDate = now()->toDateString();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('created_at', $currentDate)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No attendance found for today.'
            ], 404);
        }


        $latestLog = DB::table('tracker_users_logs')
            ->where('user_id', $userId)
            ->where('attendance_id', $attendance->id)
            ->latest('id')
            ->first();
        $isRunning = false;
        $lastStartTime = null;

        if ($latestLog) {
            // agar stop time NULL hai → matlab timer chal raha hai
            if (is_null($latestLog->tracker_stop_time)) {
                $isRunning = true;
                $lastStartTime = $latestLog->tracker_start_time;
            }
        }

        // echo'<pre>';
        // print_r($latestLog);
        // die("ppp");

        $activeSeconds   = $latestLog ? (int)$latestLog->user_active : 0;
        $inactiveSeconds = $latestLog ? (int)$latestLog->user_inactive : 0;
        $totalWorkingSeconds = (int) $attendance->total_working_hours;


        return response()->json([
            'success' => true,
            'message' => 'Employee tracker status fetched successfully.',
            'response' => [
                'attendance_id'        => $attendance->id,
                'office_in_time'       => $attendance->office_in_time,
                'office_out_time'      => $attendance->office_out_time,
                'lunch_in_time'        => $attendance->lunch_in_time,
                'lunch_out_time'       => $attendance->lunch_out_time,
                'break_in_time'        => $attendance->break_in_time,
                'break_out_time'       => $attendance->break_out_time,

                'user_active'          => gmdate("H:i:s", $activeSeconds),
                'user_active_seconds'  => $activeSeconds,

                'user_inactive'        => gmdate("H:i:s", $inactiveSeconds),
                'user_inactive_seconds' => $inactiveSeconds,
                'total_working_hours'   => gmdate("H:i:s", $totalWorkingSeconds),
                'total_working_seconds' => $totalWorkingSeconds,
                // ✅ ADD THESE
                'is_running' => $isRunning,
                'tracker_start_time' => $lastStartTime,
            ],
        ], 200);
    }



    public function startTracker(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required',
            'tracker_start_time' => 'required',
            'current_timee' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors()], 422);
        }

        try {
            $tracker_start_time = Carbon::createFromFormat('H:i:s', $request->tracker_start_time);
            $current_time = Carbon::createFromFormat('H:i:s', $request->current_timee);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid time format. Expected HH:MM:SS'
            ], 422);
        }

        $time_diff_in_seconds = $current_time->diffInSeconds($tracker_start_time);
        $todayDate = Carbon::today()->toDateString();

        $track = Attendance::where('user_id', $request->user_id)
            ->whereDate('created_at', $todayDate)
            ->whereNull('office_out_time')
            ->whereNotNull('office_in_time')
            ->first();

        if (!$track) {
            return response()->json(['success' => false, 'message' => 'No matching record found.'], 404);
        }

        $existing_time_in_seconds = (int) ($track->total_working_hours ?? 0);
        // echo"<pre>";
        // print_r($track);
        // die("oppoop");
        $track->update([
            'total_working_hours' => $existing_time_in_seconds + $time_diff_in_seconds
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tracker resumed at this moment!',
        ], 200);
    }



    public function startTrackerSession(Request $request)
    {
        $ipAddress = get_client_ip();
        $currentDate = now()->toDateString();
        $userId = Auth::id();
        $startReason = $request->start_reason ?? 'manual_start';
        // $startReason = $request->start_reason ?? 'manual_start';

        $lastLog = DB::table('tracker_users_logs')
            ->where('user_id', $userId)
            ->where('attendance_id', $request->attendance_id)
            ->whereDate('tracker_start_time', $currentDate)
            ->orderBy('id', 'DESC')
            ->first();

        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('created_at', $currentDate)
            ->first();

        $lunchSeconds = 0;
        if ($attendance && $attendance->lunch_in_time && $attendance->lunch_out_time) {
            $lunchSeconds = Carbon::parse($attendance->lunch_out_time)
                ->diffInSeconds(Carbon::parse($attendance->lunch_in_time));
        }

        $inactiveSeconds = 0;
        $lastStoppedLog = DB::table('tracker_users_logs')
            ->where('user_id', $userId)
            ->where('attendance_id', $request->attendance_id)
            ->whereNotNull('tracker_stop_time')
            ->orderBy('tracker_stop_time', 'DESC')
            ->first();

        if ($lastStoppedLog && $lastStoppedLog->reason_stop === 'idle_stop') {
            $lastStopTime = Carbon::parse($lastStoppedLog->tracker_stop_time);
            $inactiveSeconds = now()->diffInSeconds($lastStopTime);
        } else {
            $inactiveSeconds = 0;
        }


        $previousUserInactive = DB::table('tracker_users_logs')
            ->where('user_id', $userId)
            ->where('attendance_id', $request->attendance_id)
            ->orderBy('id', 'DESC')
            ->value('user_inactive') ?? 0;
        //  echo"<pre>";
        //  print_r(gettype($previousUserInactive))   ;
        //  die("pppp");

        $previousUserInactive = (int) $previousUserInactive;



        if ($attendance && $attendance->lunch_out_time && $lastStoppedLog) {
            $lunchOut = Carbon::parse($attendance->lunch_out_time);
            $lastStop = Carbon::parse($lastStoppedLog->tracker_stop_time);
            if ($lastStop->greaterThan($lunchOut)) {
                $lunchSeconds = 0;
            }
        }

        $finalInactiveSeconds = max(
            0,
            ($previousUserInactive + $inactiveSeconds) - 0
        );

        $sessionCount = DB::table('tracker_users_logs')
            ->where('user_id', $userId)
            ->where('attendance_id', $request->attendance_id)
            ->whereDate('tracker_start_time', $currentDate)
            ->count() + 1;

        DB::table('tracker_users_logs')->insert([
            'user_id'            => $userId,
            'attendance_id'      => $request->attendance_id,
            'tracker_start_time' => now(),
            'session_count'      => $sessionCount,
            'user_active'        => $lastLog->user_active ?? 0,
            'user_inactive'      => $finalInactiveSeconds,
            'reason_start'      => $startReason,
            'reason_stop'        => null,
            'ip'                 => $ipAddress,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Work started successfully',
            'tracker_start_time' => now()->format("H:i:s"),
            'previous_user_active' => $lastLog->user_active ?? 0,
        ]);
    }

    public function stopTrackerSession(Request $request)
    {
        $request->validate([
            'attendance_id'           => 'required',
            'stop_reason'             => 'nullable|string',
            'final_active_seconds'    => 'required|integer',
            'final_inactive_seconds'  => 'required|integer',
            'mouse_clicks'            => 'nullable|integer',
            'key_presses'             => 'nullable|integer',
        ]);

        $ipAddress = get_client_ip();

        $trackerLog = DB::table('tracker_users_logs')
            ->where('user_id', auth()->id())
            ->where('attendance_id', $request->attendance_id)
            ->whereNull('tracker_stop_time')
            ->orderBy('tracker_start_time', 'desc')
            ->first();



        if (!$trackerLog) {
            return response()->json([
                'success' => false,
                'message' => 'No active session found'
            ], 404);
        }

        $stop = now();

        $finalActive   = (int) $request->final_active_seconds;
        $finalInactive = (int) $request->final_inactive_seconds;


        $actionReason = $request->stop_reason ?? 'manual_stop';
        $mouseClicks = (int) $request->mouse_clicks;
        $keyPresses  = (int) $request->key_presses;


        DB::table('tracker_users_logs')
            ->where('id', $trackerLog->id)
            ->update([
                'tracker_stop_time' => $stop,
                'working_duration'  => gmdate("H:i:s", $finalActive),
                'user_active'       => $finalActive,
                'user_inactive'     => $finalInactive,
                'reason_stop'       => $actionReason,
                'mouse_clicks'      => $mouseClicks,
                'key_presses'       => $keyPresses,
                'ip'                => $ipAddress,
                'updated_at'        => now(),
            ]);


        $totalDurationSeconds = DB::table('tracker_users_logs')
            ->where('user_id', auth()->id())
            ->where('attendance_id', $request->attendance_id)
            ->whereNotNull('working_duration')
            ->selectRaw("SUM(TIME_TO_SEC(working_duration)) AS total_seconds")
            ->value('total_seconds') ?? 0;

        $message = $actionReason === 'idle_stop'
            ? 'Tracker stopped due to inactivity'
            : 'Tracker stopped manually';

        return response()->json([
            'success'                => true,
            'stop_type'              => $actionReason,
            'message'                => $message,
            'tracker_stop_time'      => $stop->format("H:i:s"),
            'working_duration'       => gmdate("H:i:s", $finalActive),
            'total_duration'         => gmdate("H:i:s", $totalDurationSeconds),
            'previous_user_active'   => $finalActive,
            'user_inactive'          => gmdate("H:i:s", $finalInactive),
            'user_inactive_seconds'  => $finalInactive,
        ]);
    }






    public function getUserHistory(Request $request)
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $currentDate = now()->toDateString();

        // Get all logs ordered by start time
        $logs = DB::table('tracker_users_logs')
            ->select(
                'id',
                'user_id',
                'attendance_id',
                'tracker_start_time',
                'tracker_stop_time',
                'working_duration',
                'user_active',
                'user_inactive',  // ✅ Get the stored inactive value
                'reason_start',
                'reason_stop',
                'mouse_clicks',
                'key_presses',
                'session_count',
                'ip',
                'remarks',
                'created_at',
                'updated_at'
            )
            ->where('user_id', $userId)
            ->whereDate('created_at', $currentDate)
            ->orderBy('tracker_start_time', 'asc')
            ->get();

        $processedLogs = [];
        $previousLog = null;

        foreach ($logs as $index => $log) {
            $startTime = $log->tracker_start_time ? strtotime($log->tracker_start_time) : null;
            $stopTime = $log->tracker_stop_time ? strtotime($log->tracker_stop_time) : null;

            // Calculate session duration (for reference only)
            if ($startTime && $stopTime && $stopTime > $startTime) {
                $sessionDuration = $stopTime - $startTime;
            } elseif ($startTime && !$stopTime) {
                // Still running
                $sessionDuration = time() - $startTime;
            } else {
                $sessionDuration = 0;
            }

            // ✅ GET the active time from database
            $activeTime = (int)($log->user_active ?? 0);

            // ✅ GET the inactive time from database - DO NOT RECALCULATE!
            $inactiveTime = (int)($log->user_inactive ?? 0);

            // Check if session is still running
            $isRunning = (!$log->tracker_stop_time || $log->tracker_stop_time == '0000-00-00 00:00:00');

            $processedLogs[] = (object)[
                'id' => $log->id,
                'user_id' => $log->user_id,
                'attendance_id' => $log->attendance_id,
                'tracker_start_time' => $log->tracker_start_time,
                'tracker_stop_time' => $log->tracker_stop_time,
                'working_duration' => $log->working_duration,
                'user_active' => $activeTime,                    // ✅ From DB
                'user_inactive' => $inactiveTime,                // ✅ From DB (not calculated)
                'session_duration' => $sessionDuration,
                'reason_start' => $log->reason_start,
                'reason_stop' => $log->reason_stop,
                'mouse_clicks' => (int)($log->mouse_clicks ?? 0),
                'key_presses' => (int)($log->key_presses ?? 0),
                'session_count' => (int)($log->session_count ?? 0),
                'ip' => $log->ip,
                'remarks' => $log->remarks,
                'created_at' => $log->created_at,
                'updated_at' => $log->updated_at,
                'is_running' => $isRunning,
            ];

            $previousLog = $log;
        }

        // Calculate totals using database values
        $totalActive = array_sum(array_column($processedLogs, 'user_active'));
        $totalInactive = array_sum(array_column($processedLogs, 'user_inactive'));
        $totalSessionDuration = array_sum(array_column($processedLogs, 'session_duration'));

        // Calculate total working day duration
        $firstStart = $logs->first()?->tracker_start_time;
        $lastStop = $logs->last()?->tracker_stop_time;
        $totalDayDuration = 0;

        if ($firstStart) {
            $firstStartTime = strtotime($firstStart);
            if ($lastStop && $lastStop != '0000-00-00 00:00:00') {
                $lastStopTime = strtotime($lastStop);
                $totalDayDuration = $lastStopTime - $firstStartTime;
            } else {
                $totalDayDuration = time() - $firstStartTime;
            }
        }

        return response()->json([
            'success' => true,
            'data' => array_reverse($processedLogs), // Reverse for latest first
            'summary' => [
                'total_active' => $totalActive,
                'total_inactive' => $totalInactive,
                'total_session_duration' => $totalSessionDuration,
                'total_day_duration' => max(0, $totalDayDuration),
                'total_sessions' => count($processedLogs),
                'total_mouse_clicks' => $logs->sum('mouse_clicks'),
                'total_key_presses' => $logs->sum('key_presses'),
                'overall_activity_percentage' => $totalDayDuration > 0 ? round(($totalActive / $totalDayDuration) * 100, 2) : 0,
            ],
            'debug' => [
                'date' => $currentDate,
                'user_id' => $userId,
                'calculation_method' => 'Using stored database values (NO RECALCULATION)'
            ]
        ]);
    }

    /**
     * Optimized version using MySQL - also using stored values
     */
    public function getUserHistoryOptimized(Request $request)
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $currentDate = now()->toDateString();

        // ✅ Simply fetch the stored values - no MySQL calculations
        $logs = DB::table('tracker_users_logs')
            ->select(
                'id',
                'user_id',
                'attendance_id',
                'tracker_start_time',
                'tracker_stop_time',
                'working_duration',
                'user_active',
                'user_inactive',  // ✅ Use stored value directly
                'reason_start',
                'reason_stop',
                'mouse_clicks',
                'key_presses',
                'session_count',
                'ip',
                'remarks',
                'created_at',
                'updated_at'
            )
            ->where('user_id', $userId)
            ->whereDate('created_at', $currentDate)
            ->orderBy('tracker_start_time', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs,
            'debug' => [
                'total_records' => count($logs),
                'date' => $currentDate,
                'user_id' => $userId,
                'method' => 'Using stored database values (NO RECALCULATION)'
            ]
        ]);
    }




    public function syncOfflineData(Request $request)
    {
        DB::beginTransaction();

        try {

            $offlineData = $request->offline_data;

            $attendanceMap = [];

            // =====================================
            // SORT DATA BY TIME
            // =====================================

            usort($offlineData, function ($a, $b) {

                return strtotime($a['saved_at'])
                    - strtotime($b['saved_at']);
            });

            // =====================================
            // FIRST LOOP -> CREATE ATTENDANCE
            // =====================================

            foreach ($offlineData as $item) {

                if ($item['type'] !== 'punch_in') {
                    continue;
                }

                $attendance = Attendance::create([

                    'user_id' => $item['user_id'],

                    'office_in_time' => Carbon::parse(
                        $item['office_in_time']
                    )->format('Y-m-d H:i:s'),
                ]);

                // LOCAL => REAL ID
                $attendanceMap[$item['local_attendance_id']] = $attendance->id;
            }

            // =====================================
            // SECOND LOOP
            // =====================================

            foreach ($offlineData as $item) {

                if ($item['type'] === 'punch_in') {
                    continue;
                }

                $attendanceId =
                    $attendanceMap[$item['local_attendance_id']] ?? null;

                if (!$attendanceId) {
                    continue;
                }

                $attendance =
                    Attendance::find($attendanceId);

                if (!$attendance) {
                    continue;
                }

                // =====================================
                // LUNCH IN
                // =====================================

                if ($item['type'] === 'lunch_in') {

                    $attendance->update([

                        'lunch_in_time' => Carbon::parse(
                            $item['lunch_in_time']
                        )->format('Y-m-d H:i:s')
                    ]);

                    continue;
                }

                // =====================================
                // LUNCH OUT
                // =====================================

                if ($item['type'] === 'lunch_out') {

                    $attendance->update([

                        'lunch_out_time' => Carbon::parse(
                            $item['lunch_out_time']
                        )->format('Y-m-d H:i:s')
                    ]);

                    continue;
                }

                // =====================================
                // TRACKER START
                // =====================================

                if ($item['type'] === 'tracker_start') {

                    TrackerLog::create([

                        'attendance_id' => $attendanceId,

                        'user_id' => $item['user_id'],

                        'tracker_start_time' => Carbon::parse(
                            $item['time']
                        )->format('Y-m-d H:i:s'),

                        'reason_start' =>
                        $item['start_reason'],

                        'session_count' => 1,

                        'mouse_clicks' => 0,

                        'key_presses' => 0,

                        'user_active' => 0,

                        'user_inactive' => 0,
                    ]);

                    continue;
                }

                // =====================================
                // TRACKER STOP
                // =====================================

                if ($item['type'] === 'tracker_stop') {

                    // ✅ FIND LAST OPEN SESSION

                    $trackerLog = TrackerLog::where(
                        'attendance_id',
                        $attendanceId
                    )
                        ->whereNull(
                            'tracker_stop_time'
                        )
                        ->latest('id')
                        ->first();

                    // ✅ IF NO OPEN SESSION FOUND
                    if (!$trackerLog) {
                        continue;
                    }

                    $trackerLog->update([

                        'tracker_stop_time' => Carbon::parse(
                            $item['saved_at']
                        )->format('Y-m-d H:i:s'),

                        'reason_stop' =>
                        $item['stop_reason'],

                        'user_active' =>
                        (int) (
                            $item['final_active_seconds']
                            ?? 0
                        ),

                        'user_inactive' =>
                        (int) (
                            $item['final_inactive_seconds']
                            ?? 0
                        ),

                        'mouse_clicks' =>
                        (int) (
                            $item['mouse_clicks']
                            ?? 0
                        ),

                        'key_presses' =>
                        (int) (
                            $item['key_presses']
                            ?? 0
                        ),
                    ]);

                    continue;
                }

                // =====================================
                // FINAL ATTENDANCE
                // =====================================

                if (
                    $item['type']
                    === 'final_attendance'
                ) {

                    $attendance->update([

                        'office_out_time' =>
                        !empty($item['office_out_time'])
                            ? Carbon::parse(
                                $item['office_out_time']
                            )->format('Y-m-d H:i:s')
                            : null,

                        'lunch_in_time' =>
                        !empty($item['lunch_in_time'])
                            ? Carbon::parse(
                                $item['lunch_in_time']
                            )->format('Y-m-d H:i:s')
                            : null,

                        'lunch_out_time' =>
                        !empty($item['lunch_out_time'])
                            ? Carbon::parse(
                                $item['lunch_out_time']
                            )->format('Y-m-d H:i:s')
                            : null,
                    ]);

                    // ✅ CLOSE ANY OPEN TRACKER SESSION

                    $openTracker =
                        TrackerLog::where(
                            'attendance_id',
                            $attendanceId
                        )
                        ->whereNull(
                            'tracker_stop_time'
                        )
                        ->latest('id')
                        ->first();

                    if ($openTracker) {

                        $openTracker->update([

                            'tracker_stop_time' =>
                            Carbon::parse(
                                $item['office_out_time']
                            )->format('Y-m-d H:i:s'),

                            'reason_stop' =>
                            'final_punch_out',

                            'user_active' =>
                            (int) (
                                $item['final_active_seconds']
                                ?? 0
                            ),

                            'user_inactive' =>
                            (int) (
                                $item['final_inactive_seconds']
                                ?? 0
                            ),

                            'mouse_clicks' =>
                            (int) (
                                $item['mouse_clicks']
                                ?? 0
                            ),

                            'key_presses' =>
                            (int) (
                                $item['key_presses']
                                ?? 0
                            ),
                        ]);
                    }

                    continue;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Offline data synced successfully'
            ]);
        } catch (\Exception $err) {

            DB::rollBack();

            Log::error('Bulk Sync Error', [

                'message' => $err->getMessage(),

                'line' => $err->getLine(),

                'file' => $err->getFile()
            ]);

            return response()->json([

                'success' => false,

                'message' => $err->getMessage()

            ], 500);
        }
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

        while ($currentDate <= $endDate) {

            if (
                !$currentDate->isWeekend() &&
                !in_array($currentDate->format('Y-m-d'), $holidays)
            ) {
                $workingDays++;
            }

            $currentDate->addDay();
        }

        return $workingDays;
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

    private function getRequiredMinutesForDay($dateKey, $companyLeaves)
    {
        $requiredMinutes = 480;

        if (isset($companyLeaves[$dateKey])) {

            foreach ($companyLeaves[$dateKey] as $leave) {

                if ($leave->leave_type == 'full_day') {
                    return 0;
                }

                if (
                    !empty($leave->start_time) &&
                    !empty($leave->end_time)
                ) {

                    $start = Carbon::parse(
                        $dateKey . ' ' . $leave->start_time
                    );

                    $end = Carbon::parse(
                        $dateKey . ' ' . $leave->end_time
                    );

                    $requiredMinutes -= $start->diffInMinutes($end);
                }
            }
        }

        return max(0, $requiredMinutes);
    }
    public function attendenceGraphReportApi(Request $request)
    {
        try {
            // Validate request parameters
            $validated = $request->validate([
                'user_id' => 'nullable|string',
                'filter' => 'nullable|string|in:this_month,month,default_month',
                'filter_month' => 'nullable|date_format:Y-m',
            ]);

            $currentUser = Auth::user();
            $selectedUserId = $validated['user_id'] ?? 'all';
            $filterType = $validated['filter'] ?? 'this_month';
            $employeeSelected = (
                $selectedUserId &&
                $selectedUserId !== 'all'
            );

            if (!empty($validated['filter_month'])) {
                $filterType = 'month';
            } else {
                $filterType = 'default_month';
            }

            // Get date range
            $request->merge([
                'filter' => $filterType,
                'filter_month' => $validated['filter_month'] ?? null,
            ]);

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
                $companyLeaves = DB::table('company_leaves')
                    ->whereDate('leave_date', '>=', $startDate->format('Y-m-d'))
                    ->whereDate('leave_date', '<=', $endDate->format('Y-m-d'))
                    ->get()
                    ->groupBy(function ($item) {
                        return Carbon::parse($item->leave_date)->format('Y-m-d');
                    });

                $attendanceRecords = Attendance::where(
                    'user_id',
                    $user->id
                )
                    ->whereDate(
                        'created_at',
                        '>=',
                        $startDate->format('Y-m-d')
                    )
                    ->whereDate(
                        'created_at',
                        '<=',
                        $endDate->format('Y-m-d')
                    )
                    ->whereNotNull('office_in_time')
                    ->orderBy('created_at')
                    ->get();

                $ontime = 0;
                $late = 0;
                $shortLeave = 0;
                $halfDay = 0;
                $absent = 0;
                $sandwich = 0;
                $workingDays = $this->getWorkingDays(
                    $startDate,
                    $endDate
                );

                $dailyPoint = $workingDays > 0
                    ? (100 / $workingDays)
                    : 0;
                $attendanceStatusByDate = [];

                foreach ($attendanceRecords as $attendance) {
                    $dateKey = Carbon::parse(
                        $attendance->created_at
                    )->format('Y-m-d');
                    $officeIn = Carbon::parse(
                        $attendance->office_in_time
                    )->format('H:i:s');
                    $isHandledByWorkingHours = false;

                    if (
                        !empty($attendance->office_in_time) &&
                        !empty($attendance->office_out_time)
                    ) {
                        $officeInTime = Carbon::parse(
                            $attendance->office_in_time
                        );
                        $officeOutTime = Carbon::parse(
                            $attendance->office_out_time
                        );
                        $workedMinutes = $officeInTime->diffInMinutes($officeOutTime);
                        $requiredMinutes = $this->getRequiredMinutesForDay(
                            $dateKey,
                            $companyLeaves
                        );

                        // Case 1: Full Day Leave
                        if ($requiredMinutes == 0) {
                            if ($officeIn <= '10:00:59') {
                                $ontime++;
                                $attendanceStatusByDate[$dateKey] = 'present';
                            } elseif ($officeIn <= '10:15:59') {
                                $late++;
                                $attendanceStatusByDate[$dateKey] = 'late';
                            } else {
                                $ontime++;
                                $attendanceStatusByDate[$dateKey] = 'present';
                            }
                            $isHandledByWorkingHours = true;
                        }
                        // Case 2: Relaxation Applied (has short leave in company_leaves)
                        elseif ($requiredMinutes < 480) {
                            // First check office_in time for half day (after 12:00 PM)
                            if ($officeIn > '12:00:00') {
                                $halfDay++;
                                $attendanceStatusByDate[$dateKey] = 'half_day';
                            }
                            // Then check worked minutes
                            elseif ($workedMinutes < 240) {
                                // Less than 4 hours = Absent
                                $absent++;
                                $attendanceStatusByDate[$dateKey] = 'absent';
                            } elseif ($workedMinutes < 360) {
                                // 4-6 hours = Half Day
                                $halfDay++;
                                $attendanceStatusByDate[$dateKey] = 'half_day';
                            } elseif ($workedMinutes < $requiredMinutes) {
                                // Worked less than required minutes but more than 6 hours = Short Leave
                                $shortLeave++;
                                $attendanceStatusByDate[$dateKey] = 'short_leave';
                            } else {
                                // Worked enough to cover required minutes, check office_in time
                                if ($officeIn <= '10:00:59') {
                                    $ontime++;
                                    $attendanceStatusByDate[$dateKey] = 'present';
                                } elseif ($officeIn <= '10:15:59') {
                                    $late++;
                                    $attendanceStatusByDate[$dateKey] = 'late';
                                } else {
                                    $shortLeave++;
                                    $attendanceStatusByDate[$dateKey] = 'short_leave';
                                }
                            }
                            $isHandledByWorkingHours = true;
                        }
                        // Case 3: Normal Working Day (no leave)
                        else {
                            // First check office_in time for half day (after 12:00 PM)
                            if ($officeIn > '12:00:00') {
                                $halfDay++;
                                $attendanceStatusByDate[$dateKey] = 'half_day';
                            }
                            // Then check worked minutes
                            elseif ($workedMinutes < 240) {
                                // Less than 4 hours = Absent
                                $absent++;
                                $attendanceStatusByDate[$dateKey] = 'absent';
                            } elseif ($workedMinutes < 360) {
                                // 4-6 hours = Half Day
                                $halfDay++;
                                $attendanceStatusByDate[$dateKey] = 'half_day';
                            } elseif ($workedMinutes < 480) {
                                // 6-8 hours = Short Leave
                                $shortLeave++;
                                $attendanceStatusByDate[$dateKey] = 'short_leave';
                            } else {
                                // Worked 8+ hours, check office_in time
                                if ($officeIn <= '10:00:59') {
                                    $ontime++;
                                    $attendanceStatusByDate[$dateKey] = 'present';
                                } elseif ($officeIn <= '10:15:59') {
                                    $late++;
                                    $attendanceStatusByDate[$dateKey] = 'late';
                                } elseif ($officeIn <= '12:00:00') {
                                    $shortLeave++;
                                    $attendanceStatusByDate[$dateKey] = 'short_leave';
                                } else {
                                    $halfDay++;
                                    $attendanceStatusByDate[$dateKey] = 'half_day';
                                }
                            }
                            $isHandledByWorkingHours = true;
                        }
                    }

                    if (!$isHandledByWorkingHours) {
                        if ($officeIn <= '10:00:59') {
                            $ontime++;
                            $attendanceStatusByDate[$dateKey] = 'present';
                        } elseif ($officeIn <= '10:15:59') {
                            $late++;
                            $attendanceStatusByDate[$dateKey] = 'late';
                        } elseif ($officeIn <= '12:00:00') {
                            $shortLeave++;
                            $attendanceStatusByDate[$dateKey] = 'short_leave';
                        } else {
                            $halfDay++;
                            $attendanceStatusByDate[$dateKey] = 'half_day';
                        }
                    }
                }

                $presentDays = Attendance::where(
                    'user_id',
                    $user->id
                )
                    ->whereDate(
                        'created_at',
                        '>=',
                        $startDate->format('Y-m-d')
                    )
                    ->whereDate(
                        'created_at',
                        '<=',
                        $endDate->format('Y-m-d')
                    )
                    ->whereNotNull('office_in_time')
                    ->selectRaw('DATE(created_at)')
                    ->distinct()
                    ->count();

                $currentDate = $startDate->copy();

                while ($currentDate <= $endDate) {
                    $dateKey = $currentDate->format('Y-m-d');
                    $isWeekend = $currentDate->isWeekend();
                    $isHoliday = in_array(
                        $dateKey,
                        DB::table('holidays')
                            ->pluck('date')
                            ->map(function ($date) {
                                return Carbon::parse($date)->format('Y-m-d');
                            })
                            ->toArray()
                    );

                    if ($isWeekend) {
                        if (!isset($attendanceStatusByDate[$dateKey])) {
                            $attendanceStatusByDate[$dateKey] = 'weekend';
                        }
                        $currentDate->addDay();
                        continue;
                    }

                    if ($isHoliday) {
                        if (!isset($attendanceStatusByDate[$dateKey])) {
                            $attendanceStatusByDate[$dateKey] = 'holiday';
                        }
                        $currentDate->addDay();
                        continue;
                    }

                    if (!isset($attendanceStatusByDate[$dateKey])) {
                        $attendanceStatusByDate[$dateKey] = 'absent';
                        $absent++;
                    }

                    $currentDate->addDay();
                }

                $validStatuses = [
                    'absent',
                    'short_leave',
                    'half_day'
                ];

                $holidays = DB::table('holidays')
                    ->whereDate('date', '>=', $startDate->format('Y-m-d'))
                    ->whereDate('date', '<=', $endDate->format('Y-m-d'))
                    ->pluck('date')
                    ->map(function ($date) {
                        return Carbon::parse($date)->format('Y-m-d');
                    })
                    ->toArray();

                $sandwichDates = [];
                $sandwichDetails = [];
                $processedDates = [];

                $current = $startDate->copy();

                while ($current <= $endDate) {
                    $dateKey = $current->format('Y-m-d');

                    if (isset($processedDates[$dateKey])) {
                        $current->addDay();
                        continue;
                    }

                    $chainDates = [];
                    $hasLeaveInChain = false;
                    $hasHolidayInChain = false;
                    $hasWeekendInChain = false;
                    $leavePositions = [];
                    $gapPositions = [];

                    $temp = $current->copy();

                    while ($temp <= $endDate) {
                        $tempKey = $temp->format('Y-m-d');
                        $tempStatus = $attendanceStatusByDate[$tempKey] ?? null;
                        $isLeaveType = in_array($tempStatus, $validStatuses);
                        $isWeekend = $temp->isWeekend();
                        $isHoliday = in_array($tempKey, $holidays);

                        if ($isLeaveType || $isWeekend || $isHoliday) {
                            $chainDates[] = $tempKey;

                            if ($isLeaveType) {
                                $hasLeaveInChain = true;
                                $leavePositions[] = count($chainDates) - 1;
                            }
                            if ($isHoliday) {
                                $hasHolidayInChain = true;
                            }
                            if ($isWeekend) {
                                $hasWeekendInChain = true;
                                $gapPositions[] = count($chainDates) - 1;
                            }

                            $temp->addDay();
                            continue;
                        }

                        break;
                    }

                    $isSandwich = false;

                    if (count($chainDates) > 1 && $hasLeaveInChain && $hasHolidayInChain) {
                        $isSandwich = true;
                    }

                    if (!$isSandwich && count($chainDates) > 1 && $hasLeaveInChain && $hasWeekendInChain && !$hasHolidayInChain) {
                        $firstLeavePos = reset($leavePositions);
                        $lastLeavePos = end($leavePositions);
                        $firstGapPos = reset($gapPositions);
                        $lastGapPos = end($gapPositions);

                        if ($firstGapPos < $firstLeavePos && $lastLeavePos < $lastGapPos) {
                            $isSandwich = true;
                        } elseif ($firstLeavePos < $firstGapPos && $lastLeavePos > $lastGapPos) {
                            $isSandwich = true;
                        }
                    }

                    if ($isSandwich) {
                        sort($chainDates);
                        $sandwichDetails[] = [
                            'from' => reset($chainDates),
                            'to' => end($chainDates),
                            'total_days' => count($chainDates),
                            'dates' => $chainDates
                        ];

                        foreach ($chainDates as $d) {
                            if (isset($sandwichDates[$d])) {
                                continue;
                            }
                            $sandwichDates[$d] = true;
                            $processedDates[$d] = true;
                            $status = $attendanceStatusByDate[$d] ?? null;

                            if ($status == 'absent') {
                                $absent = max(0, $absent - 1);
                            } elseif ($status == 'short_leave') {
                                $shortLeave = max(0, $shortLeave - 1);
                            } elseif ($status == 'half_day') {
                                $halfDay = max(0, $halfDay - 1);
                            }
                        }
                        $current = $temp->copy();
                        continue;
                    }

                    $current->addDay();
                }

                $sandwich = count($sandwichDates);

                $performanceData = $this->calculatePerformance(
                    $late,
                    $shortLeave,
                    $halfDay,
                    $absent,
                    count($sandwichDetails)
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

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance graph report retrieved successfully',
                'data' => [
                    'report_data' => $allData[0] ?? null,
                    'all_data' => $allData,
                    'is_all_users' => $isAllUsers,
                    'is_admin' => $isAdmin,
                    'selected_user_id' => $selectedUserId,
                    'filter_type' => $filterType,
                    'filter_month' => $request->input('filter_month', Carbon::now()->format('Y-m')),
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'employee_selected' => $employeeSelected,
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve attendance report',
                'error' => $e->getMessage(),
            ], 500);
        }
    }















    public function attendancesSettings()
    {
        $settings = AttendanceSetting::first();

        return response()->json([

            'success' => true,

            'message' => 'Attendance settings fetched successfully',

            'data' => $settings

        ]);
    }

    public function attendanceSettingsUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [

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

        if ($validator->fails()) {

            return response()->json([

                'success' => false,

                'message' => 'Validation errors',

                'errors' => $validator->errors()

            ], 422);
        }

        $validated = $validator->validated();

        AttendanceSetting::updateOrCreate(
            ['id' => 1],
            $validated
        );

        $updatedSettings = AttendanceSetting::first();

        return response()->json([

            'success' => true,

            'message' => 'Settings Updated Successfully',

            'data' => $updatedSettings

        ]);
    }
}
