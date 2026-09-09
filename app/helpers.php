<?php

use App\Models\ProjectAssignment;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;


if (!function_exists('check_user_name')) {
    function check_user_name($user_id)
    {
        // Assuming you have a User model
        $user = \App\Models\User::find($user_id);
        return $user ? $user->name : 'Unknown User';
    }
}


if (!function_exists('check_user_email')) {
    function check_user_email($user_id)
    {
        // Assuming you have a User model
        $user = \App\Models\User::find($user_id);
        return $user ? $user->email : 'Unknown User';
    }
}


/*  */

/*
function getWeekHourCount(){
    $user_id = auth()->id();
    $weekHourCounts = ProjectAssignment::select(DB::raw('WEEK(project_time) as week_number'), DB::raw('SUM(project_time) as total_hours'))
        ->where('assigned_to', $user_id)
        ->groupBy(DB::raw('WEEK(project_time)'))
        ->get();

   // dd($weekHourCounts->toArray()); // Add this line to debug

    return $weekHourCounts->toArray();
}
    */

function getWeekHourCount()
{
    $user_id = auth()->id();

    // Get the start and end dates for the current week
    $startOfWeek = now()->startOfWeek();
    $endOfWeek = now()->endOfWeek();

    $weekHourCounts = ProjectAssignment::select(DB::raw('WEEK(project_date) as week_number'), DB::raw('SUM(project_time) as total_hours'))
        ->where('assigned_to', $user_id)
        ->whereBetween('project_date', [$startOfWeek, $endOfWeek])
        ->groupBy(DB::raw('WEEK(project_date)'))
        ->get();

    // dd($weekHourCounts->toArray()); // Uncomment this line to debug

    return $weekHourCounts->toArray();
}


/*
function getMonthHourCount(){
    $user_id = auth()->id();
    $monthHourCounts = ProjectAssignment::select(DB::raw('MONTH(project_time) as month_number'), DB::raw('SUM(project_time) as total_hours'))
        ->where('assigned_to', $user_id)
        ->groupBy(DB::raw('MONTH(project_time)'))
        ->get();

    return $monthHourCounts->toArray();
}
*/

function getMonthHourCount()
{
    $user_id = auth()->id();

    // Get the start and end dates for the current month
    $startOfMonth = now()->startOfMonth();
    $endOfMonth = now()->endOfMonth();

    $monthHourCounts = ProjectAssignment::select(DB::raw('MONTH(project_date) as month_number'), DB::raw('SUM(project_time) as total_hours'))
        ->where('assigned_to', $user_id)
        ->whereBetween('project_date', [$startOfMonth, $endOfMonth])
        ->groupBy(DB::raw('MONTH(project_date)'))
        ->get();

    return $monthHourCounts->toArray();
}


/* ========== on dashboard employee's hours count function ========= */

// if (!function_exists('getWeekWiseDataCount')) {
//     function getWeekWiseDataCount($selectedDate = null)
//     {
//         $UserController = App::make(UserController::class);
//         $users = $UserController->getUser();
//         $user_ids = array_map(function ($user) {
//             return $user['id'];
//         }, array_filter($users, function ($user) {
//             return isset($user['user_status']) && $user['user_status'] == 1;
//         }));
//         $finalResult = [];
//         if ($selectedDate) {
//             $startDate = Carbon::parse($selectedDate); // Convert selected date to a Carbon instance
//             $currentWeekStartDate = $startDate->startOfWeek()->format('Y-m-d');
//             $currentWeekEndDate = $startDate->endOfWeek()->format('Y-m-d');
//         } else {
//             $startDate = Carbon::now();
//             $currentWeekStartDate = $startDate->startOfWeek()->format('Y-m-d');
//             $currentWeekEndDate = $startDate->endOfWeek()->format('Y-m-d');
//         }
//         // Always get previous full week (Monday to Sunday)
//         // $startDate = Carbon::now()->subWeek();
//         // $currentWeekStartDate = $startDate->startOfWeek()->format('Y-m-d');
//         // $currentWeekEndDate = $startDate->endOfWeek()->format('Y-m-d');
//         // echo "<pre>";
//         //         print_r($currentWeekStartDate);
//         //         print_r($currentWeekEndDate);
//         //         echo "</pre>";
//         //         die("opo");
//         // echo $currentWeekStartDate;
//         // echo "<br>";
//         // echo $currentWeekEndDate;

