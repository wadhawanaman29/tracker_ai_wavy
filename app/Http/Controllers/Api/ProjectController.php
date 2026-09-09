<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Project;
use App\Models\ProjectAssignment;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{

    public function projects()
    {
        $projects = Project::select('id', 'project_name')->get();

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }
    public function storeAssignment(Request $request)
    {
        $request->validate([
            'project' => 'required',
            'project_date' => 'required',
            'comment' => 'required',
        ]);

        $timeParts = explode(':', $request->project_time);
        $hours = $timeParts[0] ?? 0;
        $minutes = $timeParts[1] ?? 0;

        $total_minutes = ($hours * 60) + $minutes;

        $projectDateTimestamp = $request->project_date;
        $original_date = \DateTime::createFromFormat('Y-m-d', $projectDateTimestamp);
        $new_timestamp = $original_date->format('Y-m-d H:i:s');

        $assignment = ProjectAssignment::create([
            'assigned_to' => auth()->id(),
            'project' => $request->project,
            'project_time' => $total_minutes,
            'created_at' => $new_timestamp,
            'project_date' => $new_timestamp,
            'comment' => $request->comment,
        ]);

        if ($assignment) {
            return response()->json([
                'success' => true,
                'message' => 'Project assigned successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign project'
            ]);
        }
    }


    public function updateAssignment(Request $request, $id)
    {
        $request->validate([
            'project' => 'required',
            'project_date' => 'required',
            'comment' => 'required',
        ]);
        $assignment = ProjectAssignment::where('id', $id)
            ->where('assigned_to', auth()->id())
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found'
            ]);
        }

        if ($request->project_time) {
            $timeParts = explode(':', $request->project_time);
            $hours = $timeParts[0] ?? 0;
            $minutes = $timeParts[1] ?? 0;
        } else {
            $hours = $request->hours ?? 0;
            $minutes = $request->minutes ?? 0;
        }

        $total_minutes = ($hours * 60) + $minutes;

        try {
            $new_timestamp = Carbon::parse($request->project_date)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format'
            ]);
        }

        $assignment->update([
            'project' => $request->project,
            'project_date' => $new_timestamp,
            'project_time' => $total_minutes,
            'comment' => $request->comment,
            'project_description' => $request->project_description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project Updated successfully'
        ]);
    }

    public function assignmentList(Request $request)
    {
        $current_user_id = auth()->id();

        $sortColumn = $request->get('sort', 'project_assignment.created_at');
        $sortDirection = $request->get('direction', 'desc');

        $data = ProjectAssignment::select(
            'project_assignment.*',
            'project.project_name',
            'project.project_description'
        )
            ->join('project', 'project_assignment.project', '=', 'project.id')
            ->where('project_assignment.assigned_to', $current_user_id)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function deleteAssignment($id)
    {
        $assignment = ProjectAssignment::where('id', $id)
            ->where('assigned_to', auth()->id())
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found'
            ]);
        }

        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully'
        ]);
    }
    public function getAssignmentById($id)
    {
        $data = ProjectAssignment::where('id', $id)
            ->where('assigned_to', auth()->id())
            ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
