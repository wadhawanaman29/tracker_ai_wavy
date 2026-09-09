<?php

namespace App\Http\Controllers\Web;

use DateTime;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectAssignment;
use Illuminate\Support\Carbon;


class ProjectController extends Controller
{
    /*public function project(){

        $data = Project::paginate(10);
        return view('pages/project', ['data' => $data]);
    }*/


    // public function project()
    // {
    //     // Join the project_assignment table on project_id and summarize project_time
    //     $data = Project::leftJoin('project_assignment', 'project.id', '=', 'project_assignment.project')
    //         ->select('project.id', 'project.project_name', 'project.created_at', 'project.updated_at', 'project.project_status',DB::raw('COALESCE(SUM(project_assignment.project_time), 0) as total_project_time'))
    //         ->groupBy('project.id', 'project.project_name', 'project.project_status', 'project.created_at', 'project.updated_at')
    //         ->paginate(20);

    //     return view('pages/project', ['data' => $data]);
    // }

    // public function project(Request $request)
    // {
    //     // die("opop");
    //     $date = $request->get('date');

    //     $sort = $request->get('sort', 'project_name');
    //     $direction = $request->get('direction', 'asc');
    //     $search = $request->get('search');
    //     if(!empty($date)){
    //         $query->whereDate('project.project_name')
    //     }
    //     // Handle multiple sort conditions
    //     if ($sort == 'project_name') {
    //         $sort = $request->get('sort', 'project_name');
    //     } elseif ($sort == 'project_time') {
    //         $sort = DB::raw('COALESCE(SUM(project_assignment.project_time), 0)');
    //     } elseif ($sort == 'created_at') {
    //         $sort = $request->get('sort', 'created_at');
    //     } else {
    //         $sort = $request->get('sort', 'project_name');
    //     }

    //     $data = Project::leftJoin('project_assignment', 'project.id', '=', 'project_assignment.project')
    //         ->select('project.id', 'project.project_name', 'project.created_at', 'project.updated_at', 'project.project_status', DB::raw('COALESCE(SUM(project_assignment.project_time), 0) as total_project_time'))
    //         ->when(request()->has('search'), function ($q) {
    //             $search = request('search');
    //             $q->where('project.project_name', 'LIKE', "%{$search}%");
    //         })
    //         ->groupBy('project.id', 'project.project_name', 'project.project_status', 'project.created_at', 'project.updated_at')
    //         ->orderBy($sort, $direction)->paginate(15)->appends(request()->query());;

    //     if ($request->ajax()) {
    //         $html = '';
    //         $startSerialNumber = ($data->currentPage() - 1) * $data->perPage() + 1;

    //         if ($data->isEmpty()) {
    //             $html .= '<tr><td colspan="5" class="text-center">No data found</td></tr>';
    //         } else {
    //             foreach ($data as $projectdata) {
    //                 $hours = intdiv($projectdata->total_project_time, 60);
    //                 $minutes = $projectdata->total_project_time % 60;

    //                 // $html .= '<tr>
    //                 // <td>' . $startSerialNumber++ . '</td>
    //                 // <td>' . $projectdata->project_name . '</td>
    //                 // <td>' . $hours . ' Hour' . ($minutes > 0 ? ' ' . $minutes . ' Minutes' : '') . '</td>
    //                 // <td>' . date('d-m-Y', strtotime($projectdata->created_at)) . '</td>';
    //                 // $html .= '</td>
    //                 //         <td class="">
    //                 //             <a class="" href="' . route('editProject', $projectdata->id) . '"><i class="bx bx-edit-alt me-1"></i></a>
    //                 //             <a href="' . route('deleteProject', $projectdata->id) . '" class="delete-btn" data-form-id="deleteForm' . $projectdata->id . '" onclick="submitDeleteForm(' . $projectdata->id . ')">
    //                 //                 <i class="bx bx-trash me-1"></i>
    //                 //             </a>
    //                 //             <a href="' . route('view_project_detail', $projectdata->id) . '"><i class="bx bx-show me-1"></i></a>
    //                 //         </td>
    //                 //     </tr>';
    //                 $editable = \Carbon\Carbon::parse($projectdata->created_at)->gt(now()->subDays(15));
    //                 // echo'<pre';
    //                 // print_r($editable);
    //                 // die("pop");
    //                 $html .= '<tr>
    //                             <td>' . $startSerialNumber++ . '</td>
    //                             <td>' . $projectdata->project_name . '</td>
    //                             <td>' . $hours . ' Hour' . ($minutes > 0 ? ' ' . $minutes . ' Minutes' : '') . '</td>
    //                             <td>' . date('d-m-Y', strtotime($projectdata->created_at)) . '</td>
    //                             <td>';
    //                 if ($editable) {
    //                     $html .= '<a class="" href="' . route('editProject', $projectdata->id) . '">
    //                                 <i class="bx bx-edit-alt me-1"></i>
    //                             </a>';
    //                 } else {
    //                     $html .= '<span class="text-muted" title="Editing disabled for projects older than 15 days">
    //                                 <i class="bx bx-edit-alt me-1"></i>
    //                             </span>';
    //                 }

    //                 $html .= '<a href="' . route('deleteProject', $projectdata->id) . '" class="delete-btn" data-form-id="deleteForm' . $projectdata->id . '" onclick="submitDeleteForm(' . $projectdata->id . ')">
    //                             <i class="bx bx-trash me-1"></i>
    //                         </a>
    //                         <a href="' . route('view_project_detail', $projectdata->id) . '">
    //                             <i class="bx bx-show me-1"></i>
    //                         </a>
    //                         </td>
    //                     </tr>';
    //             }
    //         }

    //         return response()->json(['html' => $html, 'search' => $request->search]);
    //     } else {
    //         return view('pages/project', ['data' => $data, 'search' => $request->search]);
    //     }

