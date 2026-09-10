@include('layouts.header')
<style>
    /* =========================================================
       Report pages theme (Delay Report / My Report)
       ========================================================= */

    :root {
        --dr-bg: #f5f5f9;
        --dr-surface: #ffffff;
        --dr-border: #eceef1;
        --dr-text: #32324d;
        --dr-muted: #8896ab;
        --dr-primary: #696cff;
        --dr-primary-soft: #eeeeff;
        --dr-success: #22c55e;
        --dr-success-soft: #eafcef;
        --dr-danger: #ef4444;
        --dr-danger-soft: #fdeded;
        --dr-warning: #f59e0b;
        --dr-warning-soft: #fff6e8;
        --dr-info: #0ea5e9;
        --dr-info-soft: #e9f8ff;
        --dr-radius: 0.75rem;
        --dr-shadow: 0 2px 6px rgba(20, 20, 50, 0.06);
    }

    #delayReportRoot,
    #myReportRoot {
        color: var(--dr-text);
    }

    /* ---------- Page head ---------- */
    #delayReportRoot .dr-page-head,
    #myReportRoot .dr-page-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    #delayReportRoot .dr-page-head h4,
    #myReportRoot .dr-page-head h4 {
        margin: 0;
    }

    #delayReportRoot .dr-head-actions,
    #myReportRoot .dr-head-actions {
        display: flex;
        gap: 0.5rem;
    }

    #delayReportRoot .dr-head-actions button,
    #myReportRoot .dr-head-actions button {
        border: 1px solid var(--dr-border);
        background: var(--dr-surface);
        color: var(--dr-text);
        border-radius: 0.5rem;
        padding: 0.45rem 0.85rem;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        transition: all .15s ease;
    }

    #delayReportRoot .dr-head-actions button:hover,
    #myReportRoot .dr-head-actions button:hover {
        border-color: var(--dr-primary);
        color: var(--dr-primary);
        background: var(--dr-primary-soft);
    }

    /* ---------- Filter card ---------- */
    .dr-filter-card {
        background: var(--dr-surface);
        border: 1px solid var(--dr-border);
        border-radius: var(--dr-radius);
        box-shadow: var(--dr-shadow);
        padding: 1.25rem 1.25rem 1rem;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .dr-filter-card label {
        font-size: 0.75rem;
        text-transform: none;
        color: var(--dr-muted);
        font-weight: 600;
        margin-bottom: 0.3rem;
    }

    .dr-filter-card select,
    .dr-filter-card input[type=date] {
        border-radius: 0.5rem;
        border: 1px solid var(--dr-border);
        background: var(--dr-bg);
    }

    .dr-filter-card select:focus,
    .dr-filter-card input:focus {
        border-color: var(--dr-primary);
        box-shadow: 0 0 0 0.15rem rgba(105, 108, 255, 0.15);
        background: #fff;
    }

    .dr-btn-apply {
        background: var(--dr-primary);
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(105, 108, 255, 0.3);
    }

    .dr-btn-reset {
        background: var(--dr-surface);
        border: 1px solid var(--dr-border);
        border-radius: 0.5rem;
        font-weight: 600;
        color: var(--dr-muted);
    }

    .dr-btn-reset:hover {
        color: var(--dr-danger);
        border-color: var(--dr-danger);
    }

    .dr-loading-bar {
        position: absolute;
        left: 0;
        bottom: 0;
        height: 2px;
        width: 0;
        background: var(--dr-primary);
        transition: width .4s ease;
        border-radius: 0 2px 2px 0;
    }

    .dr-loading-bar.active {
        width: 100%;
    }

    /* ---------- Summary stat cards ---------- */
    .dr-summary-row {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width:992px) {
        .dr-summary-row { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width:576px) {
        .dr-summary-row { grid-template-columns: 1fr; }
    }

    .dr-stat {
        background: var(--dr-surface);
        border: 1px solid var(--dr-border);
        border-radius: var(--dr-radius);
        padding: 1.1rem 1.25rem;
        box-shadow: var(--dr-shadow);
        position: relative;
        overflow: hidden;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .dr-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(20, 20, 50, 0.08);
    }

    .dr-stat::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        background: var(--bar, #c7c9ff);
    }

    .dr-stat .dr-stat-label {
        font-size: 0.78rem;
        color: var(--dr-muted);
        font-weight: 600;
        margin-bottom: 0.35rem;
    }

    .dr-stat .dr-stat-value {
        font-size: 1.9rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.35rem;
    }

    .dr-stat .dr-stat-sub { font-size: 0.78rem; color: var(--dr-muted); }

    .dr-stat.total { --bar: #696cff; }
    .dr-stat.total .dr-stat-value { color: #696cff; }
    .dr-stat.ontime { --bar: var(--dr-success); }
    .dr-stat.ontime .dr-stat-value { color: var(--dr-success); }
    .dr-stat.delayed { --bar: var(--dr-danger); }
    .dr-stat.delayed .dr-stat-value { color: var(--dr-danger); }
    .dr-stat.overdue { --bar: var(--dr-warning); }
    .dr-stat.overdue .dr-stat-value { color: var(--dr-warning); }
    .dr-stat.completion { --bar: #0ea5e9; }
    .dr-stat.completion .dr-stat-value { color: #0ea5e9; }

    /* ---------- Generic card ---------- */
    .dr-card {
        background: var(--dr-surface);
        border: 1px solid var(--dr-border);
        border-radius: var(--dr-radius);
        box-shadow: var(--dr-shadow);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .dr-card-head {
        padding: 0.95rem 1.25rem;
        border-bottom: 1px solid var(--dr-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.6rem;
    }

    .dr-card-head h5,
    .dr-card-head h6 {
        margin: 0;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .dr-card-body { padding: 1.1rem 1.25rem; }

    /* ---------- Quick summary mini-grid ---------- */
    .dr-quick-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        text-align: center;
    }

    /* ---------- Progress bars ---------- */
    .dr-progress {
        height: 8px;
        border-radius: 999px;
        background: var(--dr-bg);
        overflow: hidden;
    }

    .dr-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #22c55e, #4ade80);
        border-radius: 999px;
    }

    .dr-progress.thin { height: 6px; }

    /* ---------- Tables ---------- */
    #delayReportRoot table,
    #myReportRoot table { font-size: 0.87rem; }

    #delayReportRoot thead th,
    #myReportRoot thead th {
        background: var(--dr-bg);
        color: var(--dr-muted);
        font-weight: 700;
        font-size: 0.72rem;
        border-bottom: 1px solid var(--dr-border);
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    #delayReportRoot thead th.sortable,
    #myReportRoot thead th.sortable {
        cursor: pointer;
        user-select: none;
    }

    #delayReportRoot thead th.sortable .bx,
    #myReportRoot thead th.sortable .bx {
        font-size: 0.85rem;
        vertical-align: -1px;
        opacity: 0.5;
    }

    #delayReportRoot thead th.sortable:hover,
    #myReportRoot thead th.sortable:hover { color: var(--dr-primary); }

    #delayReportRoot tbody tr,
    #myReportRoot tbody tr { transition: background .12s ease; }

    #delayReportRoot tbody tr:hover,
    #myReportRoot tbody tr:hover { background: var(--dr-primary-soft) !important; }

    #delayReportRoot tbody tr.row-overdue,
    #myReportRoot tbody tr.row-overdue { background: var(--dr-warning-soft); }

    #delayReportRoot tbody tr.row-delayed,
    #myReportRoot tbody tr.row-delayed { background: var(--dr-danger-soft); }

    .badge {
        font-weight: 600;
        font-size: 0.72rem;
        padding: 0.4em 0.65em;
        border-radius: 0.4rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3em;
    }

    .dr-days-tag { font-size: 0.72rem; color: var(--dr-muted); }

    /* ---------- Toolbar ---------- */
    .dr-table-toolbar {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        flex-wrap: wrap;
    }

    .dr-search-wrap { position: relative; }

    .dr-search-wrap .bx {
        position: absolute;
        left: 0.65rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--dr-muted);
    }

    .dr-search-wrap input {
        border: 1px solid var(--dr-border);
        border-radius: 0.5rem;
        padding: 0.4rem 0.75rem 0.4rem 2rem;
        font-size: 0.82rem;
        background: var(--dr-bg);
        min-width: 220px;
    }

    .dr-search-wrap input:focus {
        outline: none;
        border-color: var(--dr-primary);
        background: #fff;
        box-shadow: 0 0 0 0.15rem rgba(105, 108, 255, 0.15);
    }

    .dr-chip {
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.3rem 0.6rem;
        border-radius: 999px;
        border: 1px solid var(--dr-border);
        background: var(--dr-bg);
        color: var(--dr-muted);
        cursor: pointer;
        transition: all .12s ease;
    }

    .dr-chip.active {
        background: var(--dr-primary);
        border-color: var(--dr-primary);
        color: #fff;
    }

    .dr-count-tag {
        font-size: 0.78rem;
        color: var(--dr-muted);
        margin-left: auto;
    }

    /* ---------- Empty ---------- */
    .dr-empty {
        text-align: center;
        padding: 2.5rem 1rem;
        color: var(--dr-muted);
    }

    .dr-empty .bx {
        font-size: 2.2rem;
        display: block;
        margin-bottom: 0.4rem;
        opacity: 0.5;
    }

    /* =========================================================
       AJAX Pagination (Projects + Task Detail)
       ========================================================= */
    #myReportRoot .dr-table-loading {
        position: relative;
        max-height: none;
        overflow-x: auto;
    }

    #myReportRoot .dr-table-loading::after {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(1px);
        z-index: 3;
        pointer-events: none;
        opacity: 0;
        transition: opacity .2s ease;
    }

    #myReportRoot .dr-table-loading.is-loading::after { opacity: 1; }

    #myReportRoot .dr-table-loading.is-loading::before {
        content: '';
        position: absolute;
        top: 50%; left: 50%;
        width: 26px; height: 26px;
        margin: -13px 0 0 -13px;
        border: 2.5px solid #e0e7ff;
        border-top-color: var(--dr-primary);
        border-radius: 50%;
        animation: drSpin .7s linear infinite;
        z-index: 4;
        pointer-events: none;
    }

    @keyframes drSpin { to { transform: rotate(360deg); } }

    #myReportRoot .dr-pagination-bar {
        border-top: 1px solid var(--dr-border);
        background: #fafbfc;
        padding: 0.75rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    #myReportRoot .dr-page-info {
        font-size: 0.78rem;
        color: var(--dr-muted);
        font-weight: 500;
    }

    #myReportRoot .dr-page-info strong {
        color: var(--dr-text);
        font-weight: 700;
    }

    #myReportRoot #drPagination,
    #myReportRoot #drProjectPagination {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    #myReportRoot #drPagination .dr-page-btn,
    #myReportRoot #drProjectPagination .dr-page-btn {
        min-width: 32px;
        height: 32px;
        padding: 0 0.55rem;
        border-radius: 8px;
        border: 1px solid var(--dr-border);
        background: #fff;
        color: var(--dr-text);
        font-size: 0.78rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .15s ease;
        user-select: none;
    }

    #myReportRoot #drPagination .dr-page-btn:hover:not(:disabled):not(.active),
    #myReportRoot #drProjectPagination .dr-page-btn:hover:not(:disabled):not(.active) {
        border-color: var(--dr-primary);
        color: var(--dr-primary);
        background: var(--dr-primary-soft);
    }

    #myReportRoot #drPagination .dr-page-btn.active,
    #myReportRoot #drProjectPagination .dr-page-btn.active {
        background: var(--dr-primary);
        border-color: var(--dr-primary);
        color: #fff;
        box-shadow: 0 3px 8px -2px rgba(105, 108, 255, 0.4);
    }

    #myReportRoot #drPagination .dr-page-btn:disabled,
    #myReportRoot #drProjectPagination .dr-page-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    #myReportRoot #drPagination .dr-page-dots,
    #myReportRoot #drProjectPagination .dr-page-dots {
        min-width: 24px;
        text-align: center;
        color: var(--dr-muted);
        font-size: 0.78rem;
        font-weight: 600;
    }

    /* ---------- Print ---------- */
    @media print {
        .layout-menu,
        .layout-navbar,
        .dr-filter-card,
        .dr-table-toolbar,
        .dr-head-actions,
        .dr-pagination-bar,
        .content-footer { display: none !important; }

        .dr-card { box-shadow: none; border: 1px solid #ddd; }
    }
</style>

@php
    $delayBadge = [
        'on_time' => 'bg-success',
        'delayed' => 'bg-danger',
        'overdue' => 'bg-warning text-dark',
        'not_due_yet' => 'bg-info',
        'cancelled' => 'bg-secondary',
    ];
    $delayLabel = [
        'on_time' => 'On Time',
        'delayed' => 'Delayed',
        'overdue' => 'Overdue',
        'not_due_yet' => 'Not Due Yet',
        'cancelled' => 'Cancelled',
    ];
    $delayIcon = [
        'on_time' => 'bx-check-circle',
        'delayed' => 'bx-error-circle',
        'overdue' => 'bx-time-five',
        'not_due_yet' => 'bx-hourglass',
        'cancelled' => 'bx-x-circle',
    ];
@endphp

<div id="myReportRoot" class="container-xxl flex-grow-1 container-p-y">

    <div class="dr-page-head">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Task Management /</span>
            My Report
        </h4>
        <div class="dr-head-actions">
            <button type="button" onclick="window.print()"><i class='bx bx-printer'></i> Print</button>
            <button type="button" id="drExportCsvBtn"><i class='bx bx-download'></i> Export CSV</button>
        </div>
    </div>

    <div class="dr-filter-card">
        <form method="GET" action="{{ route('my_report') }}" class="row g-3 align-items-end" id="drFilterForm">
            <div class="col-md-3 col-sm-6">
                <label class="form-label">Range</label>
                <select name="filter_type" id="filterType" class="form-select">
                    <option value="this_month" {{ $filterType == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $filterType == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="this_year" {{ $filterType == 'this_year' ? 'selected' : '' }}>This Year</option>
                    <option value="last_year" {{ $filterType == 'last_year' ? 'selected' : '' }}>Last Year</option>
                    <option value="custom" {{ $filterType == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>

            <div class="col-md-3 col-sm-6 {{ $filterType == 'custom' ? '' : 'd-none' }}" id="startDateBox">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>

            <div class="col-md-3 col-sm-6 {{ $filterType == 'custom' ? '' : 'd-none' }}" id="endDateBox">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>

            <div class="col-md-2 col-sm-6 d-flex gap-2">
                <button type="submit" class="btn dr-btn-apply text-white w-100">Apply</button>
                <a href="{{ route('my_report') }}" class="btn dr-btn-reset" title="Reset filters"><i
                        class='bx bx-reset'></i></a>
            </div>
        </form>
        <div class="dr-loading-bar" id="drLoadingBar"></div>
    </div>

    <div class="dr-summary-row">
        <div class="dr-stat total">
            <p class="dr-stat-label">Total</p>
            <h2 class="dr-stat-value">{{ $totalTasks }}</h2>
            <span class="dr-stat-sub">Assigned tasks</span>
        </div>
        <div class="dr-stat ontime">
            <p class="dr-stat-label">On Time</p>
            <h2 class="dr-stat-value">{{ $counts['on_time'] }}</h2>
            <span class="dr-stat-sub">Delivered on time</span>
        </div>
        <div class="dr-stat delayed">
            <p class="dr-stat-label">Delayed</p>
            <h2 class="dr-stat-value">{{ $counts['delayed'] }}</h2>
            <span class="dr-stat-sub">Completed after due date</span>
        </div>
        <div class="dr-stat overdue">
            <p class="dr-stat-label">Overdue</p>
            <h2 class="dr-stat-value">{{ $counts['overdue'] }}</h2>
            <span class="dr-stat-sub">Still open, past due date</span>
        </div>
        <div class="dr-stat completion">
            <p class="dr-stat-label">Completion</p>
            <h2 class="dr-stat-value">{{ $completionRate }}%</h2>
            <span class="dr-stat-sub">Completed of total</span>
        </div>
    </div>

    <div class="row mb-1">
        <div class="col-md-6 mb-3">
            <div class="dr-card h-100 mb-0">
                <div class="dr-card-head">
                    <h6><i class='bx bx-pie-chart-alt-2'></i> Status Breakdown</h6>
                </div>
                <div class="dr-card-body">
                    <div style="position:relative;height:260px;">
                        <canvas id="delayStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="dr-card h-100 mb-0">
                <div class="dr-card-head">
                    <h6><i class='bx bx-trophy'></i> Quick Summary</h6>
                </div>
                <div class="dr-card-body d-flex flex-column justify-content-center h-100">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-semibold">Overall completion</span>
                        <span class="fw-semibold">{{ $completionRate }}%</span>
                    </div>
                    <div class="dr-progress mb-4">
                        <div class="dr-progress-bar" style="width: {{ $completionRate }}%"></div>
                    </div>
                    <div class="dr-quick-grid">
                        <div>
                            <div class="fw-bold fs-5 text-success">{{ $counts['on_time'] }}</div>
                            <div class="text-muted small">On Time</div>
                        </div>
                        <div>
                            <div class="fw-bold fs-5 text-danger">{{ $counts['delayed'] }}</div>
                            <div class="text-muted small">Delayed</div>
                        </div>
                        <div>
                            <div class="fw-bold fs-5 text-warning">{{ $counts['overdue'] }}</div>
                            <div class="text-muted small">Overdue</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== PROJECTS (AJAX PAGINATED) ==================== --}}
    <div class="dr-card">
        <div class="dr-card-head">
            <h5><i class='bx bx-briefcase'></i> Projects</h5>
            <div class="dr-table-toolbar">
                <div class="dr-search-wrap">
                    <i class='bx bx-search'></i>
                    <input type="text" id="drProjectSearch" placeholder="Search project...">
                </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap dr-table-loading" id="drProjectWrap">
            <table class="table table-hover mb-0" id="drProjectTable">
                <thead>
                    <tr>
                        <th style="width:52px;">#</th>
                        <th>Project</th>
                        <th>Total</th>
                        <th>Pending</th>
                        <th>In Progress</th>
                        <th>On Hold</th>
                        <th>Testing</th>
                        <th>Completed</th>
                        <th>Cancelled</th>
                        <th>Overdue</th>
                        <th>Delayed</th>
                        <th>Completion</th>
                    </tr>
                </thead>
                <tbody id="drProjectTbody">
                    <tr>
                        <td colspan="12">
                            <div class="dr-empty">
                                <i class='bx bx-loader-circle bx-spin'></i>
                                Loading projects...
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="dr-pagination-bar">
            <small class="dr-page-info" id="drProjectPageInfo">—</small>
            <nav><ul id="drProjectPagination"></ul></nav>
        </div>
    </div>

    {{-- ==================== TASK DETAIL (AJAX PAGINATED) ==================== --}}
    <div class="dr-card mb-0">
        <div class="dr-card-head">
            <h5><i class='bx bx-list-check'></i> Task Detail</h5>
            <div class="dr-table-toolbar">
                <div class="dr-search-wrap">
                    <i class='bx bx-search'></i>
                    <input type="text" id="drTaskSearch" placeholder="Search title or project...">
                </div>
                <span class="dr-chip active" data-filter="all">All</span>
                <span class="dr-chip" data-filter="delayed">Delayed</span>
                <span class="dr-chip" data-filter="overdue">Overdue</span>
                <span class="dr-chip" data-filter="not_due_yet">Not Due Yet</span>
                <span class="dr-chip" data-filter="on_time">On Time</span>
            </div>
        </div>

        <div class="table-responsive text-nowrap dr-table-loading" id="drTableWrap">
            <table class="table table-striped mb-0" id="drTaskTable">
                <thead>
                    <tr>
                        <th style="width:52px;">#</th>
                        <th class="sortable" data-key="title">Title <i class='bx bx-sort-alt-2'></i></th>
                        <th class="sortable" data-key="project">Project <i class='bx bx-sort-alt-2'></i></th>
                        <th class="sortable" data-key="due">Due Date <i class='bx bx-sort-alt-2'></i></th>
                        <th>Completed</th>
                        <th>Status</th>
                        <th class="sortable" data-key="delay">Delay <i class='bx bx-sort-alt-2'></i></th>
                    </tr>
                </thead>
                <tbody id="drTaskTbody">
                    <tr>
                        <td colspan="7">
                            <div class="dr-empty">
                                <i class='bx bx-loader-circle bx-spin'></i>
                                Loading tasks...
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="dr-pagination-bar">
            <small class="dr-page-info" id="drPageInfo">—</small>
            <nav><ul id="drPagination"></ul></nav>
        </div>
    </div>
</div>

@include('layouts.footer')

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
$(function () {

    /* ============================================================
       CONFIG
       ============================================================ */
    const urlParams = new URLSearchParams(window.location.search);
    const FILTERS = {
        filter_type: urlParams.get('filter_type') || 'this_month',
        start_date:  urlParams.get('start_date')  || '',
        end_date:    urlParams.get('end_date')    || '',
    };

    const TASKS_URL    = "{{ route('my_report.tasks') }}";
    const PROJECTS_URL = "{{ route('my_report.projects') }}";

    const DELAY_LABELS = {
        on_time: 'On Time', delayed: 'Delayed', overdue: 'Overdue',
        not_due_yet: 'Not Due Yet', cancelled: 'Cancelled'
    };
    const DELAY_BADGES = {
        on_time: 'bg-success', delayed: 'bg-danger',
        overdue: 'bg-warning text-dark', not_due_yet: 'bg-info',
        cancelled: 'bg-secondary'
    };
    const DELAY_ICONS = {
        on_time: 'bx-check-circle', delayed: 'bx-error-circle',
        overdue: 'bx-time-five', not_due_yet: 'bx-hourglass',
        cancelled: 'bx-x-circle'
    };

    /* ============================================================
       HELPERS
       ============================================================ */
    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, c =>
            ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }

    function daysLate(dueDate, status) {
        if (status !== 'overdue' && status !== 'delayed') return '';
        const p = String(dueDate).split('-');
        if (p.length !== 3) return '';
        const due = new Date(+p[2], +p[1] - 1, +p[0]);
        const today = new Date(); today.setHours(0, 0, 0, 0);
        const diff = Math.round((today - due) / 86400000);
        return diff > 0 ? diff + (diff === 1 ? ' day late' : ' days late') : '';
    }

    function renderPagination($el, data) {
        const { current_page: page, last_page: lastPage, total, per_page: perPage } = data;
        const from = total === 0 ? 0 : (page - 1) * perPage + 1;
        const to   = Math.min(page * perPage, total);

        const btn = (label, target, opts = {}) => {
            const dis = opts.disabled ? 'disabled' : '';
            const act = opts.active ? 'active' : '';
            const attr = opts.disabled ? '' : `data-page="${target}"`;
            return `<li><button type="button" class="dr-page-btn ${act}" ${dis} ${attr}>${label}</button></li>`;
        };

        let html = btn(`<i class='bx bx-chevrons-left'></i>`, 1, { disabled: page <= 1 });
        html += btn(`<i class='bx bx-chevron-left'></i>`, page - 1, { disabled: page <= 1 });

        const win = 2;
        const pages = [];
        for (let p = 1; p <= lastPage; p++) {
            if (p === 1 || p === lastPage || Math.abs(p - page) <= win) pages.push(p);
        }
        let prev = 0;
        pages.forEach(p => {
            if (prev && p - prev > 1) html += `<li class="dr-page-dots">…</li>`;
            html += btn(p, p, { active: p === page });
            prev = p;
        });

        html += btn(`<i class='bx bx-chevron-right'></i>`, page + 1, { disabled: page >= lastPage });
        html += btn(`<i class='bx bx-chevrons-right'></i>`, lastPage, { disabled: page >= lastPage });

        $el.html(html);
        return { from, to };
    }

    /* ============================================================
       CHART
       ============================================================ */
    new Chart(document.getElementById('delayStatusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                data: @json($chartData),
                backgroundColor: ['#22c55e', '#ef4444', '#f59e0b', '#0ea5e9', '#94a3b8'],
                borderWidth: 2, borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '68%',
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } } }
        }
    });

    /* ============================================================
       FILTER FORM
       ============================================================ */
    $('#filterType').on('change', function () {
        const isCustom = this.value === 'custom';
        $('#startDateBox, #endDateBox').toggleClass('d-none', !isCustom);
    });
    $('#drFilterForm').on('submit', function () {
        $('#drLoadingBar').addClass('active');
    });

    /* ============================================================
       PROJECTS — AJAX PAGINATION
       ============================================================ */
    const projectState = { page: 1, perPage: 5, search: '', loading: false };

    function loadProjects() {
        if (projectState.loading) return;
        projectState.loading = true;
        $('#drProjectWrap').addClass('is-loading');

        $.ajax({
            url: PROJECTS_URL,
            type: 'GET',
            dataType: 'json',
            data: $.extend({}, FILTERS, {
                page: projectState.page,
                per_page: projectState.perPage,
                search: projectState.search
            }),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .done(function (data) {
            const rows = data.data || [];
            const $tbody = $('#drProjectTbody');

            if (!rows.length) {
                $tbody.html(`
                    <tr><td colspan="12">
                        <div class="dr-empty">
                            <i class='bx bx-folder-open'></i>
                            No projects found
                        </div>
                    </td></tr>`);
            } else {
                const startIdx = (projectState.page - 1) * projectState.perPage;
                $tbody.html(rows.map((p, i) => `
                    <tr>
                        <td>${startIdx + i + 1}</td>
                        <td class="fw-semibold">${esc(p.name)}</td>
                        <td>${p.total}</td>
                        <td>${p.pending}</td>
                        <td>${p.in_progress}</td>
                        <td>${p.on_hold}</td>
                        <td class="text-info">${p.testing ?? 0}</td>
                        <td class="text-success">${p.completed}</td>
                        <td>${p.cancelled}</td>
                        <td class="text-danger">${p.overdue}</td>
                        <td class="text-danger">${p.delayed}</td>
                        <td style="min-width:110px;">
                            <div class="dr-progress thin mb-1">
                                <div class="dr-progress-bar" style="width: ${p.completion_rate}%"></div>
                            </div>
                            <small>${p.completion_rate}%</small>
                        </td>
                    </tr>
                `).join(''));
            }

            const { from, to } = renderPagination($('#drProjectPagination'), data);
            $('#drProjectPageInfo').html(data.total === 0
                ? 'No projects to display'
                : `Showing <strong>${from}–${to}</strong> of <strong>${data.total}</strong> projects`);
        })
        .fail(function () {
            $('#drProjectTbody').html(`
                <tr><td colspan="12">
                    <div class="dr-empty">
                        <i class='bx bx-error-circle'></i>
                        Failed to load projects. Please try again.
                    </div>
                </td></tr>`);
            $('#drProjectPageInfo').text('—');
            $('#drProjectPagination').empty();
        })
        .always(function () {
            projectState.loading = false;
            $('#drProjectWrap').removeClass('is-loading');
        });
    }

    let projectTimer;
    $('#drProjectSearch').on('input', function () {
        clearTimeout(projectTimer);
        projectTimer = setTimeout(() => {
            projectState.search = $(this).val().trim();
            projectState.page = 1;
            loadProjects();
        }, 350);
    });

    $('#drProjectPagination').on('click', 'button[data-page]', function () {
        if (this.disabled) return;
        const p = parseInt($(this).data('page'), 10);
        if (!p || p === projectState.page) return;
        projectState.page = p;
        loadProjects();
    });

  
    const taskState = {
        page: 1, perPage: 5, search: '', status: 'all',
        sortBy: '', sortDir: 'asc', loading: false
    };

    function loadTasks() {
        if (taskState.loading) return;
        taskState.loading = true;
        $('#drTableWrap').addClass('is-loading');

        $.ajax({
            url: TASKS_URL,
            type: 'GET',
            dataType: 'json',
            data: $.extend({}, FILTERS, {
                page: taskState.page,
                per_page: taskState.perPage,
                search: taskState.search,
                delay_status: taskState.status,
                sort_by: taskState.sortBy,
                sort_dir: taskState.sortDir
            }),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .done(function (data) {
            const rows = data.data || [];
            const $tbody = $('#drTaskTbody');

            if (!rows.length) {
                $tbody.html(`
                    <tr><td colspan="7">
                        <div class="dr-empty">
                            <i class='bx bx-check-shield'></i>
                            No tasks found for the selected period
                        </div>
                    </td></tr>`);
            } else {
                const startIdx = (taskState.page - 1) * taskState.perPage;
                $tbody.html(rows.map((r, i) => {
                    const late = daysLate(r.due_date, r.delay_status);
                    const rowClass =
                        r.delay_status === 'overdue' ? 'row-overdue' :
                        r.delay_status === 'delayed' ? 'row-delayed' : '';
                    const badge = DELAY_BADGES[r.delay_status] || 'bg-secondary';
                    const label = DELAY_LABELS[r.delay_status] || r.delay_status;
                    const icon  = DELAY_ICONS[r.delay_status]  || 'bx-circle';

                    return `
                        <tr class="${rowClass}">
                            <td>${startIdx + i + 1}</td>
                            <td class="fw-semibold">${esc(r.title)}</td>
                            <td>${esc(r.project)}</td>
                            <td>${esc(r.due_date)}</td>
                            <td>${esc(r.completed_at)}</td>
                            <td>${esc(r.status_label || r.status)}</td>
                            <td>
                                <span class="badge ${badge}">
                                    <i class='bx ${icon}'></i> ${label}
                                </span>
                                ${late ? `<span class="dr-days-tag d-block mt-1">${late}</span>` : ''}
                            </td>
                        </tr>`;
                }).join(''));
            }

            const { from, to } = renderPagination($('#drPagination'), data);
            $('#drPageInfo').html(data.total === 0
                ? 'No tasks to display'
                : `Showing <strong>${from}–${to}</strong> of <strong>${data.total}</strong> tasks`);
        })
        .fail(function () {
            $('#drTaskTbody').html(`
                <tr><td colspan="7">
                    <div class="dr-empty">
                        <i class='bx bx-error-circle'></i>
                        Failed to load tasks. Please try again.
                    </div>
                </td></tr>`);
            $('#drPageInfo').text('—');
            $('#drPagination').empty();
        })
        .always(function () {
            taskState.loading = false;
            $('#drTableWrap').removeClass('is-loading');
        });
    }

    let searchTimer;
    $('#drTaskSearch').on('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            taskState.search = $(this).val().trim();
            taskState.page = 1;
            loadTasks();
        }, 350);
    });

    $('.dr-chip').on('click', function () {
        $('.dr-chip').removeClass('active');
        $(this).addClass('active');
        taskState.status = $(this).data('filter') || 'all';
        taskState.page = 1;
        loadTasks();
    });

    $('#drPagination').on('click', 'button[data-page]', function () {
        if (this.disabled) return;
        const p = parseInt($(this).data('page'), 10);
        if (!p || p === taskState.page) return;
        taskState.page = p;
        loadTasks();
    });

    $('#drTaskTable th.sortable').on('click', function () {
        const key = $(this).data('key');
        if (taskState.sortBy === key) {
            taskState.sortDir = taskState.sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            taskState.sortBy = key;
            taskState.sortDir = 'asc';
        }
        taskState.page = 1;
        loadTasks();
    });

 
    $('#drExportCsvBtn').on('click', function () {
        $.ajax({
            url: TASKS_URL,
            type: 'GET',
            dataType: 'json',
            data: $.extend({}, FILTERS, {
                per_page: 10000,
                search: taskState.search,
                delay_status: taskState.status,
                sort_by: taskState.sortBy,
                sort_dir: taskState.sortDir
            })
        }).done(function (data) {
            const rows = data.data || [];
            const headers = ['#', 'Title', 'Project', 'Due Date', 'Completed', 'Status', 'Delay'];
            const lines = [headers.map(h => `"${h}"`).join(',')];

            rows.forEach((r, i) => {
                const cells = [
                    i + 1, r.title, r.project, r.due_date, r.completed_at,
                    r.status_label || r.status,
                    DELAY_LABELS[r.delay_status] || r.delay_status
                ].map(v => `"${String(v ?? '').replace(/"/g, '""')}"`);
                lines.push(cells.join(','));
            });

            const blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'my-report-' + new Date().toISOString().slice(0, 10) + '.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }).fail(function () {
            alert('Export failed. Please try again.');
        });
    });

    loadProjects();
    loadTasks();

});
</script>