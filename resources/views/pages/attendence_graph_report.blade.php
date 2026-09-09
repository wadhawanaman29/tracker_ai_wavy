@include('layouts.header')

@php
    $today = \Carbon\Carbon::today()->format('Y-m-d');
@endphp

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Reports /</span>
        Attendance Report
    </h4>

    @if ($isAdmin)

        <div class="card mb-4">

            <div class="card-body">

                <form method="GET" action="{{ route('attendence_graph_report') }}" class="row g-3 align-items-end">

                    {{-- EMPLOYEE --}}
                    <div class="col-md-3 col-sm-6">

                        <label class="form-label fw-semibold">
                            Employee
                        </label>

                        <select name="user_id" class="form-select">

                            <option value="all" {{ $selectedUserId == 'all' ? 'selected' : '' }}>

                                All Employees

                            </option>

                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ $selectedUserId == $user->id ? 'selected' : '' }}>

                                    {{ $user->name }}

                                </option>
                            @endforeach

                        </select>

                    </div>

                    {{-- MONTH FILTER --}}
                    <div class="col-md-3 col-sm-6">

                        <label class="form-label fw-semibold">
                            MONTH
                        </label>

                        <select name="filter_month" class="form-select">

                            <option value="">
                                Select
                            </option>

                            @php

                                $months = [];

                                for ($i = 0; $i < 13; $i++) {
                                    $months[] = \Carbon\Carbon::now()->subMonths($i);
                                }

                            @endphp

                            @foreach ($months as $month)
                                <option value="{{ $month->format('Y-m') }}"
                                    {{ request('filter_month') == $month->format('Y-m') ? 'selected' : '' }}>

                                    {{ $month->format('F Y') }}

                                </option>
                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-2 col-sm-6">

                        <button type="submit" class="btn btn-primary w-100">

                            Apply

                        </button>

                    </div>

                    @php

                        if (request('filter_month')) {
                            $reportMonth = \Carbon\Carbon::parse(request('filter_month') . '-01')->format('m-Y');
                        } else {
                            $reportMonth = now()->format('m-Y');
                        }

                    @endphp

                    @if (request()->has('user_id') && request()->has('filter_month'))
                        <div class="col-md-2 col-sm-6"></div>

                        <div class="col-md-2 col-sm-6">

                            <div class="text-end">

                                <a href="{{ route('daily_attendence_report', [
                                    'user_id' => request('user_id', 'all'),
                                    'month' => $reportMonth,
                                ]) }}"
                                    class="view-report-btn">

                                    <i class="fa fa-eye"></i>

                                    View Report

                                </a>

                            </div>

                        </div>
                    @endif

                </form>

            </div>

        </div>

    @endif

    @if (!$isAllUsers && $reportData)

        @php

            $score = $reportData['performance_score'];

            $lateCount = $reportData['late_count'];

            $shortLeaveCount = $reportData['short_leave_count'];

            $halfDayCount = $reportData['half_day_count'];

            $absentCount = $reportData['absent_count'];

            $sandwichCount = $reportData['sandwich_count'] ?? 0;

            if ($score >= 90) {
                $scoreBadgeClass = 'bg-success';

                $scoreLabel = 'Outstanding ⭐';
            } elseif ($score >= 80) {
                $scoreBadgeClass = 'bg-primary';

                $scoreLabel = 'Good 👍';
            } elseif ($score >= 60) {
                $scoreBadgeClass = 'bg-warning text-dark';

                $scoreLabel = 'Average ⚠️';
            } else {
                $scoreBadgeClass = 'bg-danger';

                $scoreLabel = 'Poor 🚨';
            }

        @endphp
        <div class="row g-4 mb-4 attendance-summary-wrapper">

            {{-- WORKING DAYS --}}
            <div class="col">
                <div class="attendance-card working-card">



                    <p class="attendance-title">
                        W. Days
                    </p>

                    <h2 class="attendance-count">

                        {{ $reportData['working_days'] }}

                    </h2>

                    <span class="attendance-bottom-text">
                        Total Month Days
                    </span>

                </div>
            </div>

            {{-- ON TIME --}}
            <div class="col">
                <div class="attendance-card success-card">



                    <p class="attendance-title">
                        On Time
                    </p>

                    <h2 class="attendance-count text-color">

                        {{ $reportData['ontime_count'] }}

                    </h2>

                    <span class="attendance-bottom-text success-text">
                        Excellent
                    </span>

                </div>
            </div>

            {{-- LATE --}}
            <div class="col">
                <div
                    class="attendance-card
            {{ $lateCount > 3 ? 'danger-card' : ($lateCount > 0 ? 'warning-card' : 'success-card') }}">



                    <p class="attendance-title">
                        Late
                    </p>

                    <h2 class="attendance-count text-warning">

                        {{ $lateCount }}

                    </h2>

                    <span class="attendance-bottom-text warning-text">

                        Allowed:
                        {{ $reportData['allowed_late'] }}

                    </span>

                </div>
            </div>

            {{-- SHORT LEAVE --}}
            <div class="col">
                <div
                    class="attendance-card
            {{ $shortLeaveCount > 2 ? 'danger-card' : ($shortLeaveCount > 0 ? 'warning-card' : 'success-card') }}">



                    <p class="attendance-title">
                        S.Leave
                    </p>

                    <h2 class="attendance-count text-info">

                        {{ $shortLeaveCount }}

                    </h2>

                    <span class="attendance-bottom-text info-text">

                        Allowed:
                        {{ $reportData['allowed_short_leave'] }}

                    </span>

                </div>
            </div>

            {{-- HALF DAY --}}
            <div class="col">
                <div
                    class="attendance-card
            {{ $halfDayCount > 1 ? 'danger-card' : ($halfDayCount > 0 ? 'warning-card' : 'success-card') }}">



                    <p class="attendance-title">
                        H. Day
                    </p>

                    <h2 class="attendance-count text-danger">

                        {{ $halfDayCount }}

                    </h2>

                    <span class="attendance-bottom-text danger-text">

                        Allowed:
                        {{ $reportData['allowed_half_day'] }}

                    </span>

                </div>
            </div>

            {{-- ABSENT --}}
            <div class="col">
                <div
                    class="attendance-card
            {{ $absentCount > 1 ? 'danger-card' : ($absentCount > 0 ? 'warning-card' : 'success-card') }}">



                    <p class="attendance-title">
                        Absents
                    </p>

                    <h2 class="attendance-count text-danger-absentCount">

                        {{ $absentCount }}

                    </h2>

                    <span class="attendance-bottom-text danger-text">

                        Allowed:
                        {{ $reportData['allowed_absent'] }}

                    </span>

                </div>
            </div>

            {{-- SANDWICH --}}
            <div class="col">
                <div
                    class="attendance-card sandwich-card
            {{ $sandwichCount > 0 ? 'danger-card' : 'success-card' }}">



                    <p class="attendance-title">
                        Sandwich
                    </p>

                    <h2 class="attendance-count sandwich-text">

                        {{ $sandwichCount }}

                    </h2>

                    @if ($sandwichCount > 0)
                        <span class="attendance-bottom-text danger-text">

                            Weekend Converted

                        </span>
                    @else
                        <span class="attendance-bottom-text success-text">

                            No Sandwich Leave

                        </span>
                    @endif

                </div>
            </div>

            {{-- ACTIVE --}}
            <div class="col">
                <div class="attendance-card active-card">



                    <p class="attendance-title">
                        Active
                    </p>

                    <h2 class="attendance-count active-text">

                        {{ $reportData['total_active'] }}

                    </h2>

                    <span class="attendance-bottom-text success-text">
                        Productive Time
                    </span>

                </div>
            </div>

            {{-- INACTIVE --}}
            <div class="col">
                <div class="attendance-card inactive-card">



                    <p class="attendance-title">
                        Inactive
                    </p>

                    <h2 class="attendance-count inactive-text">

                        {{ $reportData['total_inactive'] }}

                    </h2>

                    <span class="attendance-bottom-text danger-text">
                        Idle Time
                    </span>

                </div>
            </div>

            {{-- PERFORMANCE --}}
            <div class="col">
                <div class="attendance-card performance-card">



                    <p class="attendance-title">
                        Perf.
                    </p>

                    <h2 class="attendance-count">

                        {{ round($reportData['performance_score']) }}%

                    </h2>

                    <span class="performance-badge ">

                        {{ $scoreLabel }}

                    </span>

                </div>
            </div>

        </div>

        {{-- CHARTS --}}
        <div class="row mb-4">

            {{-- STATUS CHART --}}
            <div class="col-md-6 mb-3">

                <div class="card h-100">

                    <div class="card-header">

                        <h6 class="mb-0">
                            Attendance Status
                        </h6>

                    </div>

                    <div class="card-body">

                        <div style="position:relative;height:260px;">

                            <canvas id="statusChart"></canvas>

                        </div>

                    </div>

                </div>

            </div>

            {{-- ACTIVE / INACTIVE --}}
            <div class="col-md-6 mb-3">

                <div class="card h-100">

                    <div class="card-header">

                        <h6 class="mb-0">
                            Active | Inactive Time
                        </h6>

                    </div>

                    <div class="card-body">

                        @if ($reportData['total_active_sec'] == 0 && $reportData['total_inactive_sec'] == 0)
                            <div class="d-flex justify-content-center align-items-center" style="height:260px;">

                                <p class="text-muted mb-0">
                                    No result found
                                </p>

                            </div>
                        @else
                            <div style="position:relative;height:260px;">

                                <canvas id="pieChart"></canvas>

                            </div>
                        @endif

                    </div>

                </div>

            </div>

        </div>
    @elseif($isAllUsers && $isAdmin && count($allData) > 0)
        <div class="card">

            <div class="card-header">

                <h5 class="mb-0">
                    All Employees Summary
                </h5>

            </div>

            <div class="table-responsive text-nowrap">

                <table class="table table-hover">

                    <thead class="table-light">

                        <tr>

                            <th>#</th>

                            <th>Employee</th>

                            <th>W. Days</th>

                            <th>On Time</th>

                            <th>Late</th>

                            <th>S. Leave</th>

                            <th>H. Day</th>

                            <th>Absents</th>

                            <th>Sandwich</th>

                            <th>Perf.</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($allData as $i => $d)
                            @php

                                $s = $d['performance_score'];

                                $badgeCls = $s >= 90 ? 'bg-success' : ($s >= 70 ? 'bg-warning' : 'bg-danger');

                            @endphp

                            <tr>

                                <td>
                                    {{ $i + 1 }}
                                </td>

                                <td>
                                    {{ $d['user']->name }}
                                </td>

                                <td>
                                    {{ $d['working_days'] }}
                                </td>

                                <td class="text-success fw-semibold">
                                    {{ $d['ontime_count'] }}
                                </td>

                                <td class="text-warning fw-semibold">
                                    {{ $d['late_count'] }}
                                </td>

                                <td class="text-info fw-semibold">
                                    {{ $d['short_leave_count'] }}
                                </td>

                                <td class="text-danger fw-semibold">
                                    {{ $d['half_day_count'] }}
                                </td>

                                <td class="text-danger fw-semibold">
                                    {{ $d['absent_count'] }}
                                </td>

                                <td style="color:#7c3aed;" class="fw-semibold">
                                    {{ $d['sandwich_count'] ?? 0 }}
                                </td>

                                <td>

                                    <span class="badge {{ $badgeCls }}">

                                        {{ $s }}%

                                    </span>

                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>
    @else
        <div class="alert alert-info">

            No attendance data found for the selected period.

        </div>

    @endif
    {{-- SANDWICH DETAILS --}}
    @if (
        ($reportData['sandwich_count'] ?? 0) > 0 &&
            (!$isAdmin || (request()->filled('filter_month') && request('user_id') != 'all')))

        <div class="card sandwich-main-card border-0 shadow-lg mb-4 overflow-hidden">

            <!-- Header -->
            <div class="card-header sandwich-header d-flex justify-content-between align-items-center flex-wrap">

                <div class="d-flex align-items-center">

                    <div class="sandwich-header-icon me-3">
                        <i class="fa fa-layer-group"></i>
                    </div>

                    <div>

                        <h4 class="mb-0 fw-bold text-black-design">
                            Sandwich Leave Details
                        </h4>

                        <small class="text-black-design">
                            Weekend + Leave Combination Tracking
                        </small>

                    </div>

                </div>

                <div class="sandwich-total-badge mt-2 mt-md-0">

                    {{ $reportData['sandwich_count'] }} Total Days

                </div>

            </div>

            <!-- Body -->
            <div class="card-body p-4">

                @foreach ($reportData['sandwich_details'] as $index => $sandwich)
                    <div class="sandwich-timeline-card mb-4">

                        <!-- Top -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">

                            <div>

                                <h5 class="fw-bold mb-1 text-dark">

                                    Sandwich #{{ $index + 1 }}

                                </h5>



                            </div>

                            <div class="sandwich-days-badge">

                                {{ $sandwich['total_days'] }} Days Sandwich

                            </div>

                        </div>

                        <!-- Timeline -->
                        <div class="sandwich-timeline">

                            @foreach ($sandwich['dates'] as $i => $date)
                                @php

                                    $carbonDate = \Carbon\Carbon::parse($date);

                                    $isWeekend = $carbonDate->isWeekend();

                                @endphp

                                <div class="timeline-item">

                                    <div class="timeline-card {{ $isWeekend ? 'weekend-card' : 'leave-card' }}">

                                        <!-- Circle -->
                                        <div class="timeline-circle">

                                            @if ($isWeekend)
                                                <i class="fa fa-calendar"></i>
                                            @else
                                                <i class="fa fa-user-times"></i>
                                            @endif

                                        </div>

                                        <!-- Day -->
                                        <h6 class="fw-bold mb-1">

                                            {{ $carbonDate->format('l') }}

                                        </h6>

                                        <!-- Date -->
                                        <div class="timeline-date">

                                            {{ $carbonDate->format('d M Y') }}

                                        </div>

                                        <!-- Badge -->
                                        <div class="mt-3">

                                            @if ($isWeekend)
                                                <span class="timeline-badge weekend">

                                                    Weekend

                                                </span>
                                            @else
                                                <span class="timeline-badge leave">

                                                    Sandwich Leave

                                                </span>
                                            @endif

                                        </div>

                                    </div>

                                    @if (!$loop->last)
                                        <div class="timeline-arrow">

                                            <i class="fa fa-arrow-right"></i>

                                        </div>
                                    @endif

                                </div>
                            @endforeach

                        </div>

                        <!-- Result -->
                        <div class="sandwich-result-box mt-4">

                            <div class="d-flex align-items-center">

                                <div class="result-icon me-3">

                                    <i class="fa fa-exclamation-triangle"></i>

                                </div>

                                <div>

                                    <strong>
                                        Result:
                                    </strong>

                                    Total
                                    <strong>{{ $sandwich['total_days'] }} Days</strong>

                                    counted as

                                    <strong>Sandwich Leave</strong>.

                                </div>

                            </div>

                        </div>

                    </div>
                @endforeach

            </div>

        </div>

    @endif

