@include('layouts.header')

@php
    $statusMeta = [
        'pending' => ['label' => 'Pending', 'header' => 'bg-label-secondary', 'icon' => 'bx-list-ul'],
        'in_progress' => ['label' => 'In Progress', 'header' => 'bg-label-primary', 'icon' => 'bx-loader-circle'],
        'on_hold' => ['label' => 'On Hold', 'header' => 'bg-label-warning', 'icon' => 'bx-pause-circle'],
        'completed' => ['label' => 'Completed', 'header' => 'bg-label-success', 'icon' => 'bx-check-circle'],
        'cancelled' => ['label' => 'Cancelled', 'header' => 'bg-label-dark', 'icon' => 'bx-x-circle'],
    ];
@endphp

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
                <div class="card h-100">
                    <div class="card-header {{ $meta['header'] }} d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">
                            <i class="bx {{ $meta['icon'] }} me-1"></i> {{ $meta['label'] }}
                        </span>
                        <span class="badge bg-white text-dark">{{ $tasksByStatus[$status]->count() }}</span>
                    </div>
                    @if (($doneColumnCapped[$status] ?? false))
                        <div class="px-3 pt-2 small text-muted">
                            Showing last {{ $doneLookbackDays }} days only.
                            @if ($isAdmin)
                                <a href="{{ route('progress_report') }}">View full history</a>
                            @endif
                        </div>
                    @endif
                    <div class="card-body kanban-column-body" data-status="{{ $status }}"
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
                            @endphp
                            <div class="card mb-3 kanban-card" draggable="true" data-task-id="{{ $t->id }}">
                                <div class="card-body p-3">
                                    <a href="{{ route('assigned.view', $t->id) }}"
                                        class="fw-semibold text-body d-block mb-1">
                                        {{ $t->title }}
                                    </a>

                                    <div class="mb-2">
                                        <span class="badge bg-label-info">{{ optional($t->project)->project_name ?? '-' }}</span>
                                        <span class="badge {{ $dueBadgeClass }}">
                                            Due {{ $dueDate->format('d M') }}
                                        </span>
                                    </div>

                                    @if ($isAdmin)
                                        <div class="small text-muted mb-2">
                                            <i class="bx bx-user"></i> {{ optional($t->assignedTo)->name ?? '-' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center small mb-0">No tasks</p>
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
