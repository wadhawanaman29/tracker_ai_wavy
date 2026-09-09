@include('layouts.header')

@php
    $statusLabels = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'on_hold' => 'On Hold',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];
    $statusBadge = [
        'pending' => 'bg-label-secondary',
        'in_progress' => 'bg-label-primary',
        'on_hold' => 'bg-label-warning',
        'completed' => 'bg-label-success',
        'cancelled' => 'bg-label-dark',
    ];
    $due = \Carbon\Carbon::parse($task->due_date);
    $canEditThisTask = $isAdmin || (int) $task->assigned_to === (int) Auth::id();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Task Management /</span>
        Task Detail
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h5 class="mb-1">{{ $task->title }}</h5>
                <span class="badge {{ $statusBadge[$task->status] }}">{{ $statusLabels[$task->status] }}</span>
                @if ($task->parent_id)
                    <span class="badge bg-label-info">Subtask of
                        <a href="{{ route('assigned.view', $task->parent_id) }}">{{ optional($task->parent)->title }}</a>
                    </span>
                @endif
            </div>

            @if ($isAdmin)
                <div>
                    <a href="{{ route('assigned.edit', $task->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-edit-alt"></i> Edit
                    </a>
                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger delete-btn"
                        data-form-id="deleteTaskForm{{ $task->id }}">
                        <i class="bx bx-trash"></i> Delete
                    </a>
                    <form id="deleteTaskForm{{ $task->id }}"
                        action="{{ route('assigned_task_delete', $task->id) }}" method="POST"
                        style="display:none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            @endif
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3"><strong>Project:</strong> {{ optional($task->project)->project_name ?? '-' }}
                </div>
                <div class="col-md-3"><strong>Assigned To:</strong> {{ optional($task->assignedTo)->name ?? '-' }}
                </div>
                <div class="col-md-3"><strong>Assigned By:</strong> {{ optional($task->assignedBy)->name ?? '-' }}
                </div>
                <div class="col-md-3"><strong>Due Date:</strong> {{ $due->format('d M Y') }}</div>
            </div>

            @if ($task->description)
                <div class="mb-3">
                    <strong>Description:</strong>
                    <div>{!! $task->description !!}</div>
                </div>
            @endif

            @if ($canEditThisTask)
                <div class="d-flex align-items-center gap-2">
                    <select id="taskStatusSelect" class="form-select form-select-sm" style="width:auto;">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" {{ $task->status == $status ? 'selected' : '' }}>
                                {{ $statusLabels[$status] }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-sm btn-primary" id="updateTaskStatusBtn"
                        data-task-id="{{ $task->id }}">
                        Update Status
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Subtasks</h5>
            @if ($isAdmin)
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                    data-bs-target="#addSubtaskModal">
                    <i class="bx bx-plus"></i> Add Subtask
                </button>
            @endif
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Assigned To</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subtasks as $i => $sub)
                        @php
                            $subCanEdit = $isAdmin || (int) $sub->assigned_to === (int) Auth::id();
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><a href="{{ route('assigned.view', $sub->id) }}">{{ $sub->title }}</a></td>
                            <td>{{ optional($sub->assignedTo)->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($sub->due_date)->format('d M Y') }}</td>
                            <td>
                                <select class="form-select form-select-sm subtask-status-select"
                                    data-task-id="{{ $sub->id }}" {{ $subCanEdit ? '' : 'disabled' }}>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}"
                                            {{ $sub->status == $status ? 'selected' : '' }}>
                                            {{ $statusLabels[$status] }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                @if ($isAdmin)
                                    <a href="{{ route('assigned.edit', $sub->id) }}"><i
                                            class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" class="delete-btn"
                                        data-form-id="deleteSubtaskForm{{ $sub->id }}">
                                        <i class="bx bx-trash me-1"></i>
                                    </a>
                                    <form id="deleteSubtaskForm{{ $sub->id }}"
                                        action="{{ route('assigned_task_delete', $sub->id) }}" method="POST"
                                        style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No subtasks yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Status History</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>To</th>
                        <th>Changed By</th>
                        <th>When</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($task->statusLogs as $log)
                        <tr>
                            <td>{{ $log->from_status ? $statusLabels[$log->from_status] : '-' }}</td>
                            <td>{{ $statusLabels[$log->to_status] }}</td>
                            <td>{{ optional($log->changedBy)->name ?? '-' }}</td>
                            <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No history</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if ($isAdmin)
    <div class="modal fade" id="addSubtaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('assigned_task.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $task->id }}">
                    <input type="hidden" name="project_id" value="{{ $task->project_id }}">

                    <div class="modal-header">
                        <h5 class="modal-title">Add Subtask</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Assign To</label>
                            <select name="assigned_to" class="form-select" required>
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ $employee->id == $task->assigned_to ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Subtask</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@include('layouts.footer')

<script>
    async function updateTaskStatus(taskId, newStatus) {
        const response = await ajaxCall({
            status: newStatus
        }, 'PUT', `/assigned_task/${taskId}/status`);

        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(response.message || 'Could not update status.', 'error');
        }
    }

    document.getElementById('updateTaskStatusBtn')?.addEventListener('click', function() {
        const select = document.getElementById('taskStatusSelect');
        updateTaskStatus(this.dataset.taskId, select.value);
    });

    document.querySelectorAll('.subtask-status-select').forEach(function(select) {
        select.addEventListener('change', function() {
            updateTaskStatus(this.dataset.taskId, this.value);
        });
    });
</script>