//         foreach ($user_ids as $userVal) {

//             $username = check_user_name($userVal);
//             $weekWiseData = ProjectAssignment::select(
//                 DB::raw('YEARWEEK(created_at, 1) as year_week'),
//                 DB::raw('DAYOFWEEK(created_at) as day_of_week'),
//                 DB::raw('SUM(project_time) as total_hours')
//             )
//                 ->where('assigned_to', $userVal)
//                 ->whereBetween('created_at', [$currentWeekStartDate, $currentWeekEndDate])
//                 ->whereBetween(DB::raw('DAYOFWEEK(created_at)'), [2, 7])
//                 ->groupBy(DB::raw('YEARWEEK(created_at, 1)'), DB::raw('DAYOFWEEK(created_at)'))
//                 ->orderBy(DB::raw('YEARWEEK(created_at, 1)'), 'asc')
//                 ->orderBy(DB::raw('DAYOFWEEK(created_at)'), 'asc')
//                 ->get();

//             $weeks = [];

//             foreach ($weekWiseData as $data) {
//                 $yearWeek = $data->year_week;
//                 $dayOfWeek = $data->day_of_week;

//                 if (!isset($weeks[$yearWeek])) {
//                     $weeks[$yearWeek] = array_fill(2, 5, 0);
//                 }

//                 $weeks[$yearWeek][$dayOfWeek] = $data->total_hours;
//             }

//             if (empty($weeks)) {
//                 $year = Carbon::now()->year;
//                 $week = Carbon::now()->weekOfYear;
//                 $weeks["$year$week"] = array_fill(2, 5, 0);
//             }
//             $userWeeks = [];
//             // echo "<pre>";
//             // print_r($weeks);
//             // echo "</pre>";
//             // die("dfgdfgfdg");
//             foreach ($weeks as $yearWeek => $days) {
//                 $year = substr($yearWeek, 0, 4);
//                 $week = substr($yearWeek, 4, 2);

//                 $startOfWeek = Carbon::now()->setISODate($year, $week)->startOfWeek()->format('Y-m-d');
//                 // $endOfWeek = Carbon::now()->setISODate($year, $week)->endOfWeek()->format('Y-m-d');


//                 $endOfWeek = Carbon::now()->setISODate($year, $week)->endOfWeek();

//                 // Subtract 2 days from the end of the week
//                 $twoDaysBeforeEndOfWeek = $endOfWeek->subDays(1)->format('Y-m-d');


//                 $userWeeks[] = [
//                     'year' => $year,
//                     'week' => $week,
//                     'start_of_week' => $startOfWeek,
//                     'end_of_week' => $twoDaysBeforeEndOfWeek,
//                     'days' => [
//                         'Monday' => $days[2] ?? 0,
//                         'Tuesday' => $days[3] ?? 0,
//                         'Wednesday' => $days[4] ?? 0,
//                         'Thursday' => $days[5] ?? 0,
//                         'Friday' => $days[6] ?? 0,
//                         'Saturday' => $days[7] ?? 0,
//                     ],

//                     'total_hours' => array_sum([
//                         $days[2] ?? 0,
//                         $days[3] ?? 0,
//                         $days[4] ?? 0,
//                         $days[5] ?? 0,
//                         $days[6] ?? 0,
//                         $days[7] ?? 0,
//                     ]),

//                 ];
//                 // echo "<pre>";
//                 // print_r($userWeeks);
//                 // echo "</pre>";
//                 // die("opo");
//             }

//             $finalResult[] = [
//                 'user_name' => $username,
//                 'user_id' => $userVal,
//                 'weeks' => $userWeeks
//             ];
//         }

//         return $finalResult;
//     }
// }

