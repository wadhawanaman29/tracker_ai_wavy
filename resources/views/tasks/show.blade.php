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
    $statusColor = [
        'pending' => '#8592a3',
        'in_progress' => '#696cff',
        'on_hold' => '#ffab00',
        'completed' => '#71dd37',
        'cancelled' => '#233446',
    ];
    $due = \Carbon\Carbon::parse($task->due_date);
    $canEditThisTask = $isAdmin || (int) $task->assigned_to === (int) Auth::id();
@endphp

<style>
    .task-header-card {
        border: none;
        border-top: 4px solid {{ $statusColor[$task->status] }};
        border-radius: 0.75rem;
        box-shadow: 0 2px 6px rgba(67, 89, 113, 0.08);
    }

    .task-meta-item {
        background-color: #f7f8fa;
        border-radius: 0.5rem;
        padding: 0.65rem 0.9rem;
    }

    .task-meta-item .meta-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #8592a3;
    }

    .status-timeline {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .status-timeline li {
        position: relative;
        padding: 0 0 1.25rem 1.75rem;
        border-left: 2px solid #eceef1;
    }

    .status-timeline li:last-child {
        border-left-color: transparent;
        padding-bottom: 0;
    }

    .status-timeline li::before {
        content: '';
        position: absolute;
        left: -7px;
        top: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--dot-color, #8592a3);
        box-shadow: 0 0 0 3px #fff;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Task Management /</span>
        Task Detail
    </h4>

    <div class="card task-header-card mb-4">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center flex-wrap">
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
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="task-meta-item h-100">
                        <div class="meta-label"><i class="bx bx-briefcase"></i> Project</div>
                        <div class="fw-semibold">{{ optional($task->project)->project_name ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="task-meta-item h-100">
                        <div class="meta-label"><i class="bx bx-user"></i> Assigned To</div>
                        <div class="fw-semibold">{{ optional($task->assignedTo)->name ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="task-meta-item h-100">
                        <div class="meta-label"><i class="bx bx-user-check"></i> Assigned By</div>
                        <div class="fw-semibold">{{ optional($task->assignedBy)->name ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="task-meta-item h-100">
                        <div class="meta-label"><i class="bx bx-calendar"></i> Due Date</div>
                        <div class="fw-semibold">{{ $due->format('d M Y') }}</div>
                    </div>
                </div>
            </div>

            @if ($task->description)
                <div class="mb-3">
                    <strong>Description:</strong>
                    <div>{!! $task->description !!}</div>
                </div>
            @endif

            @if ($canEditThisTask)
                <div class="input-group input-group-sm" style="max-width: 320px;">
                    <select id="taskStatusSelect" class="form-select">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" {{ $task->status == $status ? 'selected' : '' }}>
                                {{ $statusLabels[$status] }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-primary" id="updateTaskStatusBtn"
                        data-task-id="{{ $task->id }}">
                        <i class="bx bx-refresh me-1"></i> Update
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">
            <i class="bx bx-history"></i> Status History
        </h5>
        <div class="card-body">
            @forelse ($task->statusLogs as $log)
                @if ($loop->first)
                    <ul class="status-timeline">
                @endif

                <li style="--dot-color: {{ $statusColor[$log->to_status] ?? '#8592a3' }};">
                    <div class="d-flex justify-content-between flex-wrap">
                        <div>
                            @if ($log->from_status)
                                <span class="text-muted">{{ $statusLabels[$log->from_status] }}</span>
                                <i class="bx bx-right-arrow-alt mx-1"></i>
                            @endif
                            <span class="fw-semibold">{{ $statusLabels[$log->to_status] }}</span>
                        </div>
                        <small class="text-muted">{{ $log->created_at->format('d M Y, h:i A') }}</small>
                    </div>
                    <div class="small text-muted">by {{ optional($log->changedBy)->name ?? '-' }}</div>
                </li>

                @if ($loop->last)
                    </ul>
                @endif
            @empty
                <p class="text-muted text-center mb-0">No history yet</p>
            @endforelse
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