    //     // return view('your_view_name', compact('data'));
    // }
    public function project(Request $request)
    {
        $date = $request->get('date');
        $sort = $request->get('sort', 'project_name');
        $direction = $request->get('direction', 'asc');
        $search = $request->get('search');
        $month = $request->get('month');
        if ($sort === 'project_time') {
            $sortColumn = DB::raw('COALESCE(SUM(project_assignment.project_time), 0)');
        } elseif (in_array($sort, ['project_name', 'created_at'])) {
            $sortColumn = $sort;
        } else {
            $sortColumn = 'project_name';
        }
        $query = Project::leftJoin('project_assignment', 'project.id', '=', 'project_assignment.project')
            ->select(
                'project.id',
                'project.project_name',
                'project.created_at',
                'project.updated_at',
                'project.project_status',
                DB::raw('COALESCE(SUM(project_assignment.project_time), 0) as total_project_time')
            )
            ->groupBy('project.id', 'project.project_name', 'project.project_status', 'project.created_at', 'project.updated_at');
        if (!empty($search)) {
            $query->where('project.project_name', 'like', "%{$search}%");
        }
        if (!empty($date)) {
            $query->whereDate('project.created_at', $date);
        } elseif (!empty($month)) {
            $query->whereYear('project_assignment.created_at', '=', \Carbon\Carbon::parse($month)->year)
                ->whereMonth('project_assignment.created_at', '=', \Carbon\Carbon::parse($month)->month);
        }

        $data = $query->orderBy($sortColumn, $direction)
            ->paginate(15)
            ->appends($request->query());
        if ($request->ajax()) {
            $html = '';
            $startSerialNumber = ($data->currentPage() - 1) * $data->perPage() + 1;
            if ($data->isEmpty()) {
                $html .= '<tr><td colspan="5" class="text-center">No data found</td></tr>';
            } else {
                foreach ($data as $projectdata) {
                    $hours = intdiv($projectdata->total_project_time, 60);
                    $minutes = $projectdata->total_project_time % 60;
                    $editable = \Carbon\Carbon::parse($projectdata->created_at)->gt(now()->subDays(15));
                    $html .= '<tr>
                    <td>' . $startSerialNumber++ . '</td>
                    <td>' . $projectdata->project_name . '</td>
                    <td>' . $hours . ' Hour' . ($minutes > 0 ? ' ' . $minutes . ' Minutes' : '') . '</td>
                    <td>' . date('d-m-Y', strtotime($projectdata->created_at)) . '</td>
                    <td>';

                    if ($editable) {
                        $html .= '<a href="' . route('editProject', $projectdata->id) . '">
                        <i class="bx bx-edit-alt me-1"></i>
                    </a>';
                    } else {
                        $html .= '<span class="text-muted" title="Editing disabled for projects older than 15 days">
                        <i class="bx bx-edit-alt me-1"></i>
                    </span>';
                    }
                    $html .= '<a href="' . route('deleteProject', $projectdata->id) . '" class="delete-btn" data-form-id="deleteForm' . $projectdata->id . '" onclick="submitDeleteForm(' . $projectdata->id . ')">
                    <i class="bx bx-trash me-1"></i>
                </a>
                <a href="' . route('view_project_detail', $projectdata->id) . '">
                    <i class="bx bx-show me-1"></i>
                </a>
                </td></tr>';
                }
            }
            return response()->json(['html' => $html, 'search' => $search]);
        }
        return view('pages.project', ['data' => $data, 'search' => $search, 'date' => $date, 'month' => $month]);
    }

    public function addProject()
    {
        return view('pages/add_project');
    }

    public function storeProject(Request $request)
    {
        $request->validate([
            'project_name' => 'required',
            'project_description' => 'required',
        ]);

        $Project = Project::create([
            'project_name' => $request->project_name,
            'project_description' => $request->project_description,
        ]);
        if ($Project) {
            // return redirect()->route('project')->with('success', 'Project added successfully.');  
            return redirect()->back()->with('success', 'Project added successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to create Project.');
        }
    }



    // public function editProject(Project $id)
    // {
    //     // print_r($id);
    //     return view('pages/add_project', compact('id'));
    // }

    public function editProject(Project $id)
    {
        if (Carbon::parse($id->created_at)->lt(now()->subDays(15))) {
            return redirect()->route('project')->with('error', 'You cannot edit projects older than 15 days.');
        }
        return view('pages/add_project', compact('id'));
    }


    public function updateProject(Request $request, Project $id)
    {
        $request->validate([
            'project_name' => 'required',
            'project_description' => 'required',
        ]);


        $updateProject = $id->update([
            'project_name' => $request->project_name,
            'project_description' => $request->project_description,

        ]);


        if ($updateProject) {
            return redirect()->route('project')->with('success', 'Project has been updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update project.');
        }
    }



    public function deleteProject(Project $id)
    {
        $id->delete();
        return redirect()->route('project')
            ->with('success', 'Project deleted successfully');
    }


    // public function assignmentList(){
    //     $current_user_id = auth()->id();

    //     $data = ProjectAssignment::select('*')->where('assigned_to', $current_user_id)->paginate(10);
    //     return view('pages/project', ['data' => $data]);
    // }

    // public function assignmentList() {
    //     $current_user_id = auth()->id();

    //     // Select fields from both ProjectAssignment and projects tables
    //     $data = ProjectAssignment::select(
    //         'project_assignment.*', 
    //         'project.project_name', 
    //         'project.project_description' // Add more fields as needed
    //     )
    //     ->join('project', 'project_assignment.project', '=', 'project.id')
    //     ->where('project_assignment.assigned_to', $current_user_id)
    //     ->orderBy('id', 'desc')
    //     ->paginate(20);

    //     return view('pages/assignment_list', ['data' => $data]);
    // }