if (!function_exists('getWeekWiseDataCount')) {
    function getWeekWiseDataCount($selectedDate = null)
    {
        $UserController = App::make(UserController::class);
        $users = $UserController->getUser();

        $user_ids = array_map(function ($user) {
            return $user['id'];
        }, array_filter($users, function ($user) {
            return isset($user['user_status']) && $user['user_status'] == 1;
        }));

        $finalResult = [];

        if ($selectedDate) {
            $startDate = Carbon::parse($selectedDate);
            $currentWeekStartDate = $startDate->startOfWeek()->format('Y-m-d');
            $currentWeekEndDate = $startDate->endOfWeek()->format('Y-m-d');
        } else {
            $startDate = Carbon::now();
            $currentWeekStartDate = $startDate->startOfWeek()->format('Y-m-d');
            $currentWeekEndDate = $startDate->endOfWeek()->format('Y-m-d');
        }
        foreach ($user_ids as $userVal) {
            $username = check_user_name($userVal);
            $weekWiseData = ProjectAssignment::select(
                DB::raw('YEARWEEK(created_at, 1) as year_week'),
                DB::raw('DAYOFWEEK(created_at) as day_of_week'),
                DB::raw('SUM(project_time) as total_hours')
            )
                ->where('assigned_to', $userVal)
                ->whereBetween('created_at', [$currentWeekStartDate, $currentWeekEndDate])
                ->whereBetween(DB::raw('DAYOFWEEK(created_at)'), [2, 7])
                ->groupBy(DB::raw('YEARWEEK(created_at, 1)'), DB::raw('DAYOFWEEK(created_at)'))
                ->orderBy(DB::raw('YEARWEEK(created_at, 1)'), 'asc')
                ->orderBy(DB::raw('DAYOFWEEK(created_at)'), 'asc')
                ->get();
            //     echo'<pre>';
            //    print_r($weekWiseData->toArray());
            //    die('ooooo');
            $weeks = [];
            foreach ($weekWiseData as $data) {

                $yearWeek = $data->year_week;
                $dayOfWeek = $data->day_of_week;

                if (!isset($weeks[$yearWeek])) {
                    $weeks[$yearWeek] = array_fill(2, 6, 0);
                }

                $weeks[$yearWeek][$dayOfWeek] = $data->total_hours;
            }

            if (empty($weeks)) {
                $year = Carbon::now()->year;
                $week = Carbon::now()->weekOfYear;
                $weeks["$year$week"] = array_fill(2, 6, 0);
            }

            $attendanceData = [];
            $startOfWeekCarbon = Carbon::parse($currentWeekStartDate);
            $endOfWeekCarbon = Carbon::parse($currentWeekEndDate);


            for ($date = $startOfWeekCarbon; $date->lte($endOfWeekCarbon); $date->addDay()) {
                $dayOfWeek = $date->dayOfWeekIso;

                if ($dayOfWeek >= 1 && $dayOfWeek <= 6) {
                    $attendance = Attendance::where('user_id', $userVal)
                        ->whereDate('created_at', $date->format('Y-m-d'))
                        ->first();

                    if ($attendance && $attendance->office_in_time && $attendance->office_out_time) {
                        $officeIn = Carbon::parse($attendance->office_in_time);
                        $officeOut = Carbon::parse($attendance->office_out_time);
                        $workMinutes = $officeOut->diffInMinutes($officeIn);
                        if ($attendance->lunch_in_time && $attendance->lunch_out_time) {
                            $lunchIn = Carbon::parse($attendance->lunch_in_time);
                            $lunchOut = Carbon::parse($attendance->lunch_out_time);
                            $workMinutes -= $lunchOut->diffInMinutes($lunchIn);
                        }

                        // $attendanceData[$dayOfWeek] = round($workMinutes / 60, 2);
                        $attendanceData[$dayOfWeek] = $workMinutes;
                    } else {
                        $attendanceData[$dayOfWeek] = 0;
                    }
                }
            }


            $userWeeks = [];

            //  echo'<pre>';
            //  print_r($attendanceData);
            //  die('oooxxxxoo');
            foreach ($weeks as $yearWeek => $days) {
                $year = substr($yearWeek, 0, 4);
                $week = substr($yearWeek, 4, 2);

                $startOfWeek = Carbon::now()->setISODate($year, $week)->startOfWeek()->format('Y-m-d');
                $endOfWeek = Carbon::now()->setISODate($year, $week)->endOfWeek()->format('Y-m-d');

                for ($d = 2; $d <= 7; $d++) {
                    if (!isset($attendanceData[$d])) {
                        $attendanceData[$d] = 0;
                    }
                }

                $userWeeks[] = [
                    'year' => $year,
                    'week' => $week,
                    'start_of_week' => $startOfWeek,
                    'end_of_week' => Carbon::parse($endOfWeek)->subDays(1)->format('Y-m-d'), // Saturday

                    'days_project' => [
                        'Monday' => $days[2] ?? 0,
                        'Tuesday' => $days[3] ?? 0,
                        'Wednesday' => $days[4] ?? 0,
                        'Thursday' => $days[5] ?? 0,
                        'Friday' => $days[6] ?? 0,
                        'Saturday' => $days[7] ?? 0,
                    ],

                    'days_attendance' => [
                        'Monday' => $attendanceData[1] ?? 0,
                        'Tuesday' => $attendanceData[2] ?? 0,
                        'Wednesday' => $attendanceData[3] ?? 0,
                        'Thursday' => $attendanceData[4] ?? 0,
                        'Friday' => $attendanceData[5] ?? 0,
                        'Saturday' => $attendanceData[6] ?? 0,
                    ],

                    'total_hours' => array_sum([
                        $days[2] ?? 0,
                        $days[3] ?? 0,
                        $days[4] ?? 0,
                        $days[5] ?? 0,
                        $days[6] ?? 0,
                        $days[7] ?? 0,
                    ]),

                    'total_hours_attendance' => array_sum([
                        $attendanceData[1] ?? 0,
                        $attendanceData[2] ?? 0,
                        $attendanceData[3] ?? 0,
                        $attendanceData[4] ?? 0,
                        $attendanceData[5] ?? 0,
                        $attendanceData[6] ?? 0,
                    ]),
                ];
            }
            // echo '<pre>';
            // print_r($userWeeks);
            // die("opo");
            $finalResult[] = [
                'user_name' => $username,
                'user_id' => $userVal,
                'weeks' => $userWeeks,
            ];
        }

        return $finalResult;
    }
}


