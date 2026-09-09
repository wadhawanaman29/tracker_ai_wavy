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
</script>