    public function assignmentList(Request $request)
    {
        $current_user_id = auth()->id();
        $search = $request->get('search');
        $date = $request->get('date');
        $allowedSortColumns = ['project_name', 'project_date'];
        $sortColumn = in_array($request->get('sort'), $allowedSortColumns) ? $request->get('sort') : 'project_name';
        $sortDirection = $request->get('direction') === 'desc' ? 'desc' : 'asc';
        $query = ProjectAssignment::select(
            'project_assignment.*',
            'project.project_name',
            'project.project_description'
        )
            ->join('project', 'project_assignment.project', '=', 'project.id')
            ->where('project_assignment.assigned_to', $current_user_id);
        if (!empty($search)) {
            $query->where('project.project_name', 'like', '%' . $search . '%');
        }
        if (!empty($date)) {
            $query->whereDate('project_assignment.project_date', $date);
        }
        $data = $query->orderBy($sortColumn, $sortDirection)
            ->paginate(20)
            ->appends([
                'sort' => $sortColumn,
                'direction' => $sortDirection,
                'search' => $search,
                'date' => $date,

            ]);
        // $sortColumn = $request->get('sort', 'project_name');
        // $sortDirection = $request->get('direction', 'asc');
        //old
        // $data = ProjectAssignment::select(
        //     'project_assignment.*',
        //     'project.project_name',
        //     'project.project_description'
        // )
        //     ->join('project', 'project_assignment.project', '=', 'project.id')
        //     ->where('project_assignment.assigned_to', $current_user_id)
        //     // ->orderBy('project_assignment.id', 'desc')
        //     ->orderBy($sortColumn, $sortDirection)
        //     ->paginate(20);
        // echo '<pre>';
        // print_r($data->toArray());
        // die("oioio");



        //new


        if ($request->ajax()) {
            $html = '';
            $startSerialNumber = ($data->currentPage() - 1) * $data->perPage() + 1;

            if ($data->isEmpty()) {
                $html .= '<tr><td colspan="5" class="text-center">No data found</td></tr>';
            } else {
                foreach ($data as $assignment) {
                    $hours = intdiv($assignment->project_time, 60);
                    $minutes = $assignment->project_time % 60;
                    $formattedTime = sprintf('%d:%02d', $hours, $minutes);

                    // $html .= '<tr>
                    //             <td>' . $startSerialNumber++ . '</td>
                    //             <td>' . $assignment->project_name . '</td>
                    //             <td>' . date('d.m.Y', strtotime($assignment->project_date)) . '</td>
                    //             <td>' . $formattedTime . ' Hour</td>
                    //             <td>
                    //                 <a href="' . route('editAssignment', $assignment->id) . '">
                    //                     <i class="bx bx-edit-alt me-1"></i>
                    //                 </a>
                    //                 <a href="' . route('deleteAssignment', $assignment->id) . '" 
                    //                     class="delete-btn" 
                    //                     data-form-id="deleteForm' . $assignment->id . '" 
                    //                     onclick="submitDeleteForm(' . $assignment->id . ')">
                    //                     <i class="bx bx-trash me-1"></i>
                    //                 </a>

                    //             </td>
                    //         </tr>';
                    $editable = \Carbon\Carbon::parse($assignment->project_date)->gt(now()->subDays(15));

                    $html .= '<tr>
            <td>' . $startSerialNumber++ . '</td>
            <td>' . $assignment->project_name . '</td>
            <td>' . date('d.m.Y', strtotime($assignment->project_date)) . '</td>
            <td>' . $formattedTime . ' Hour</td>
            <td>';

                    if ($editable) {
                        $html .= '<a href="' . route('editAssignment', $assignment->id) . '">
                <i class="bx bx-edit-alt me-1"></i>
              </a>';
                    } else {
                        $html .= '<span class="text-muted" title="Editing disabled after 15 days">
                <i class="bx bx-edit-alt me-1"></i>
              </span>';
                    }

                    // Show delete button regardless (you can add similar check if needed)
                    $html .= '<a href="' . route('deleteAssignment', $assignment->id) . '" 
            class="delete-btn" 
            data-form-id="deleteForm' . $assignment->id . '" 
            onclick="submitDeleteForm(' . $assignment->id . ')">
            <i class="bx bx-trash me-1"></i>
         </a>';

                    $html .= '</td></tr>';
                }
            }

            return response()->json(['html' => $html]);
        }

        return view('pages.assignment_list', ['data' => $data, 'search' => $search, 'date' => $date,]);
    }



    public function addAssignment()
    {
        $project = Project::orderBy('project_name', 'asc')->get();
        return view('pages/add_assignment', ['projects' => $project]);
    }

    public function storeAssignment(Request $request)
    {
        // echo $request->hours.''.$request->minutes;
        // // print_r(auth()->id());
        // dd($request->all());
        // die;
        $request->validate([
            'project' => 'required',
            // 'project_time' => 'required',
            'project_date' => 'required',
            'comment' => 'required',

        ]);
        // Calculate total minutes
        $total_minutes = ($request->hours * 60) + $request->minutes;

        // $minutes = $request->minutes ?? '00';

        // $assignment = ProjectAssignment::create([
        //     'assigned_to' => 
        //     'project' => $request->project,
        //     'project_time' => $request->project_time,
        //     'comment' => $request->comment,
        //     'project_description' => $request->project_description,              
        // ]);
        $projectDateTimestamp = $request->project_date;
        $original_date = DateTime::createFromFormat('Y-m-d', $projectDateTimestamp);
        $new_timestamp = $original_date->format('Y-m-d H:i:s');
        $assignment = ProjectAssignment::create([
            'assigned_to' => auth()->id(),
            'project' => $request->project,
            // 'project_time' => $request->hours.':'.$request->minutes,
            'project_time' => $total_minutes, // Format hours and minutes
            'created_at' => $new_timestamp,
            'project_date' => $new_timestamp,
            'comment' => $request->comment,
            'project_description' => $request->project_description,
        ]);

        if ($assignment) {
            return redirect()->route('assignment_list')->with('success', 'Project assigned successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to create assign.');
        }
    }

    // public function assignmentList() {
    //     $current_user_id = auth()->id();

    //     // Fetch the total assignment hours for the current user
    //     $totalHours = ProjectAssignment::where('assigned_to', $current_user_id)->sum('project_time');

    //     // Fetch the assignments with project details
    //     $data = ProjectAssignment::select(
    //         'project_assignments.*', 
    //         'projects.project_name', 
    //         'projects.project_description'
    //     )
    //     ->join('projects', 'project_assignments.project_id', '=', 'projects.id')
    //     ->where('project_assignments.assigned_to', $current_user_id)
    //     ->paginate(10);

    //     // Pass the total hours and assignments data to the view
    //     return view('pages.project', ['data' => $data, 'totalHours' => $totalHours]);
    // }

    public function reportData(Request $request)
    {
        $data = ProjectAssignment::select(
            'project_assignment.*',
            'project.project_name',
            'project.project_description' // Add more fields as needed
        )
            ->join('project', 'project_assignment.project', '=', 'project.id')

            ->paginate(20);

        return view('pages/hour_report', ['data' => $data]);
    }

