@include('layouts.header')

@php
    $statusMeta = [
        'pending' => ['label' => 'Pending', 'badge' => 'bg-label-secondary', 'icon' => 'bx-list-ul', 'color' => '#8592a3'],
        'in_progress' => ['label' => 'In Progress', 'badge' => 'bg-label-primary', 'icon' => 'bx-loader-circle', 'color' => '#696cff'],
        'on_hold' => ['label' => 'On Hold', 'badge' => 'bg-label-warning', 'icon' => 'bx-pause-circle', 'color' => '#ffab00'],
        'completed' => ['label' => 'Completed', 'badge' => 'bg-label-success', 'icon' => 'bx-check-circle', 'color' => '#71dd37'],
        'cancelled' => ['label' => 'Cancelled', 'badge' => 'bg-label-dark', 'icon' => 'bx-x-circle', 'color' => '#233446'],
    ];

    $avatarColors = ['primary', 'success', 'info', 'warning', 'danger'];
@endphp

<style>
    .kanban-column-card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 2px 6px rgba(67, 89, 113, 0.08);
    }

    .kanban-column-header {
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
        border-bottom: 2px solid rgba(0, 0, 0, 0.06);
    }

    .kanban-column-body {
        background-color: #f7f8fa;
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
    }

    .kanban-card {
        border: none !important;
        border-left: 3px solid var(--kanban-accent, #8592a3) !important;
        border-radius: 0.5rem;
        box-shadow: 0 1px 2px rgba(67, 89, 113, 0.1);
        cursor: grab;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .kanban-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(67, 89, 113, 0.18);
    }

    .kanban-card:active {
        cursor: grabbing;
    }

    .kanban-card .card-body {
        padding: 0.9rem 1rem;
    }

    .kanban-empty {
        border: 1px dashed rgba(67, 89, 113, 0.25);
        border-radius: 0.5rem;
        padding: 1.5rem 0.5rem;
    }

    .sortable-ghost {
        opacity: 0.4;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center flex-wrap py-3 mb-2">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Task Management /</span>
            {{ $isAdmin ? 'Task Board' : 'My Tasks' }}
        </h4>

        @if ($isAdmin)
            <a href="{{ route('assigned_task') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> New Task
            </a>
        @endif
    </div>

    @if ($isAdmin)
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('assigned_task_list') }}" class="row g-3 align-items-end">
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label fw-semibold">Project</label>
                        <select name="project_id" class="form-select">
                            <option value="all" {{ $selectedProjectId == 'all' ? 'selected' : '' }}>All Projects
                            </option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}"
                                    {{ $selectedProjectId == $project->id ? 'selected' : '' }}>
                                    {{ $project->project_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label class="form-label fw-semibold">Employee</label>
                        <select name="employee_id" class="form-select">
                            <option value="all" {{ $selectedEmployeeId == 'all' ? 'selected' : '' }}>All Employees
                            </option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ $selectedEmployeeId == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <button type="submit" class="btn btn-primary w-100">Apply</button>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <a href="{{ route('progress_report') }}" class="btn btn-outline-primary w-100">
                            <i class="bx bx-line-chart me-1"></i> Delay Report
                        </a>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="row g-3 kanban-board">
        @foreach ($statuses as $status)
            @php $meta = $statusMeta[$status]; @endphp
            <div class="col-lg col-md-6">
                <div class="card kanban-column-card h-100">
                    <div
                        class="card-header kanban-column-header {{ $meta['badge'] }} d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">
                            <i class="bx {{ $meta['icon'] }} me-1"></i> {{ $meta['label'] }}
                        </span>
                        <span class="badge rounded-pill bg-white text-dark">{{ $tasksByStatus[$status]->count() }}</span>
                    </div>
                    @if (($doneColumnCapped[$status] ?? false))
                        <div class="px-3 pt-2 small text-muted">
                            <i class="bx bx-info-circle"></i> Showing last {{ $doneLookbackDays }} days only.
                            @if ($isAdmin)
                                <a href="{{ route('progress_report') }}">View full history</a>
                            @endif
                        </div>
                    @endif
                    <div class="card-body kanban-column-body p-2" data-status="{{ $status }}"
                        style="min-height:200px; max-height:65vh; overflow-y:auto;">
                        @forelse ($tasksByStatus[$status] as $t)
                            @php
                                $dueDate = \Carbon\Carbon::parse($t->due_date);
                                $daysLeft = \Carbon\Carbon::today()->diffInDays($dueDate, false);

                                if (in_array($t->status, ['completed', 'cancelled'])) {
                                    $dueBadgeClass = 'bg-label-secondary';
                                } elseif ($daysLeft < 0) {
                                    $dueBadgeClass = 'bg-label-danger';
                                } elseif ($daysLeft <= 1) {
                                    $dueBadgeClass = 'bg-label-warning';
                                } else {
                                    $dueBadgeClass = 'bg-label-success';
                                }

                                $assigneeName = optional($t->assignedTo)->name;
                                $initials = $assigneeName
                                    ? collect(explode(' ', trim($assigneeName)))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('')
                                    : '?';
                                $avatarColor = $avatarColors[$t->assigned_to % count($avatarColors)];
                            @endphp
                            <div class="card mb-2 kanban-card" draggable="true" data-task-id="{{ $t->id }}"
                                style="--kanban-accent: {{ $meta['color'] }};">
                                <div class="card-body">
                                    <a href="{{ route('assigned.view', $t->id) }}"
                                        class="fw-semibold text-body d-block mb-2">
                                        {{ $t->title }}
                                    </a>

                                    <div class="mb-2 d-flex flex-wrap gap-1">
                                        <span class="badge bg-label-info">
                                            <i class="bx bx-briefcase"></i>
                                            {{ optional($t->project)->project_name ?? '-' }}
                                        </span>
                                        <span class="badge {{ $dueBadgeClass }}">
                                            <i class="bx bx-calendar"></i>
                                            {{ $dueDate->format('d M') }}
                                        </span>
                                    </div>

                                    @if ($isAdmin)
                                        <div class="d-flex align-items-center gap-2 mt-2">
                                            <div class="avatar avatar-xs">
                                                <span
                                                    class="avatar-initial rounded-circle bg-label-{{ $avatarColor }}">{{ $initials }}</span>
                                            </div>
                                            <span class="small text-muted">{{ $assigneeName ?? 'Unassigned' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="kanban-empty text-center text-muted small">
                                <i class="bx bx-inbox fs-4 d-block mb-1"></i>
                                No tasks
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@include('layouts.footer')

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.kanban-column-body').forEach(function(list) {
            new Sortable(list, {
                group: 'tasks',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: async function(evt) {
                    if (evt.from === evt.to) {
                        return;
                    }

                    const taskId = evt.item.dataset.taskId;
                    const newStatus = evt.to.dataset.status;

                    const response = await ajaxCall({
                        status: newStatus
                    }, 'PUT', `/assigned_task/${taskId}/status`);

                    if (response.success) {
                        showToast(response.message, 'success');
                    } else {
                        showToast(response.message || 'Could not update task status.', 'error');

                        // Revert the card back to its original column/position.
                        if (evt.oldIndex >= evt.from.children.length) {
                            evt.from.appendChild(evt.item);
                        } else {
                            evt.from.insertBefore(evt.item, evt.from.children[evt.oldIndex]);
                        }
                    }
                }
            });
        });
    });
</script>
