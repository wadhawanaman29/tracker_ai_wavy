@include('layouts.header')

@php
    $statusMeta = [
        'pending' => [
            'label' => 'Pending',
            'icon' => 'bx-time-five',
            'color' => '#94a3b8',
            'gradient' => 'linear-gradient(135deg, #cbd5e1, #94a3b8)',
            'glow' => 'rgba(148,163,184,0.15)',
        ],
        'in_progress' => [
            'label' => 'In Progress',
            'icon' => 'bx-rocket',
            'color' => '#6366f1',
            'gradient' => 'linear-gradient(135deg, #a5b4fc, #6366f1)',
            'glow' => 'rgba(99,102,241,0.15)',
        ],
        'on_hold' => [
            'label' => 'On Hold',
            'icon' => 'bx-pause-circle',
            'color' => '#f59e0b',
            'gradient' => 'linear-gradient(135deg, #fcd34d, #f59e0b)',
            'glow' => 'rgba(245,158,11,0.15)',
        ],
        'testing' => [
            'label' => 'Testing',
            'icon' => 'bx-test-tube',
            'color' => '#06b6d4',
            'gradient' => 'linear-gradient(135deg, #67e8f9, #06b6d4)',
            'glow' => 'rgba(6,182,212,0.15)',
        ],
        'completed' => [
            'label' => 'Completed',
            'icon' => 'bx-check-shield',
            'color' => '#10b981',
            'gradient' => 'linear-gradient(135deg, #6ee7b7, #10b981)',
            'glow' => 'rgba(16,185,129,0.15)',
        ],
        'cancelled' => [
            'label' => 'Cancelled',
            'icon' => 'bx-block',
            'color' => '#64748b',
            'gradient' => 'linear-gradient(135deg, #cbd5e1, #64748b)',
            'glow' => 'rgba(100,116,139,0.15)',
        ],
    ];

    $avatarColors = ['primary', 'success', 'info', 'warning', 'danger'];
@endphp