    public function listReport(Request $request)
    {
        $query = ProjectAssignment::select(
            'project_assignment.*',
            'project.project_name',
            'project.project_description'
        )
            ->join('project', 'project_assignment.project', '=', 'project.id');

        // Check if any filters are applied
        $hasFilters = $request->hasAny(['user_id', 'project', 'date_filter', 'month']);

        if ($hasFilters) {
            if ($request->user_id && $request->user_id !== 'all') {
                $query->where('assigned_to', $request->user_id);
            }

            if ($request->project && $request->project !== 'all') {
                $query->where('project.id', $request->project);
            }

            if ($request->date_filter) {
                switch ($request->date_filter) {
                    case 'today':
                        $query->whereDate('project_assignment.project_date', now()->toDateString());
                        break;
                    case 'yesterday':
                        $query->whereDate('project_assignment.project_date', now()->subDay()->toDateString());
                        break;
                    case 'this_week':
                        $query->whereBetween('project_assignment.project_date', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'last_week':
                        $query->whereBetween('project_assignment.project_date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                        break;
                    case 'this_month':
                        $query->whereMonth('project_assignment.project_date', now()->month);
                        break;
                    case 'last_month':
                        $query->whereMonth('project_assignment.project_date', now()->subMonth()->month);
                        break;
                    case 'this_year':
                        $query->whereYear('project_assignment.project_date', now()->year);
                        break;
                    case 'last_year':
                        $query->whereYear('project_assignment.project_date', now()->subYear()->year);
                        break;
                    case 'date':
                        if ($request->month) {
                            $query->whereDate('project_assignment.project_date', $request->month);
                        }
                        break;
                }
            }
        } else {
            $query->whereDate('project_assignment.project_date', now()->toDateString());
        }

        $data = $query->orderBy('project_assignment.project_date', 'desc')->paginate(20);

        $project = Project::orderBy('project_name', 'asc')->get();

        return view('pages.list_report', [
            'data' => $data,
            'project' => $project,
            'dateFilter' => $request->date_filter,
        ]);
    }




    // public function generate(Request $request)
    // {
    //     // Get selected user_id and month-year from request
    //     $selectedUserId = $request->input('user_id');
    //     $userName = check_user_name($selectedUserId);

    //     $selectedMonthYear = $request->input('month'); // Example: "2024-07-01"

    //     // Fetch users (adjust as per your User model)
    //     $users = User::all();

    //     // Query to fetch data based on filters
    //     $data = ProjectAssignment::select(
    //         'project_assignment.*', 
    //         'project.project_name', 
    //         'project.project_description' // Add more fields as needed
    //     )
    //     ->join('project', 'project_assignment.project', '=', 'project.id')
    //     ->where('project_assignment.assigned_to', $selectedUserId)
    //     ->where('project_assignment.created_at', 'like', $selectedMonthYear.'%') // Filtering by year
    //     ->paginate(10);

    //     // Pass data to your blade view
    //     return view('pages.list_report', [
    //         'users' => $users,
    //         'data' => $data,
    //         'selectedUserName' => $userName,
    //         'selectedUserId' => $selectedUserId,

    //     ]);
    // }report

    public function report(Request $request)
    {
        $UserId = $request->user_id;
        $userName = check_user_name($UserId); // Assuming check_user_name retrieves the user's name
        $data = ProjectAssignment::select(
            'project_assignment.*',
            'project.project_name',
            'project.project_description' // Add more fields as needed
        )
            ->join('project', 'project_assignment.project', '=', 'project.id')
            ->where('project_assignment.assigned_to', $UserId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pages.list_report', [
            'selectedUserName' => $userName,
            'data' => $data,
        ]);
    }

    //     public function generate(Request $request)
    // {
    //     // Get selected user_id and month-year from request
    //     $selectedUserId = $request->input('user_id');
    //     $userName = check_user_name($selectedUserId); // Assuming check_user_name retrieves the user's name

    //     $selectedMonthYear = $request->input('month'); // Example: "2024-07-01"

    //     // Fetch all users (assuming User model is used)
    //     $users = User::all();

    //     if ($selectedUserId != "all") {
    //         $data = ProjectAssignment::select(
    //             'project_assignment.*', 
    //             'project.project_name', 
    //             'project.project_description' // Add more fields as needed
    //         )
    //         ->join('project', 'project_assignment.project', '=', 'project.id')
    //         ->where('project_assignment.assigned_to', $selectedUserId)
    //         ->where('project_assignment.created_at', 'like', $selectedMonthYear.'%') // Filtering by year
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(20);
    //     } else {
    //         $data = ProjectAssignment::select(
    //             'project_assignment.*', 
    //             'project.project_name', 
    //             'project.project_description' // Add more fields as needed
    //         )
    //         ->join('project', 'project_assignment.project', '=', 'project.id')
    //         ->where('project_assignment.created_at', 'like', $selectedMonthYear.'%') // Filtering by year
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(20);
    //     }



    //     // Pass data to your blade view
    //     return view('pages.list_report', [
    //         'users' => $users,
    //         'data' => $data,
    //         'selectedUserName' => $userName,
    //         'selectedUserId' => $selectedUserId,
    //         'selectedMonthYear' => $selectedMonthYear,
    //     ]);
    // }

    public function generate(Request $request)
    {
        return view('pages.list_report', ['data' => $data]);
    }


    // public function employee(Request $request): Response  
    // {
    //     $query = ProjectAssignment::query();
    //     $dateFilter = $request->date_filter;
    //     $selectedUserId = $request->input('user_id');
    //     $selectedMonthYear = $request->input('month');

    //     // Apply date filters based on the provided filter
    //     switch($dateFilter){
    //         case 'date':
    //             $query->where('project_assignment.created_at', 'like', $selectedMonthYear.'%'); // Filtering by date
    //             break;
    //         case 'today':
    //             $query->whereDate('project_assignment.created_at', Carbon::today());
    //             break;
    //         case 'yesterday':
    //             $query->whereDate('project_assignment.created_at', Carbon::yesterday());
    //             break;
    //         case 'this_week':
    //             $query->whereBetween('project_assignment.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    //             break;
    //         case 'last_week':
    //             $query->whereBetween('project_assignment.created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);
    //             break;
    //         case 'this_month':
    //             $query->whereMonth('project_assignment.created_at', Carbon::now()->month)
    //                 ->whereYear('project_assignment.created_at', Carbon::now()->year);
    //             break;
    //         case 'last_month':
    //             $query->whereMonth('project_assignment.created_at', Carbon::now()->subMonth()->month)
    //                 ->whereYear('project_assignment.created_at', Carbon::now()->subMonth()->year);
    //             break;
    //         case 'this_year':
    //             $query->whereYear('project_assignment.created_at', Carbon::now()->year);
    //             break;
    //         case 'last_year':
    //             $query->whereYear('project_assignment.created_at', Carbon::now()->subYear()->year);
    //             break;
    //     }

    //     $query->orderBy('created_at', 'asc');

    //     // Apply user ID filter if not "all"
    //     if ($selectedUserId != "all") {
    //         $query->select('project_assignment.*', 'project.project_name', 'project.project_description')
    //             ->join('project', 'project_assignment.project', '=', 'project.id')
    //             ->where('project_assignment.assigned_to', $selectedUserId);
    //     }else{
    //         $query->select('project_assignment.*', 'project.project_name', 'project.project_description')
    //         ->join('project', 'project_assignment.project', '=', 'project.id');
    //     }

    //     // Pagination
    //     $data = $query->paginate(10);

    //     // Return the partial view containing only the table HTML
    //     return response()->view('pages.employee_table', compact('data', 'dateFilter'));
    // }

    // public function employee(Request $request): JsonResponse
    //    {
    //         // Retrieve filter data from the request
    //         $dateFilter = $request->input('date_filter'); // Ensure the correct input name
    //         $userId = $request->input('user_id');
    //         $month = $request->input('month');

    //         // Fetch data based on filters
    //         $query = ProjectAssignment::query();

    //         // Apply date filters based on the provided filter
    //         switch ($dateFilter) {
    //             case 'date':
    //                 $query->whereDate('project_assignment.created_at', $month);
    //                 break;
    //             case 'today':
    //                 $query->whereDate('project_assignment.created_at', today());
    //                 break;
    //             case 'yesterday':
    //                 $query->whereDate('project_assignment.created_at', Carbon::yesterday());

    //                 break;
    //             case 'this_week':
    //                 $query->whereBetween('project_assignment.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    //                 break;
    //             case 'last_week':
    //                 $query->whereBetween('project_assignment.created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
    //                 break;
    //             case 'this_month':
    //                 $query->whereMonth('project_assignment.created_at', now()->month)
    //                     ->whereYear('project_assignment.created_at', now()->year);
    //                 break;
    //             case 'last_month':
    //                 $query->whereMonth('project_assignment.created_at', now()->subMonth()->month)
    //                     ->whereYear('project_assignment.created_at', now()->subMonth()->year);
    //                 break;
    //             case 'this_year':
    //                 $query->whereYear('project_assignment.created_at', now()->year);
    //                 break;
    //             case 'last_year':
    //                 $query->whereYear('project_assignment.created_at', now()->subYear()->year);
    //                 break;
    //             default:
    //                 // Optionally handle cases where no date filter is applied
    //                 break;
    //         }

    //         if ($userId != "all") {
    //             $query->select('project_assignment.*', 'project.project_name', 'project.project_description')
    //                 ->join('project', 'project_assignment.project', '=', 'project.id')
    //                 ->where('project_assignment.assigned_to', $userId);
    //         }else{
    //             $query->select('project_assignment.*', 'project.project_name', 'project.project_description')
    //             ->join('project', 'project_assignment.project', '=', 'project.id');
    //         }


    //         $sort = $request->get('sort', ''); // Default to 'project_name' if not provided
    //         $direction = $request->get('direction', ''); // Default to 'asc' if not provided

    //         $query->orderBy('project_assignment.created_at', 'asc');

    //         // Paginate results
    //        // $query->orderBy("project_assignment.$sort", $direction);

    //         $data = $query->paginate(15);

    //         // Generate HTML for the table rows
    //         $html = '';
    //         $startSerialNumber = ($data->currentPage() - 1) * $data->perPage() + 1;

    //         if ($data->isEmpty()) {
    //             $html .= '<tr><td colspan="7" class="text-center">No records found</td></tr>';
    //         } else {
    //             foreach ($data as $index => $record) {
    //                 $hours = intdiv($record->project_time, 60);
    //                 $minutes = $record->project_time % 60;
    //                 $formattedDate = date('d/m/Y', strtotime($record->project_date));
    //                 $formattedTime = sprintf('%d:%02d', $hours, $minutes);
    //                 $escapedComment = htmlspecialchars($record->comment, ENT_QUOTES, 'UTF-8');
    //                 $strippedComment = str_replace('&nbsp;', ' ', strip_tags($record->comment));

    //                 $html .= '<tr>
    //                     <td>' . $startSerialNumber++ . '</td>
    //                     <td>' . check_user_name($record->assigned_to) . '</td>
    //                     <td>' . $record->project_name . '</td>
    //                     <td>' . $formattedDate . '</td>
    //                     <td>' . $formattedTime . '</td>
    //                     <td>' . $record->comment . '</td>
    //                     <td>
    //                         <span class="view-reason" 
    //                             data-reason="' . $escapedComment . '"
    //                             data-reason="' . $strippedComment . '"
    //                             data-toggle="modal"
    //                             data-target="#exampleModalCenter"
    //                             style="font-size: 14px; cursor: pointer;">View</span>
    //                     </td>
    //                 </tr>';
    //             }
    //         }
    //        // $pagination = $data->appends($request->except('page'))->links()->render();
    //         $pagination = $data->appends($request->except('page'))->appends(['sort' => $sort, 'direction' => $direction])->links()->render();


    //         // Return JSON response with HTML
    //         return response()->json([
    //             'html' => $html,
    //             'datefilter' => $dateFilter,
    //             'pagination' => $pagination, // Return the pagination links here
    //            // 'page' => $pagination, 
    //             'user_id' => $userId,
    //             'month' => $month
    //         ]);
    //    }


    //   public function employeedata(Request $request): JsonResponse  
    //     {       
    //         $sort = $request->get('sort', ''); // Default to 'project_name' if not provided
    //         $direction = $request->get('direction', ''); // Default to 'asc' if not provided
    //         $userId = $request->get('id', ''); // Default to 'all' if not provided
    //         $dateFilter = $request->get('filter', ''); // Default to empty string if not provided
    //         $month = $request->get('month', ''); // Default to empty string if not provided

    //           // Handle multiple sort conditions
    //           if ($sort == 'project_name') {
    //               $sort = $request->get('sort', 'project_name');
    //           } elseif ($sort == 'project_time') {
    //             $sort = $request->get('sort', 'project_time');
    //           } elseif ($sort == 'created_at') {
    //               $sort = $request->get('sort', 'created_at');
    //           } else {
    //               $sort = $request->get('sort', 'project_name');
    //           }

    //         $query = ProjectAssignment::query();

    //           // Apply date filters based on the provided filter
    //           switch ($dateFilter) {
    //               case 'date':
    //                   $query->whereDate('project_assignment.created_at', $month);
    //                   break;
    //               case 'today':
    //                   $query->whereDate('project_assignment.created_at', today());
    //                   break;
    //               case 'yesterday':
    //                   $query->whereDate('project_assignment.created_at', Carbon::yesterday());
    //                   break;
    //               case 'this_week':
    //                   $query->whereBetween('project_assignment.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    //                   break;
    //               case 'last_week':
    //                   $query->whereBetween('project_assignment.created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
    //                   break;
    //               case 'this_month':
    //                   $query->whereMonth('project_assignment.created_at', now()->month)
    //                       ->whereYear('project_assignment.created_at', now()->year);
    //                   break;
    //               case 'last_month':
    //                   $query->whereMonth('project_assignment.created_at', now()->subMonth()->month)
    //                       ->whereYear('project_assignment.created_at', now()->subMonth()->year);
    //                   break;
    //               case 'this_year':
    //                   $query->whereYear('project_assignment.created_at', now()->year);
    //                   break;
    //               case 'last_year':
    //                   $query->whereYear('project_assignment.created_at', now()->subYear()->year);
    //                   break;
    //               default:
    //                   // Optionally handle cases where no date filter is applied
    //                   break;
    //           }

    //           if ($userId != "all") {
    //             $query->select('project_assignment.*', 'project.project_name', 'project.project_description')
    //                 ->join('project', 'project_assignment.project', '=', 'project.id')
    //                 ->where('project_assignment.assigned_to', $userId);
    //             }else{
    //                 $query->select('project_assignment.*', 'project.project_name', 'project.project_description')
    //                 ->join('project', 'project_assignment.project', '=', 'project.id');
    //             }

    //           // Paginate results         
    //           $data = $query->orderBy($sort, $direction)->paginate(15); 
    //           // Generate HTML for the table rows
    //           $html = '';
    //           $startSerialNumber = ($data->currentPage() - 1) * $data->perPage() + 1;

    //           if ($data->isEmpty()) {
    //               $html .= '<tr><td colspan="7" class="text-center">No records found</td></tr>';
    //           } else {
    //               foreach ($data as $index => $record) {
    //                   $hours = intdiv($record->project_time, 60);
    //                   $minutes = $record->project_time % 60;
    //                   $formattedDate = date('d/m/Y', strtotime($record->project_date));
    //                   $formattedTime = sprintf('%d:%02d', $hours, $minutes);
    //                   $escapedComment = htmlspecialchars($record->comment, ENT_QUOTES, 'UTF-8');
    //                   $strippedComment = str_replace('&nbsp;', ' ', strip_tags($record->comment));

    //                   $html .= '<tr>
    //                       <td>' . $startSerialNumber++ . '</td>
    //                       <td>' . check_user_name($record->assigned_to) . '</td>
    //                       <td>' . $record->project_name . '</td>
    //                       <td>' . $formattedDate . '</td>
    //                       <td>' . $formattedTime . '</td>
    //                       <td>' . $record->comment . '</td>
    //                       <td>
    //                           <span class="view-reason" 
    //                               data-reason="' . $escapedComment . '"
    //                               data-reason="' . $strippedComment . '"
    //                               data-toggle="modal"
    //                               data-target="#exampleModalCenter"
    //                               style="font-size: 14px; cursor: pointer;">View</span>
    //                       </td>
    //                   </tr>';

    //               }
    //           }

    //          // $pagination = $data->appends($request->except('page'))->appends(['sort' => $sort, 'direction' => $direction])->links()->render();


    //           $pagination = $data->appends($request->except('page'))->links()->render();

    //           // Return JSON response with HTML
    //           return response()->json([
    //               'html' => $html,
    //               'datefilter' => $dateFilter,
    //               'pagination' => $pagination, // Return the pagination links here
    //               'user_id' => $userId,
    //               'month' => $month
    //           ]);
    //     }


    public function employee(Request $request): JsonResponse
    {
        // Retrieve filter data from the request
        $dateFilter = $request->input('date_filter', '');
        $userId = $request->input('user_id', '');
        $month = $request->input('month', '');

        // Base query
        $query = $this->baseQuery($dateFilter, $month, $userId);

        // Retrieve sorting parameters from the request
        $sort = $request->input('sort', '');
        $direction = $request->input('direction', '');

        // Validate the sort column and direction
        //   $validSortColumns = ['project_assignment.created_at', 'project.project_name', 'project_assignment.project_time'];
        //   if (!in_array($sort, $validSortColumns)) {
        //       $sort = 'project_assignment.created_at'; 
        //   }
        //   if (!in_array($direction, ['asc', 'desc'])) {
        //       $direction = 'asc'; 
        //   }

        // Apply sorting
        $query->orderBy($sort, $direction);


        // Default sorting
        // $query->orderBy('project_assignment.created_at', 'asc');

        // Paginate results
        $data = $query->paginate(15);

        // Generate HTML for the table rows
        $html = $this->generateTableRows($data);

        // Generate pagination
        $pagination = $data->appends($request->except('page'))->links()->render();

        // Return JSON response
        return response()->json([
            'html' => $html,
            'datefilter' => $dateFilter,
            'pagination' => $pagination,
            'user_id' => $userId,
            'month' => $month,
            'sort' => $sort,
            'direction' => $direction

        ]);
    }


    public function employeedata(Request $request): JsonResponse
    {
        $sort = $request->get('sort', '');
        $direction = $request->get('direction', '');
        $userId = $request->get('id', 'all');
        $dateFilter = $request->get('filter', '');
        $month = $request->get('month', '');

        // Base query
        $query = $this->baseQuery($dateFilter, $month, $userId);

        // Apply sorting
        $query->orderBy($sort, $direction);

        // Paginate results
        $data = $query->paginate(15);

        // Generate HTML for the table rows
        $html = $this->generateTableRows($data);

        // Generate pagination
        $pagination = $data->appends($request->except('page'))
            ->appends(['sort' => $sort, 'direction' => $direction])
            ->links()->render();

        // Return JSON response
        return response()->json([
            'html' => $html,
            'datefilter' => $dateFilter,
            'pagination' => $pagination,
            'user_id' => $userId,
            'month' => $month
        ]);
    }


    private function applyDateFilter($query, $dateFilter, $month)
    {
        switch ($dateFilter) {
            case 'date':
                $query->whereDate('project_assignment.created_at', $month);
                break;
            case 'today':
                $query->whereDate('project_assignment.created_at', today());
                break;
            case 'yesterday':
                $query->whereDate('project_assignment.created_at', Carbon::yesterday());
                break;
            case 'this_week':
                $query->whereBetween('project_assignment.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'last_week':
                $query->whereBetween('project_assignment.created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('project_assignment.created_at', now()->month)
                    ->whereYear('project_assignment.created_at', now()->year);
                break;
            case 'last_month':
                $query->whereMonth('project_assignment.created_at', now()->subMonth()->month)
                    ->whereYear('project_assignment.created_at', now()->subMonth()->year);
                break;
            case 'this_year':
                $query->whereYear('project_assignment.created_at', now()->year);
                break;
            case 'last_year':
                $query->whereYear('project_assignment.created_at', now()->subYear()->year);
                break;
            default:
                // Optionally handle cases where no date filter is applied
                break;
        }
    }

    private function baseQuery($dateFilter, $month, $userId)
    {
        $query = ProjectAssignment::query()
            ->select('project_assignment.*', 'project.project_name', 'project.project_description')
            ->join('project', 'project_assignment.project', '=', 'project.id');

        // Apply date filters
        $this->applyDateFilter($query, $dateFilter, $month);

        // Apply user filter if not "all"
        if ($userId != "all") {
            $query->where('project_assignment.assigned_to', $userId);
        }

        return $query;
    }

    private function generateTableRows($data)
    {
        $html = '';
        $startSerialNumber = ($data->currentPage() - 1) * $data->perPage() + 1;

        if ($data->isEmpty()) {
            $html .= '<tr><td colspan="7" class="text-center">No records found</td></tr>';
        } else {
            foreach ($data as $index => $record) {
                $hours = intdiv($record->project_time, 60);
                $minutes = $record->project_time % 60;
                $formattedDate = date('d/m/Y', strtotime($record->project_date));
                $formattedTime = sprintf('%d:%02d', $hours, $minutes);
                $escapedComment = htmlspecialchars($record->comment, ENT_QUOTES, 'UTF-8');
                $strippedComment = str_replace('&nbsp;', ' ', strip_tags($record->comment));

                $html .= '<tr>
                <td>' . $startSerialNumber++ . '</td>
                <td>' . check_user_name($record->assigned_to) . '</td>
                <td>' . $record->project_name . '</td>
                <td>' . $formattedDate . '</td>
                <td>' . $formattedTime . '</td>
                <td>' . $record->comment . '</td>
                <td>
                    <span class="view-reason" 
                        data-reason="' . $escapedComment . '"
                        data-reason="' . $strippedComment . '"
                        data-toggle="modal"
                        data-target="#exampleModalCenter"
                        style="font-size: 14px; cursor: pointer;">View</span>
                </td>
            </tr>';
            }
        }

        return $html;
    }



    public function view_user_report()
    {
        $users = User::select(
            'users.id',
            'users.name',
            'users.email',
            'users.user_type',
            'users.user_status',
            'users.created_at',

        )
            ->where('users.user_type', '!=', '0')
            ->paginate(20);
        foreach ($users as $project) {
            $user_id = $project->id; // Assuming `id` is the user ID, adjust it accordingly if it's not.
            $monthHourCounts = ProjectAssignment::select(DB::raw('MONTH(project_time) as month_number'), DB::raw('SUM(project_time) as total_hours'))
                ->where('assigned_to', $user_id)
                ->groupBy(DB::raw('MONTH(project_time)'))
                ->get()
                ->toArray(); // Convert collection to array directly
            $project->monthHourCounts = $monthHourCounts; // Assigning $monthHourCounts to the project
        }

        return view('pages/view_user_report', ['data' => $users]);
    }

    public function view_project_report()
    {

        $currentDate = now()->toDateString(); // Get the current date in the format YYYY-MM-DD
        $data = ProjectAssignment::whereDate('updated_at', $currentDate)->paginate(20);

        foreach ($data as $project) {
            $id = $project->project; // Assuming `id` is the user ID, adjust it accordingly if it's not.
            $name = Project::select()
                ->where('id', $id)
                ->get()
                ->toArray(); // Convert collection to array directly
            $project->name = $name; // Assigning $monthHourCounts to the project
        }
        //  echo "<pre>";
        //  print_r($data);
        //  echo "<pre>";
        return view('pages/view_project_report', ['data' => $data]);
    }

    public function viewuser($id)
    {
        $user = User::where('id', $id)->first(); // Fetch user by ID
        $user_id = $id; // Assuming `id` is the user ID, adjust it accordingly if it's not.
        $HourCounts = ProjectAssignment::select(DB::raw('MONTH(project_time) as month_number'), DB::raw('SUM(project_time) as total_hours'))
            ->where('assigned_to', $user_id)
            ->groupBy(DB::raw('MONTH(project_time)'))
            ->get()
            ->toArray(); // Convert collection to array directly
        return view('pages.viewuser', ['data' => $user, 'HourCounts' => $HourCounts]);
    }



    // public function reportData(Request $request){



    //     $assignments = ProjectAssignment::all(); // Fetch all assignments


    //     //dd($request);

    //     if ($request->ajax()) {
    //         $filterValue = $request->filterValue;

    //         // Apply filtering logic based on $filterValue
    //         // Example: Filter assignments based on time
    //         if ($filterValue === 'days') {
    //             $assignments = $assignments->where('project_time', '<=', 24);
    //         } elseif ($filterValue === 'week') {
    //             $assignments = $assignments->where('project_time', '<=', 168);
    //         } elseif ($filterValue === 'month') {
    //             $assignments = $assignments->where('project_time', '<=', 720);
    //         } elseif ($filterValue === '6_months') {
    //             $assignments = $assignments->where('project_time', '<=', 4320);
    //         } elseif ($filterValue === 'year') {
    //             $assignments = $assignments->where('project_time', '<=', 8760);
    //         }

    //         // You can apply more filtering logic here based on your requirements

    //         return response()->json(['assignments' => $assignments]);
    //     }

    //     return view('pages/hour_report', compact('assignments'));
    // }



    public function deleteAssignment(ProjectAssignment $id)
    {
        $id->delete();
        return redirect()->route('assignment_list')
            ->with('success', 'Project deleted successfully');
    }
    public function editAssignment(ProjectAssignment $id)
    {
        $assignmentDate = Carbon::parse($id->project_date);

        if ($assignmentDate->lt(now()->subDays(15))) {
            return redirect()->route('assignment_list')->with('error', 'You cannot edit records older than 15 days.');
        }
        $projects = Project::get();
        $customColumnValue = $id->project;
        $hours = intdiv($id->project_time, 60);
        $minutes = $id->project_time % 60;
        // echo'<pre>';
        // print_r($hours);
        // print_r($minutes);
        // die("opo");
        return view('pages.add_assignment', compact('projects', 'id', 'customColumnValue', 'hours', 'minutes'))->with('project_data', $customColumnValue);
    }

    // public function editAssignment(ProjectAssignment $id)
    // {
    //     $assignmentDate = Carbon::parse($id->project_date);

    //     if ($assignmentDate->lt(now()->subDays(15))) {
    //         return redirect()->route('assignment_list')->with('error', 'You cannot edit records older than 15 days.');
    //     }

    //     $projects = Project::get();
    //     $customColumnValue = $id->project;
    //     // echo'<pre>';
    //     // print_r($projects->toArray());
    //     // die('oo');

    //     return view('pages.add_assignment', compact('projects', 'id'), ['project_data' => $customColumnValue]);
    // }


    public function updateAssignment(Request $request, ProjectAssignment $id)
    {
        $request->validate([
            'project' => 'required',
            // 'project_time' => 'required',
            'project_date' => 'required',
            'comment' => 'required',
        ]);
        // Calculate total minutes
        $total_minutes = ($request->hours * 60) + $request->minutes;
        $projectDateTimestamp = $request->project_date;
        $original_date = DateTime::createFromFormat('Y-m-d', $projectDateTimestamp);
        $new_timestamp = $original_date->format('Y-m-d H:i:s');

        $updateAssignment = $id->update([
            'assigned_to' => auth()->id(),
            'project' => $request->project,
            'project_date' => $new_timestamp,
            // 'project_time' => $request->project_time,
            'project_time' => $total_minutes, // Format hours and minutes
            'created_at' => $new_timestamp,
            'comment' => $request->comment,
            'project_description' => $request->project_description,
        ]);
        if ($updateAssignment) {
            return redirect()->route('assignment_list')->with('success', 'Assignment has been updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update Assignment.');
        }
    }


    public function viewProjectDetails($id)
    {
        // Fetch project details with total project time
        $data = Project::leftJoin('project_assignment', 'project.id', '=', 'project_assignment.project')
            ->select(
                'project.id',
                'project.project_name',
                'project.created_at',
                'project.updated_at',
                'project.project_status',
                DB::raw('COALESCE(SUM(project_assignment.project_time), 0) as total_project_time')
            )
            ->where('project.id', '=', $id)
            ->groupBy('project.id', 'project.project_name', 'project.project_status', 'project.created_at', 'project.updated_at')
            ->paginate(20);

        // Fetch employees and their hours worked on this project
        $employees = ProjectAssignment::where('project', $id)
            ->join('users', 'project_assignment.assigned_to', '=', 'users.id')
            ->select('project_assignment.id as assignment_id', 'users.name as employee_name', 'project_assignment.project_time', 'project_assignment.comment as employee_comment', 'project_assignment.project_date as task_date')
            ->orderby('project_assignment.project_date', 'desc')
            ->get();

        return view('pages.project_details', ['data' => $data, 'employees' => $employees]);
    }

    /*public function viewEmployeeFullReport($id)
    {
        // Fetch the assignment using the provided ID
        $assignment = ProjectAssignment::find($id);

        // Check if the assignment exists
        if (!$assignment) {
            return redirect()->back()->with('error', 'Assignment not found.');
        }

        // Fetch the employee details using the employee_id from the assignment
        $employee = User::find($assignment->assigned_to);

        // Check if the employee exists
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        // Fetch all projects and assignments related to the employee
        $projects = ProjectAssignment::where('assigned_to', $employee->id)
                    ->join('project', 'project_assignment.project', '=', 'project.id')
                    ->select('project.*', 'project_assignment.project_time', 'project_assignment.comment')
                    ->get();

        // Pass the employee details and related project to the view
        return view('pages.viewuser', [
            'employee' => $employee,
            'projects' => $projects
        ]);
    }*/

    // public function viewEmployeeFullReport($id)
    // {
    //     // Fetch the assignment using the provided ID
    //     $assignment = ProjectAssignment::find($id);

    //     // Check if the assignment exists
    //     if (!$assignment) {
    //         return redirect()->back()->with('error', 'Assignment not found.');
    //     }

    //     // Fetch the employee details using the employee_id from the assignment
    //     $employee = User::find($assignment->assigned_to);

    //     // Check if the employee exists
    //     if (!$employee) {
    //         return redirect()->back()->with('error', 'Employee not found.');
    //     }

    //     // Fetch the most recent project assignment for the employee
    //     $latestProjectAssignment = ProjectAssignment::where('assigned_to', $employee->id)
    //         ->orderBy('created_at', 'desc')
    //         ->first();

    //     if (!$latestProjectAssignment) {
    //         return redirect()->back()->with('error', 'No assignments found for this employee.');
    //     }

    //     // Fetch the project details for the latest assignment
    //     $project = DB::table('project')
    //         ->join('project_assignment', 'project.id', '=', 'project_assignment.project')
    //         ->where('project_assignment.id', $latestProjectAssignment->id)
    //         ->select('project.*', 'project_assignment.project_time', 'project_assignment.comment')
    //         ->first();

    //     // Pass the employee details and related project to the view
    //     return view('pages.viewuser', [
    //         'employee' => $employee,
    //         'project' => $project
    //     ]);
    // }

    // public function viewEmployeeFullReport($id)
    // {
    //     // Fetch the assignment using the provided ID
    //     $assignment = ProjectAssignment::find($id);
    //     // dd($assignment);
    //     // die();

    //     $project = DB::table('project')
    //     ->join('project_assignment', 'project.id', '=', 'project_assignment.project')
    //     ->where('project_assignment.id', $assignment->id)
    //     ->select('project.*', 'project_assignment.project_time', 'project_assignment.comment')
    //     ->first();

    //     // dd($project);
    //     // die();

    //     // Check if the assignment exists
    //     if (!$assignment) {
    //         return redirect()->back()->with('error', 'Assignment not found.');
    //     }

    //     // Fetch the employee details using the employee_id from the assignment
    //     $employee = User::find($assignment->assigned_to);

    //     // Check if the employee exists
    //     if (!$employee) {
    //         return redirect()->back()->with('error', 'Employee not found.');
    //     }

    //     // Fetch the most recent project assignment for the employee
    //     $latestProjectAssignment = ProjectAssignment::where('assigned_to', $employee->id)
    //         ->orderBy('created_at', 'desc')
    //         ->first();

    //     if (!$latestProjectAssignment) {
    //         return redirect()->back()->with('error', 'No assignments found for this employee.');
    //     }

    //     // Fetch the project details for the latest assignment
    //     $project = DB::table('project')
    //         ->join('project_assignment', 'project.id', '=', 'project_assignment.project')
    //         ->where('project_assignment.id', $latestProjectAssignment->id)
    //         ->select('project.*', 'project_assignment.project_time', 'project_assignment.comment')
    //         ->first();

    //     // Pass the employee details and related project to the view
    //     return view('pages.viewuser', [
    //         'employee' => $employee,
    //         'project' => $project,
    //         'assignment' => $assignment
    //     ]);
    // }

    public function viewEmployeeFullReport($id)
    {
        // Fetch the assignment using the provided ID
        $assignment = ProjectAssignment::find($id);
        // dd($assignment);
        // die();

        $project = DB::table('project')
            ->join('project_assignment', 'project.id', '=', 'project_assignment.project')
            ->where('project_assignment.id', $id) // Assuming 'id' is the primary key of 'project_assignment' table
            ->select('project.*') // Select fields from 'project' table
            ->first();

        // dd($project);
        // die();

        // Check if the assignment exists
        if (!$assignment) {
            return redirect()->back()->with('error', 'Assignment not found.');
        }

        // Fetch the employee details using the employee_id from the assignment
        $employee = User::find($assignment->assigned_to);

        // Check if the employee exists
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        // Fetch the most recent project assignment for the employee
        $latestProjectAssignment = ProjectAssignment::where('assigned_to', $employee->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestProjectAssignment) {
            return redirect()->back()->with('error', 'No assignments found for this employee.');
        }

        // Fetch the project details for the latest assignment
        // $project = DB::table('project')
        //     ->join('project_assignment', 'project.id', '=', 'project_assignment.project')
        //     ->where('project_assignment.id', $latestProjectAssignment->id)
        //     ->select('project.*', 'project_assignment.project_time', 'project_assignment.comment')
        //     ->first();

        // Pass the employee details and related project to the view
        return view('pages.viewuser', [
            'employee' => $employee,
            'project' => $project,
            'assignment' => $assignment
        ]);
    }
}
