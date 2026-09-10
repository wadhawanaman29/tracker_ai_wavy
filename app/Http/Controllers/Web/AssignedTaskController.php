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
    private const STATUSES = ['pending', 'in_progress', 'on_hold', 'testing', 'completed', 'cancelled'];
    private const BOARD_DONE_LOOKBACK_DAYS = 30;
    private const BOARD_DONE_LIMIT = 50;
    public function assignedTask()
    {
        return view('tasks.form', [
            'task' => null,
            'projects' => Project::orderBy('project_name', 'asc')->get(),
            'employees' => $this->getEmployees(),
        ]);
    }


    public function edit(Task $id)
    {
        return view('tasks.form', [
            'task' => $id,
            'projects' => Project::orderBy('project_name', 'asc')->get(),
            'employees' => $this->getEmployees(),
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:project,id',
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        $task = Task::create([
            'project_id' => $validated['project_id'],
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

        return redirect()->route('assigned_task_list')->with('success', 'Task created successfully.');
    }


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

        return redirect()->route('assigned.view', $id->id)
            ->with('success', 'Task updated successfully.');
    }


    public function assignedTaskList(Request $request)
    {
        $isAdmin = Auth::user()->user_type == '0';

        $query = Task::with(['project', 'assignedTo']);

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

        $doneCutoff = now()->subDays(self::BOARD_DONE_LOOKBACK_DAYS);

        $tasksByStatus = [];
        $doneColumnCapped = [];
        foreach (self::STATUSES as $status) {
            $statusTasks = $tasks->where('status', $status);

            if (in_array($status, ['completed', 'cancelled'], true)) {
                $recentTasks = $statusTasks
                    ->filter(fn($t) => $t->updated_at && $t->updated_at->gte($doneCutoff))
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

    public function show(Task $id)
    {
        $isAdmin = Auth::user()->user_type == '0';

        if (! $isAdmin && (int) $id->assigned_to !== (int) Auth::id()) {
            abort(403, 'You do not have access to this task.');
        }

        $id->load(['project', 'assignedTo', 'assignedBy', 'statusLogs.changedBy']);

        $hasEverBeenCompleted = $this->hasEverBeenCompleted($id);

        return view('tasks.show', [
            'task' => $id,
            'isAdmin' => $isAdmin,
            'statuses' => self::STATUSES,
            'canEditStatus' => $isAdmin || ! $hasEverBeenCompleted,
            'editableStatuses' => $id->status === 'completed' ? ['completed', 'testing'] : self::STATUSES,
        ]);
    }


    private function hasEverBeenCompleted(Task $task): bool
    {
        return $task->status === 'completed'
            || TaskStatusLog::where('task_id', $task->id)->where('to_status', 'completed')->exists();
    }

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

        if ($this->hasEverBeenCompleted($id) && ! $isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Only an admin can change the status of a task that has already been completed.',
            ], 403);
        }

        if ($fromStatus === 'completed' && $toStatus !== 'completed' && $toStatus !== 'testing') {
            return response()->json([
                'success' => false,
                'message' => 'A completed task can only be moved to Testing.',
            ], 422);
        }

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


    public function destroy(Task $id)
    {
        $id->delete();

        return redirect()->route('assigned_task_list')->with('success', 'Task deleted successfully.');
    }


    public function progress_report(Request $request)
    {
        [$query, $filterType, $startDate, $endDate, $selectedEmployeeId, $selectedProjectId] = $this->progressReportQuery($request);

        $tasks = $query->orderBy('due_date', 'asc')->get();
        $report = $this->summarizeTasks($tasks);

        return view('tasks.progress_report', array_merge($report, [
            'projects' => Project::orderBy('project_name')->get(),
            'employees' => $this->getEmployees(),
            'selectedEmployeeId' => $selectedEmployeeId,
            'selectedProjectId' => $selectedProjectId,
            'filterType' => $filterType,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]));
    }

    public function progressReportTasksAjax(Request $request)
    {
        [$query] = $this->progressReportQuery($request);

        $tasks = $query->orderBy('due_date', 'asc')->get();

        $search  = strtolower(trim($request->get('search', '')));
        $status  = $request->get('delay_status', 'all');
        $sortBy  = $request->get('sort_by');
        $sortDir = $request->get('sort_dir') === 'desc' ? 'desc' : 'asc';
        $page    = max(1, (int) $request->get('page', 1));
        $perPage = 5;

        $rows = $tasks->map(function ($task) {
            return [
                'title'        => $task->title,
                'project'      => optional($task->project)->project_name ?? '-',
                'assignee'     => optional($task->assignedTo)->name ?? '-',
                'due_date'     => optional($task->due_date)->format('d-m-Y'),
                'due_sort'     => optional($task->due_date)->format('Y-m-d'),
                'completed_at' => $task->completed_at ? $task->completed_at->format('d-m-Y') : '-',
                'status'       => $task->status,
                'status_label' => ucfirst(str_replace('_', ' ', $task->status)),
                'delay_status' => $this->classifyTaskDelay($task),
            ];
        });

        if ($status !== 'all') {
            $rows = $rows->where('delay_status', $status);
        }

        if ($search !== '') {
            $rows = $rows->filter(
                fn($row) =>
                str_contains(strtolower($row['title']), $search) ||
                    str_contains(strtolower($row['project']), $search) ||
                    str_contains(strtolower($row['assignee']), $search)
            );
        }

        $sortMap = ['title' => 'title', 'project' => 'project', 'assignee' => 'assignee', 'due' => 'due_sort', 'delay' => 'delay_status'];
        if ($sortBy && isset($sortMap[$sortBy])) {
            $rows = $sortDir === 'desc' ? $rows->sortByDesc($sortMap[$sortBy]) : $rows->sortBy($sortMap[$sortBy]);
        }

        $rows     = $rows->values();
        $total    = $rows->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page     = min($page, $lastPage);

        return response()->json([
            'data'         => $rows->forPage($page, $perPage)->values(),
            'current_page' => $page,
            'last_page'    => $lastPage,
            'total'        => $total,
            'per_page'     => $perPage,
        ]);
    }

    public function employeeReport(Request $request)
    {
        [$filterType, $startDate, $endDate] = $this->resolveDateRange($request);

        $tasks = Task::whereBetween('due_date', [$startDate->toDateString(), $endDate->toDateString()])->get();

        $summaries = $this->getEmployees()->map(function ($employee) use ($tasks) {
            $employeeTasks = $tasks->where('assigned_to', $employee->id);
            $total = $employeeTasks->count();
            $completed = $employeeTasks->where('status', 'completed')->count();
            $delayed = 0;
            $overdue = 0;

            foreach ($employeeTasks as $task) {
                $delayStatus = $this->classifyTaskDelay($task);
                if ($delayStatus === 'delayed') {
                    $delayed++;
                } elseif ($delayStatus === 'overdue') {
                    $overdue++;
                }
            }

            return [
                'employee' => $employee,
                'total' => $total,
                'completed' => $completed,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100) : 0,
                'delayed' => $delayed,
                'overdue' => $overdue,
            ];
        });

        return view('tasks.employee_report', [
            'summaries' => $summaries,
            'filterType' => $filterType,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]);
    }


    public function employeeReportShow(Request $request, User $user)
    {
        [$filterType, $startDate, $endDate] = $this->resolveDateRange($request);

        $tasks = Task::with('project')
            ->where('assigned_to', $user->id)
            ->whereBetween('due_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('due_date', 'asc')
            ->get();

        $report = $this->summarizeTasks($tasks);

        return view('tasks.employee_report_show', array_merge($report, [
            'employee' => $user,
            'filterType' => $filterType,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]));
    }


    public function myReport(Request $request)
    {
        $user = Auth::user();
        [$filterType, $startDate, $endDate] = $this->resolveDateRange($request);

        $tasks = Task::with('project')
            ->where('assigned_to', $user->id)
            ->whereBetween('due_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('due_date', 'asc')
            ->get();

        $report = $this->summarizeTasks($tasks);

        return view('tasks.my_report', array_merge($report, [
            'employee' => $user,
            'filterType' => $filterType,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]));
    }


    private function resolveDateRange(Request $request): array
    {
        $filterType = $request->get('filter_type', 'this_month');

        switch ($filterType) {
            case 'last_month':
                $startDate = now()->subMonthNoOverflow()->startOfMonth();
                $endDate = now()->subMonthNoOverflow()->endOfMonth();
                break;
            case 'this_year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
            case 'last_year':
                $startDate = now()->subYear()->startOfYear();
                $endDate = now()->subYear()->endOfYear();
                break;
            case 'custom':
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                    break;
                }
                $filterType = 'this_month';
                // fall through to the default range below
            case 'this_month':
            default:
                $filterType = 'this_month';
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
        }

        return [$filterType, $startDate, $endDate];
    }

    private function summarizeTasks($tasks): array
    {
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
                    'testing' => 0,
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

        uasort($employeeDelayCounts, fn($a, $b) => $b['count'] <=> $a['count']);

        foreach ($projectSummary as &$summary) {
            $summary['completion_rate'] = $summary['total'] > 0
                ? round(($summary['completed'] / $summary['total']) * 100)
                : 0;
        }
        unset($summary);
        uasort($projectSummary, fn($a, $b) => $b['total'] <=> $a['total']);

        return [
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
            'employeeChartLabels' => array_map(fn($e) => $e['name'], array_values($employeeDelayCounts)),
            'employeeChartData' => array_map(fn($e) => $e['count'], array_values($employeeDelayCounts)),
            'projectSummary' => $projectSummary,
            'rows' => $rows,
        ];
    }


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


    private function progressReportQuery(Request $request)
    {
        [$filterType, $startDate, $endDate] = $this->resolveDateRange($request);

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

        return [$query, $filterType, $startDate, $endDate, $selectedEmployeeId, $selectedProjectId];
    }

    public function progressReportProjectsAjax(Request $request)
    {
        [$query] = $this->progressReportQuery($request);

        $tasks = $query->orderBy('due_date', 'asc')->get();
        $report = $this->summarizeTasks($tasks);

        $search  = strtolower(trim($request->get('search', '')));
        $page    = max(1, (int) $request->get('page', 1));
        $perPage = 5;

        $projects = collect($report['projectSummary'])->values();

        if ($search !== '') {
            $projects = $projects->filter(
                fn($p) =>
                str_contains(strtolower($p['name']), $search)
            );
        }

        $projects = $projects->values();
        $total    = $projects->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page     = min($page, $lastPage);

        return response()->json([
            'data'         => $projects->forPage($page, $perPage)->values(),
            'current_page' => $page,
            'last_page'    => $lastPage,
            'total'        => $total,
            'per_page'     => $perPage,
        ]);
    }


    public function myReportTasksAjax(Request $request)
    {
        $user = Auth::user();
        [$filterType, $startDate, $endDate] = $this->resolveDateRange($request);

        $tasks = Task::with(['project', 'assignedTo'])
            ->where('assigned_to', $user->id)
            ->whereBetween('due_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('due_date', 'asc')
            ->get();

        $search  = strtolower(trim($request->get('search', '')));
        $status  = $request->get('delay_status', 'all');
        $sortBy  = $request->get('sort_by');
        $sortDir = $request->get('sort_dir') === 'desc' ? 'desc' : 'asc';
        $page    = max(1, (int) $request->get('page', 1));
        $perPage = max(1, min(10000, (int) $request->get('per_page', 5)));

        $rows = $tasks->map(function ($task) {
            return [
                'title'        => $task->title,
                'project'      => optional($task->project)->project_name ?? '-',
                'due_date'     => optional($task->due_date)->format('d-m-Y'),
                'due_sort'     => optional($task->due_date)->format('Y-m-d'),
                'completed_at' => $task->completed_at ? $task->completed_at->format('d-m-Y') : '-',
                'status'       => $task->status,
                'status_label' => ucfirst(str_replace('_', ' ', $task->status)),
                'delay_status' => $this->classifyTaskDelay($task),
            ];
        });

        if ($status !== 'all') {
            $rows = $rows->where('delay_status', $status);
        }

        if ($search !== '') {
            $rows = $rows->filter(
                fn($row) =>
                str_contains(strtolower($row['title']), $search) ||
                    str_contains(strtolower($row['project']), $search)
            );
        }

        $sortMap = ['title' => 'title', 'project' => 'project', 'due' => 'due_sort', 'delay' => 'delay_status'];
        if ($sortBy && isset($sortMap[$sortBy])) {
            $rows = $sortDir === 'desc'
                ? $rows->sortByDesc($sortMap[$sortBy])
                : $rows->sortBy($sortMap[$sortBy]);
        }

        $rows     = $rows->values();
        $total    = $rows->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page     = min($page, $lastPage);

        return response()->json([
            'data'         => $rows->forPage($page, $perPage)->values(),
            'current_page' => $page,
            'last_page'    => $lastPage,
            'total'        => $total,
            'per_page'     => $perPage,
        ]);
    }


    public function myReportProjectsAjax(Request $request)
    {
        $user = Auth::user();
        [$filterType, $startDate, $endDate] = $this->resolveDateRange($request);

        $tasks = Task::with(['project', 'assignedTo'])
            ->where('assigned_to', $user->id)
            ->whereBetween('due_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('due_date', 'asc')
            ->get();

        $report   = $this->summarizeTasks($tasks);
        $projects = collect($report['projectSummary'])->values();

        $search  = strtolower(trim($request->get('search', '')));
        $page    = max(1, (int) $request->get('page', 1));
        $perPage = max(1, min(10000, (int) $request->get('per_page', 5)));

        if ($search !== '') {
            $projects = $projects->filter(
                fn($p) =>
                str_contains(strtolower($p['name']), $search)
            );
        }

        $projects = $projects->values();
        $total    = $projects->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page     = min($page, $lastPage);

        return response()->json([
            'data'         => $projects->forPage($page, $perPage)->values(),
            'current_page' => $page,
            'last_page'    => $lastPage,
            'total'        => $total,
            'per_page'     => $perPage,
        ]);
    }
}
