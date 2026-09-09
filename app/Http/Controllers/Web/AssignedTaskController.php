<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatusLog;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignedTaskController extends Controller
{
    private const STATUSES = ['pending', 'in_progress', 'on_hold', 'completed', 'cancelled'];

    // Keeps the live Kanban board's Completed/Cancelled columns from growing
    // unbounded as history piles up; the Delay Report has the full history.
    private const BOARD_DONE_LOOKBACK_DAYS = 30;
    private const BOARD_DONE_LIMIT = 50;

    /**
     * Show the create-task form (admin only).
     */
    public function assignedTask()
    {
        return view('tasks.form', [
            'task' => null,
            'projects' => Project::orderBy('project_name', 'asc')->get(),
            'employees' => $this->getEmployees(),
        ]);
    }

    /**
     * Show the edit-task form (admin only).
     */
    public function edit(Task $id)
    {
        return view('tasks.form', [
            'task' => $id,
            'projects' => Project::orderBy('project_name', 'asc')->get(),
            'employees' => $this->getEmployees(),
        ]);
    }

    /**
     * Create a task, or a subtask when parent_id is present (admin only).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:project,id',
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'parent_id' => 'nullable|exists:tasks,id',
        ]);

        $task = Task::create([
            'project_id' => $validated['project_id'],
            'parent_id' => $validated['parent_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $validated['assigned_to'],
            'assigned_by' => Auth::id(),
            'status' => 'pending',
            'due_date' => $validated['due_date'],
        ]);

        TaskStatusLog::create([
            'task_id' => $task->id,
            'from_status' => null,
            'to_status' => 'pending',
            'changed_by' => Auth::id(),
        ]);

        $this->notifyAssignee($task);

        if ($task->parent_id) {
            return redirect()->route('assigned.view', $task->parent_id)
                ->with('success', 'Subtask added successfully.');
        }

        return redirect()->route('assigned_task_list')->with('success', 'Task created successfully.');
    }

    /**
     * Update a task's details (admin only).
     */
    public function update(Request $request, Task $id)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:project,id',
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        $previousAssignee = $id->assigned_to;

        $id->update([
            'project_id' => $validated['project_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $validated['assigned_to'],
            'due_date' => $validated['due_date'],
        ]);

        if ((int) $previousAssignee !== (int) $validated['assigned_to']) {
            $this->notifyAssignee($id);
        }

        return redirect()->route('assigned.view', $id->parent_id ?: $id->id)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * The Kanban board. Admin sees every top-level task (optionally filtered),
     * an employee only sees their own.
     */
    public function assignedTaskList(Request $request)
    {
        $isAdmin = Auth::user()->user_type == '0';

        $query = Task::whereNull('parent_id')->with(['project', 'assignedTo', 'subtasks']);

        $selectedProjectId = $request->get('project_id', 'all');
        $selectedEmployeeId = $request->get('employee_id', 'all');

        if ($isAdmin) {
            if ($selectedProjectId !== 'all') {
                $query->where('project_id', $selectedProjectId);
            }
            if ($selectedEmployeeId !== 'all') {
                $query->where('assigned_to', $selectedEmployeeId);
            }
        } else {
            $query->where('assigned_to', Auth::id());
        }

        $tasks = $query->orderBy('due_date', 'asc')->get();

        // Pending/in-progress/on-hold are naturally bounded by current
        // workload, but completed/cancelled tasks accumulate forever — cap
        // the live board to a recent window so a column can't grow into
        // hundreds of stale cards (full history is in the Delay Report).
        $doneCutoff = now()->subDays(self::BOARD_DONE_LOOKBACK_DAYS);

        $tasksByStatus = [];
        $doneColumnCapped = [];
        foreach (self::STATUSES as $status) {
            $statusTasks = $tasks->where('status', $status);

            if (in_array($status, ['completed', 'cancelled'], true)) {
                $recentTasks = $statusTasks
                    ->filter(fn ($t) => $t->updated_at && $t->updated_at->gte($doneCutoff))
                    ->sortByDesc('updated_at');

                $doneColumnCapped[$status] = $recentTasks->count() > self::BOARD_DONE_LIMIT
                    || $statusTasks->count() > $recentTasks->count();

                $statusTasks = $recentTasks->take(self::BOARD_DONE_LIMIT);
            }

            $tasksByStatus[$status] = $statusTasks->values();
        }

        return view('tasks.board', [
            'isAdmin' => $isAdmin,
            'statuses' => self::STATUSES,
            'tasksByStatus' => $tasksByStatus,
            'doneColumnCapped' => $doneColumnCapped,
            'doneLookbackDays' => self::BOARD_DONE_LOOKBACK_DAYS,
            'projects' => $isAdmin ? Project::orderBy('project_name')->get() : collect(),
            'employees' => $isAdmin ? $this->getEmployees() : collect(),
            'selectedProjectId' => $selectedProjectId,
            'selectedEmployeeId' => $selectedEmployeeId,
        ]);
    }

    /**
     * Task detail: meta, subtasks, status changer, status history.
     */
    public function show(Task $id)
    {
        $isAdmin = Auth::user()->user_type == '0';

        if (! $isAdmin && (int) $id->assigned_to !== (int) Auth::id()) {
            abort(403, 'You do not have access to this task.');
        }

        $id->load(['project', 'assignedTo', 'assignedBy', 'parent', 'statusLogs.changedBy']);

        $subtasks = $id->subtasks()->with('assignedTo')->orderBy('due_date', 'asc')->get();

        return view('tasks.show', [
            'task' => $id,
            'subtasks' => $subtasks,
            'isAdmin' => $isAdmin,
            'employees' => $isAdmin ? $this->getEmployees() : collect(),
            'projects' => $isAdmin ? Project::orderBy('project_name')->get() : collect(),
            'statuses' => self::STATUSES,
        ]);
    }

    /**
     * Move a task/subtask between statuses. Called via AJAX from the Kanban
     * board (drag-and-drop) and the task detail page's status control.
     */
    public function updateStatus(Request $request, Task $id)
    {
        $isAdmin = Auth::user()->user_type == '0';

        if (! $isAdmin && (int) $id->assigned_to !== (int) Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to update this task.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', self::STATUSES),
        ]);

        $fromStatus = $id->status;
        $toStatus = $validated['status'];

        $id->status = $toStatus;
        $id->completed_at = $toStatus === 'completed' ? now() : null;
        $id->save();

        if ($fromStatus !== $toStatus) {
            TaskStatusLog::create([
                'task_id' => $id->id,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'changed_by' => Auth::id(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully.',
            'task' => [
                'id' => $id->id,
                'status' => $id->status,
                'completed_at' => $id->completed_at,
            ],
        ]);
    }

    /**
     * Delete a task (and its subtasks/status logs via cascading FKs).
     */
    public function destroy(Task $id)
    {
        $parentId = $id->parent_id;
        $id->delete();

        if ($parentId) {
            return redirect()->route('assigned.view', $parentId)->with('success', 'Subtask deleted successfully.');
        }

        return redirect()->route('assigned_task_list')->with('success', 'Task deleted successfully.');
    }

    /**
     * Admin delay/on-time report across tasks and subtasks (each classified
     * independently), for a filterable employee/project/date-range slice.
     */
    public function progress_report(Request $request)
    {
        $filterType = $request->get('filter_type', 'this_month');

        if ($filterType === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } else {
            $filterType = 'this_month';
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }

        $selectedEmployeeId = $request->get('employee_id', 'all');
        $selectedProjectId = $request->get('project_id', 'all');

        $query = Task::with(['project', 'assignedTo'])
            ->whereBetween('due_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($selectedEmployeeId !== 'all') {
            $query->where('assigned_to', $selectedEmployeeId);
        }
        if ($selectedProjectId !== 'all') {
            $query->where('project_id', $selectedProjectId);
        }

        $tasks = $query->orderBy('due_date', 'asc')->get();

        $counts = ['on_time' => 0, 'delayed' => 0, 'overdue' => 0, 'not_due_yet' => 0, 'cancelled' => 0];
        $employeeDelayCounts = [];
        $projectSummary = [];
        $rows = [];

        foreach ($tasks as $task) {
            $delayStatus = $this->classifyTaskDelay($task);
            $counts[$delayStatus]++;

            if (in_array($delayStatus, ['delayed', 'overdue'], true)) {
                $uid = $task->assigned_to;
                if (! isset($employeeDelayCounts[$uid])) {
                    $employeeDelayCounts[$uid] = [
                        'name' => optional($task->assignedTo)->name ?? 'Unknown',
                        'count' => 0,
                    ];
                }
                $employeeDelayCounts[$uid]['count']++;
            }

            $pid = $task->project_id;
            if (! isset($projectSummary[$pid])) {
                $projectSummary[$pid] = [
                    'name' => optional($task->project)->project_name ?? 'Unknown',
                    'total' => 0,
                    'pending' => 0,
                    'in_progress' => 0,
                    'on_hold' => 0,
                    'completed' => 0,
                    'cancelled' => 0,
                    'overdue' => 0,
                    'delayed' => 0,
                ];
            }
            $projectSummary[$pid]['total']++;
            $projectSummary[$pid][$task->status]++;
            if (in_array($delayStatus, ['delayed', 'overdue'], true)) {
                $projectSummary[$pid][$delayStatus]++;
            }

            $rows[] = [
                'type' => $task->parent_id ? 'Subtask' : 'Task',
                'title' => $task->title,
                'project' => optional($task->project)->project_name ?? '-',
                'assignee' => optional($task->assignedTo)->name ?? '-',
                'due_date' => optional($task->due_date)->format('d-m-Y'),
                'completed_at' => $task->completed_at ? $task->completed_at->format('d-m-Y') : '-',
                'status' => $task->status,
                'delay_status' => $delayStatus,
            ];
        }

        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('status', 'completed')->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        uasort($employeeDelayCounts, fn ($a, $b) => $b['count'] <=> $a['count']);

        foreach ($projectSummary as &$summary) {
            $summary['completion_rate'] = $summary['total'] > 0
                ? round(($summary['completed'] / $summary['total']) * 100)
                : 0;
        }
        unset($summary);
        uasort($projectSummary, fn ($a, $b) => $b['total'] <=> $a['total']);

        return view('tasks.progress_report', [
            'counts' => $counts,
            'totalTasks' => $totalTasks,
            'completionRate' => $completionRate,
            'chartLabels' => ['On Time', 'Delayed', 'Overdue', 'Not Due Yet', 'Cancelled'],
            'chartData' => [
                $counts['on_time'],
                $counts['delayed'],
                $counts['overdue'],
                $counts['not_due_yet'],
                $counts['cancelled'],
            ],
            'employeeChartLabels' => array_map(fn ($e) => $e['name'], array_values($employeeDelayCounts)),
            'employeeChartData' => array_map(fn ($e) => $e['count'], array_values($employeeDelayCounts)),
            'projectSummary' => $projectSummary,
            'rows' => $rows,
            'projects' => Project::orderBy('project_name')->get(),
            'employees' => $this->getEmployees(),
            'selectedEmployeeId' => $selectedEmployeeId,
            'selectedProjectId' => $selectedProjectId,
            'filterType' => $filterType,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]);
    }

    /**
     * Due-date based delay classification, per the confirmed policy:
     * completed after due date = delayed, still open past due date =
     * overdue, completed on/before due date = on time.
     */
    private function classifyTaskDelay(Task $task): string
    {
        if ($task->status === 'cancelled') {
            return 'cancelled';
        }

        $due = Carbon::parse($task->due_date)->endOfDay();

        if ($task->status === 'completed') {
            return $task->completed_at && $task->completed_at->lte($due) ? 'on_time' : 'delayed';
        }

        return now()->gt($due) ? 'overdue' : 'not_due_yet';
    }

    private function getEmployees()
    {
        // Matches UserController::users()' active-employee filter: exclude
        // admins, deactivated employees, and soft-deleted ones (delete_status).
        return User::where('user_type', '1')
            ->where('user_status', '!=', '0')
            ->where('delete_status', '!=', '1')
            ->orderBy('name')
            ->get();
    }

    private function notifyAssignee(Task $task): void
    {
        $assignee = User::find($task->assigned_to);

        if ($assignee) {
            $assignee->notify(new TaskAssignedNotification($task));
        }
    }
}