/* ========= All employees designation ========= */
// function getDesignations()
// {
//     return [
//         'Bidder',
//         'Designer',
//         'Developer',           
//         'Digital Marketing',
//         'Tester',
//     ];
// }

function getDesignations()
{
    return [
        'Bidder' => 'Bidder',
        'Designer' => 'Designer',
        'Developer' => 'Developer',
        'Digital Marketing' => 'Digital Marketing',
        'Tester' => 'Tester',
    ];
}





/* === Get Current device ip ======= */
function getPublicIp()
{
    $response = Http::get('https://api.ipify.org?format=json');
    if ($response->successful()) {
        return $response->json()['ip'];
    }
    return null; // or handle the error as needed
}

function getUsers()
{
    $users = User::select(
        '*'
    )
        ->where('users.user_type', '!=', '0')->get();

    return $users;
}

function get_client_ip()
{
    // Client-supplied headers (X-Forwarded-For, Client-IP, etc.) are spoofable
    // and can arrive as a comma-separated proxy chain when a VPN is involved,
    // which corrupted the stored ip value. request()->ip() honors the app's
    // TrustProxies config (no trusted proxies here) and falls back to the
    // real REMOTE_ADDR, so it always yields a single clean address.
    return request()->ip() ?: 'UNKNOWN';
}

function notification()
{
    $currentDate = Carbon::now()->toDateString(); // Get current date
    $notifications = Deposit::whereDate('startdate', '<=', $currentDate)
        ->whereDate('enddate', '>=', $currentDate)
        ->get();

    // print_r($notifications);
    // die();
    return $notifications;
}
