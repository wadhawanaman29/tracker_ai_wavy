<?php
use App\Models\ProjectAssignment;
use App\Models\User;
use App\Models\Deposit;
use Carbon\Carbon;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;


if (!function_exists('check_user_name')) {
    function check_user_name($user_id) {
        // Assuming you have a User model
        $user = \App\Models\User::find($user_id);
        return $user ? $user->name : 'Unknown User';
    }
}

if (!function_exists('check_user_email')) {
    function check_user_email($user_id) {
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

    function getWeekHourCount(){
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

function getMonthHourCount(){
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
    function getWeekWiseDataCount()
    {
        $UserController = App::make(UserController::class);
        $users = $UserController->getUser();
        $user_ids = array_column($users, 'id');

        $finalResult = [];

        // Get the start and end date of the current week
        $currentWeekStartDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $currentWeekEndDate = Carbon::now()->endOfWeek()->format('Y-m-d');

        foreach ($user_ids as $userVal) {
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

            $weeks = [];
            foreach ($weekWiseData as $data) {
                $yearWeek = $data->year_week;
                $dayOfWeek = $data->day_of_week;

                if (!isset($weeks[$yearWeek])) {
                    $weeks[$yearWeek] = array_fill(2, 5, 0);
                }

                $weeks[$yearWeek][$dayOfWeek] = $data->total_hours;
            }

            if (empty($weeks)) {
                $year = Carbon::now()->year;
                $week = Carbon::now()->weekOfYear;
                $weeks["$year$week"] = array_fill(2, 5, 0);
            }

            $userWeeks = [];
            foreach ($weeks as $yearWeek => $days) {
                $year = substr($yearWeek, 0, 4);
                $week = substr($yearWeek, 4, 2);

                $startOfWeek = Carbon::now()->setISODate($year, $week)->startOfWeek()->format('Y-m-d');
                // $endOfWeek = Carbon::now()->setISODate($year, $week)->endOfWeek()->format('Y-m-d');

                $endOfWeek = Carbon::now()->setISODate($year, $week)->endOfWeek();

                // Subtract 2 days from the end of the week
                $twoDaysBeforeEndOfWeek = $endOfWeek->subDays(1)->format('Y-m-d');


                $userWeeks[] = [
                    'year' => $year,
                    'week' => $week,
                    'start_of_week' => $startOfWeek,
                    'end_of_week' => $twoDaysBeforeEndOfWeek,
                    'days' => [
                        'Monday' => $days[2] ?? 0,
                        'Tuesday' => $days[3] ?? 0,
                        'Wednesday' => $days[4] ?? 0,
                        'Thursday' => $days[5] ?? 0,
                        'Friday' => $days[6] ?? 0,
                        'Saturday' => $days[7] ?? 0,
                    ],
                    'total_hours' => array_sum([
                        $days[2] ?? 0,
                        $days[3] ?? 0,
                        $days[4] ?? 0,
                        $days[5] ?? 0,
                        $days[6] ?? 0,
                    ]),
                ];
            }

            $finalResult[] = [
                'user_id' => $userVal,
                'weeks' => $userWeeks
            ];
        }

        return $finalResult;
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

    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
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



    