<style>
    /* ================= UNIQUE KANBAN DESIGN ================= */
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .kanban-wrap * {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .kanban-wrap {
        position: relative;
        padding: 1rem 0 2rem;
    }

    /* Floating background orbs */
    .kanban-wrap::before,
    .kanban-wrap::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        filter: blur(90px);
        opacity: 0.5;
        pointer-events: none;
        z-index: 0;
    }

    .kanban-wrap::before {
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(165, 180, 252, 0.5), transparent 70%);
        top: -60px;
        left: -60px;
    }

    .kanban-wrap::after {
        width: 380px;
        height: 380px;
        background: radial-gradient(circle, rgba(110, 231, 183, 0.4), transparent 70%);
        bottom: -80px;
        right: -80px;
    }

    /* ================= HERO HEADER ================= */
    .kanban-hero {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.75rem;
        border-radius: 1.5rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8faff 50%, #f0f4ff 100%);
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow:
            0 1px 2px rgba(15, 23, 42, 0.03),
            0 8px 32px -12px rgba(99, 102, 241, 0.12);
        overflow: hidden;
    }

    .kanban-hero::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(165, 180, 252, 0.25), transparent 65%);
        pointer-events: none;
    }

    .kanban-hero-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.55rem;
        box-shadow: 0 8px 20px -6px rgba(99, 102, 241, 0.5);
        flex-shrink: 0;
    }

    .kanban-hero-title {
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: -0.025em;
        color: #0f172a;
        margin: 0;
        line-height: 1.2;
    }

    .kanban-hero-title span {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .kanban-hero-sub {
        font-size: 0.82rem;
        color: #94a3b8;
        font-weight: 500;
        margin: 4px 0 0;
    }

    /* Buttons */
    .btn-hero-primary {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.7rem 1.25rem;
        border-radius: 12px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        font-weight: 600;
        font-size: 0.85rem;
        border: none;
        box-shadow:
            0 4px 14px -2px rgba(99, 102, 241, 0.4),
            0 2px 6px -2px rgba(99, 102, 241, 0.3);
        transition: all 0.25s ease;
        text-decoration: none;
        overflow: hidden;
    }

    .btn-hero-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
        transition: left 0.6s ease;
    }

    .btn-hero-primary:hover {
        transform: translateY(-2px);
        box-shadow:
            0 8px 24px -4px rgba(99, 102, 241, 0.5),
            0 3px 10px -2px rgba(99, 102, 241, 0.35);
        color: #fff;
    }

    .btn-hero-primary:hover::before {
        left: 100%;
    }

    .btn-hero-ghost {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.7rem 1.1rem;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.9);
        color: #475569;
        font-weight: 600;
        font-size: 0.82rem;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-hero-ghost:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #1e293b;
        transform: translateY(-1px);
    }

    /* ================= FILTER PANEL ================= */
    .kanban-filters {
        position: relative;
        z-index: 1;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(226, 232, 240, 0.7);
        border-radius: 1.25rem;
        padding: 1.1rem 1.25rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 2px 12px -4px rgba(15, 23, 42, 0.06);
    }

    .kanban-filters label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #94a3b8;
        margin-bottom: 0.4rem;
        display: block;
    }

    .kanban-filters select {
        width: 100%;
        padding: 0.6rem 2.2rem 0.6rem 0.9rem;
        border-radius: 10px;
        border: 1.5px solid #eef2f7;
        background-color: #f8fafc;
        font-size: 0.85rem;
        font-weight: 500;
        color: #1e293b;
        transition: all 0.2s ease;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
    }

    .kanban-filters select:hover {
        border-color: #c7d2fe;
        background-color: #fff;
    }

    .kanban-filters select:focus {
        outline: none;
        border-color: #6366f1;
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    /* ================= KANBAN COLUMNS ================= */
    .kb-board {
        position: relative;
        z-index: 1;
    }

    .kb-column {
        border-radius: 1.35rem;
        background: rgba(255, 255, 255, 0.72);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(226, 232, 240, 0.7);
        box-shadow:
            0 1px 2px rgba(15, 23, 42, 0.02),
            0 4px 20px -8px rgba(15, 23, 42, 0.08);
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .kb-column:hover {
        box-shadow:
            0 1px 2px rgba(15, 23, 42, 0.03),
            0 12px 32px -10px rgba(15, 23, 42, 0.12);
    }

    /* Column head */
    .kb-head {
        position: relative;
        padding: 1rem 1.1rem 0.85rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(226, 232, 240, 0.6);
    }

    .kb-head::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--kb-grad);
        border-radius: 3px 3px 0 0;
    }

    .kb-head-left {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        min-width: 0;
    }

    .kb-head-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: var(--kb-grad);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px -2px var(--kb-glow);
    }

    .kb-head-label {
        font-size: 0.88rem;
        font-weight: 700;
        color: #1e293b;
        letter-spacing: -0.01em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .kb-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 26px;
        height: 26px;
        padding: 0 0.5rem;
        border-radius: 8px;
        background: var(--kb-grad);
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
        box-shadow: 0 3px 8px -2px var(--kb-glow);
        flex-shrink: 0;
    }

    /* Column body */
    .kb-body {
        padding: 0.75rem;
        flex: 1;
        overflow-y: auto;
        background:
            linear-gradient(180deg, rgba(248, 250, 252, 0.4) 0%, rgba(248, 250, 252, 0.1) 100%);
        min-height: 200px;
        max-height: 68vh;
    }

    .kb-body::-webkit-scrollbar {
        width: 5px;
    }

    .kb-body::-webkit-scrollbar-track {
        background: transparent;
    }

    .kb-body::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.3);
        border-radius: 10px;
    }

    .kb-body::-webkit-scrollbar-thumb:hover {
        background: rgba(148, 163, 184, 0.5);
    }

    /* ================= TASK CARDS ================= */
    .kb-card {
        position: relative;
        background: #ffffff;
        border-radius: 14px;
        padding: 0.85rem 0.9rem;
        margin-bottom: 0.65rem;
        cursor: grab;
        border: 1px solid rgba(226, 232, 240, 0.85);
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1),
            box-shadow 0.2s ease,
            border-color 0.2s ease;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        overflow: hidden;
    }

    .kb-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 3px;
        background: var(--kb-grad);
        opacity: 0.85;
        transition: width 0.25s ease;
    }

    .kb-card:hover {
        transform: translateY(-3px);
        box-shadow:
            0 2px 4px rgba(15, 23, 42, 0.04),
            0 12px 28px -8px var(--kb-glow);
        border-color: rgba(99, 102, 241, 0.25);
    }

    .kb-card:hover::before {
        width: 4px;
    }

    .kb-card:active {
        cursor: grabbing;
        transform: scale(0.985);
    }

    .kb-card-title {
        font-size: 0.86rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
        text-decoration: none;
        display: block;
        margin-bottom: 0.55rem;
        letter-spacing: -0.005em;
        transition: color 0.15s ease;
        padding-left: 0.35rem;
    }

    .kb-card-title:hover {
        color: var(--kb-color);
    }

    .kb-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        padding-left: 0.35rem;
        margin-bottom: 0.55rem;
    }

    .kb-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.28rem 0.55rem;
        border-radius: 7px;
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        line-height: 1;
    }

    .kb-tag i {
        font-size: 0.8rem;
    }

    .kb-tag-project {
        background: linear-gradient(135deg, #eef2ff, #e0e7ff);
        color: #4f46e5;
    }

    .kb-tag-date {
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        color: #475569;
    }

    .kb-tag-danger {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #dc2626;
    }

    .kb-tag-warning {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #d97706;
    }

    .kb-tag-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #059669;
    }

    /* Assignee */
    .kb-assignee {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-top: 0.6rem;
        margin-top: 0.1rem;
        padding-left: 0.35rem;
        border-top: 1px dashed rgba(226, 232, 240, 0.9);
    }

    .kb-avatar {
        width: 24px;
        height: 24px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
        box-shadow: 0 2px 6px -1px rgba(15, 23, 42, 0.15);
    }

    .kb-avatar-name {
        font-size: 0.73rem;
        font-weight: 600;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Empty state */
    .kb-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        color: #cbd5e1;
        background: rgba(255, 255, 255, 0.6);
        border: 1.5px dashed rgba(203, 213, 225, 0.7);
        border-radius: 12px;
        text-align: center;
    }

    .kb-empty i {
        font-size: 1.75rem;
        margin-bottom: 0.4rem;
        opacity: 0.7;
    }

    .kb-empty span {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    /* Drag ghost */
    .sortable-ghost {
        opacity: 0.35;
        background: #eef2ff !important;
        border-style: dashed !important;
        transform: rotate(2deg);
    }

    .sortable-drag {
        transform: rotate(-1.5deg);
        box-shadow: 0 20px 40px -12px rgba(99, 102, 241, 0.35) !important;
    }

    /* Capped note */
    .kb-capped-note {
        padding: 0.6rem 1.1rem;
        font-size: 0.7rem;
        color: #94a3b8;
        background: rgba(248, 250, 252, 0.6);
        border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        font-weight: 500;
    }

    .kb-capped-note a {
        color: #6366f1;
        font-weight: 600;
        text-decoration: none;
    }

    .kb-capped-note a:hover {
        text-decoration: underline;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .kb-body {
            max-height: 50vh;
        }
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="kanban-wrap">

        <div class="kanban-hero">
            <div class="d-flex align-items-center gap-3">
                <div class="kanban-hero-icon">
                    <i class="bx bx-grid-alt"></i>
                </div>
                <div>
                    <h4 class="kanban-hero-title">
                        {{ $isAdmin ? 'Task' : 'My' }}
                        <span>{{ $isAdmin ? 'Board' : 'Tasks' }}</span>
                    </h4>
                    <p class="kanban-hero-sub">
                        <i class="bx bx-move" style="vertical-align:middle;"></i>
                        Drag &amp; drop to change status instantly
                    </p>
                </div>
            </div>

            @if ($isAdmin)
                <a href="{{ route('assigned_task') }}" class="btn-hero-primary">
                    <i class="bx bx-plus-circle" style="font-size:1.05rem;"></i>
                    New Task
                </a>
            @endif
        </div>

        @if ($isAdmin)
            <div class="kanban-filters">
                <form method="GET" action="{{ route('assigned_task_list') }}" class="row g-3 align-items-end">
                    <div class="col-md-3 col-sm-6">
                        <label><i class="bx bx-folder"></i> Project</label>
                        <select name="project_id">
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
                        <label><i class="bx bx-user"></i> Employee</label>
                        <select name="employee_id">
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
                        <label>&nbsp;</label>
                        <button type="submit" class="btn-hero-primary w-100 justify-content-center">
                            <i class="bx bx-filter-alt"></i> Apply
                        </button>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label>&nbsp;</label>
                        <a href="{{ route('progress_report') }}" class="btn-hero-ghost w-100 justify-content-center">
                            <i class="bx bx-line-chart"></i> Report
                        </a>
                    </div>
                </form>
            </div>
        @endif

        <div class="row g-3 kb-board">
            @foreach ($statuses as $status)
                @php $meta = $statusMeta[$status]; @endphp
                <div class="col-lg col-md-6">
                    <div class="kb-column"
                        style="--kb-grad: {{ $meta['gradient'] }}; --kb-glow: {{ $meta['glow'] }}; --kb-color: {{ $meta['color'] }};">

                        <div class="kb-head">
                            <div class="kb-head-left">
                                <div class="kb-head-icon">
                                    <i class="bx {{ $meta['icon'] }}"></i>
                                </div>
                                <span class="kb-head-label">{{ $meta['label'] }}</span>
                            </div>
                            <span class="kb-count">{{ $tasksByStatus[$status]->count() }}</span>
                        </div>

                        @if ($doneColumnCapped[$status] ?? false)
                            <div class="kb-capped-note">
                                <i class="bx bx-info-circle"></i> Last {{ $doneLookbackDays }} days only.
                                @if ($isAdmin)
                                    <a href="{{ route('progress_report') }}">View all</a>
                                @endif
                            </div>
                        @endif

                        <div class="kb-body" data-status="{{ $status }}">
                            @forelse ($tasksByStatus[$status] as $t)
                                @php
                                    $dueDate = \Carbon\Carbon::parse($t->due_date);
                                    $daysLeft = \Carbon\Carbon::today()->diffInDays($dueDate, false);

                                    if (in_array($t->status, ['completed', 'cancelled'])) {
                                        $dueClass = 'kb-tag-date';
                                    } elseif ($daysLeft < 0) {
                                        $dueClass = 'kb-tag-danger';
                                    } elseif ($daysLeft <= 1) {
                                        $dueClass = 'kb-tag-warning';
                                    } else {
                                        $dueClass = 'kb-tag-success';
                                    }

                                    $assigneeName = optional($t->assignedTo)->name;
                                    $initials = $assigneeName
                                        ? collect(explode(' ', trim($assigneeName)))
                                            ->map(fn($w) => mb_substr($w, 0, 1))
                                            ->take(2)
                                            ->implode('')
                                        : '?';

                                    $avatarGradients = [
                                        'linear-gradient(135deg, #818cf8, #6366f1)',
                                        'linear-gradient(135deg, #6ee7b7, #10b981)',
                                        'linear-gradient(135deg, #67e8f9, #06b6d4)',
                                        'linear-gradient(135deg, #fcd34d, #f59e0b)',
                                        'linear-gradient(135deg, #fca5a5, #ef4444)',
                                    ];
                                    $avatarBg = $avatarGradients[$t->assigned_to % count($avatarGradients)];
                                @endphp

                                <div class="kb-card" draggable="true" data-task-id="{{ $t->id }}">
                                    <a href="{{ route('assigned.view', $t->id) }}" class="kb-card-title">
                                        {{ $t->title }}
                                    </a>

                                    <div class="kb-tags">
                                        <span class="kb-tag kb-tag-project">
                                            <i class="bx bx-briefcase"></i>
                                            {{ optional($t->project)->project_name ?? '-' }}
                                        </span>
                                        <span class="kb-tag {{ $dueClass }}">
                                            <i class="bx bx-calendar"></i>
                                            {{ $dueDate->format('d M') }}
                                        </span>
                                    </div>

                                    @if ($isAdmin)
                                        <div class="kb-assignee">
                                            <div class="kb-avatar" style="background: {{ $avatarBg }};">
                                                {{ $initials }}
                                            </div>
                                            <span class="kb-avatar-name">{{ $assigneeName ?? 'Unassigned' }}</span>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="kb-empty">
                                    <i class="bx bx-inbox"></i>
                                    <span>No tasks yet</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@include('layouts.footer')

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.kb-body').forEach(function(list) {
            new Sortable(list, {
                group: 'tasks',
                animation: 180,
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                onEnd: async function(evt) {
                    if (evt.from === evt.to) return;

                    const taskId = evt.item.dataset.taskId;
                    const newStatus = evt.to.dataset.status;

                    const response = await ajaxCall({
                        status: newStatus
                    }, 'PUT', `/assigned_task/${taskId}/status`);

                    if (response.success) {
                        showToast(response.message, 'success');
                    } else {
                        showToast(response.message || 'Could not update task status.',
                            'error');
                        if (evt.oldIndex >= evt.from.children.length) {
                            evt.from.appendChild(evt.item);
                        } else {
                            evt.from.insertBefore(evt.item, evt.from.children[evt
                                .oldIndex]);
                        }
                    }
                }
            });
        });
    });
</script>