</div>

@if (!$isAllUsers && $reportData)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>

    <script>
        const chartLabels = @json($reportData['chart_labels']);

        const chartStatusData = @json($reportData['chart_status_data']);

        const total_active = @json($reportData['total_active_sec']);

        const total_inactive = @json($reportData['total_inactive_sec']);

        // STATUS CHART

        const ctxStatus = document
            .getElementById('statusChart')
            .getContext('2d');

        new Chart(ctxStatus, {

            type: 'doughnut',

            data: {

                labels: chartLabels,

                datasets: [{

                    data: chartStatusData,

             backgroundColor: [
    '#22c55e',
    '#f59e0b',
    '#0ea5e9',
    '#dc2626',
    '#7c3aed',
    '#1586ff' // Sandwich Brown
],

                    borderWidth: 1

                }]
            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {
                        position: 'bottom'
                    },

                    tooltip: {

                        callbacks: {

                            label: function(context) {

                                return context.label + ': ' + context.raw;
                            }
                        }
                    }
                }
            }
        });

        // ACTIVE / INACTIVE CHART

        const ctx = document
            .getElementById('pieChart')
            .getContext('2d');

        new Chart(ctx, {

            type: 'doughnut',

            data: {

                labels: [
                    'Active Time',
                    'Inactive Time'
                ],

                datasets: [{

                    data: [
                        total_active,
                        total_inactive
                    ],

                    backgroundColor: [
                        '#1cd657',
                        '#FD4069'
                    ],

                    borderWidth: 1

                }]
            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    tooltip: {

                        callbacks: {

                            label: function(context) {

                                function formatSeconds(seconds) {

                                    let h = Math.floor(seconds / 3600);

                                    let m = Math.floor(
                                        (seconds % 3600) / 60
                                    );

                                    let s = seconds % 60;

                                    return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
                                }

                                return context.label + ': ' +
                                    formatSeconds(context.raw);
                            }
                        }
                    },

                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endif

@include('layouts.footer')
