@include('layouts.header')

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
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Task Management /</span>
        My Report
    </h4>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('my_report') }}" class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label fw-semibold">Range</label>
                    <select name="filter_type" id="filterType" class="form-select">
                        <option value="this_month" {{ $filterType == 'this_month' ? 'selected' : '' }}>This Month
                        </option>
                        <option value="last_month" {{ $filterType == 'last_month' ? 'selected' : '' }}>Last Month
                        </option>
                        <option value="this_year" {{ $filterType == 'this_year' ? 'selected' : '' }}>This Year
                        </option>
                        <option value="last_year" {{ $filterType == 'last_year' ? 'selected' : '' }}>Last Year
                        </option>
                        <option value="custom" {{ $filterType == 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6 {{ $filterType == 'custom' ? '' : 'd-none' }}" id="startDateBox">
                    <label class="form-label fw-semibold">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>

                <div class="col-md-3 col-sm-6 {{ $filterType == 'custom' ? '' : 'd-none' }}" id="endDateBox">
                    <label class="form-label fw-semibold">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>

                <div class="col-md-2 col-sm-6">
                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4 attendance-summary-wrapper">
        <div class="col">
            <div class="attendance-card working-card">
                <p class="attendance-title">Total</p>
                <h2 class="attendance-count">{{ $totalTasks }}</h2>
                <span class="attendance-bottom-text">Assigned Tasks</span>
            </div>
        </div>

        <div class="col">
            <div class="attendance-card success-card">
                <p class="attendance-title">On Time</p>
                <h2 class="attendance-count text-color">{{ $counts['on_time'] }}</h2>
                <span class="attendance-bottom-text success-text">Delivered on time</span>
            </div>
        </div>

        <div class="col">
            <div class="attendance-card {{ $counts['delayed'] > 0 ? 'danger-card' : 'success-card' }}">
                <p class="attendance-title">Delayed</p>
                <h2 class="attendance-count text-danger">{{ $counts['delayed'] }}</h2>
                <span class="attendance-bottom-text danger-text">Completed after due date</span>
            </div>
        </div>

        <div class="col">
            <div class="attendance-card {{ $counts['overdue'] > 0 ? 'danger-card' : 'success-card' }}">
                <p class="attendance-title">Overdue</p>
                <h2 class="attendance-count text-danger">{{ $counts['overdue'] }}</h2>
                <span class="attendance-bottom-text danger-text">Still open, past due date</span>
            </div>
        </div>

        <div class="col">
            <div class="attendance-card performance-card">
                <p class="attendance-title">Completion</p>
                <h2 class="attendance-count">{{ $completionRate }}%</h2>
                <span class="attendance-bottom-text">Completed of total</span>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bx bx-pie-chart-alt-2"></i> Status Breakdown</h6>
                </div>
                <div class="card-body">
                    <div style="position:relative;height:260px;">
                        <canvas id="delayStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (count($projectSummary) > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bx bx-briefcase"></i> Projects</h5>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
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
                    <tbody>
                        @foreach ($projectSummary as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p['name'] }}</td>
                                <td>{{ $p['total'] }}</td>
                                <td>{{ $p['pending'] }}</td>
                                <td>{{ $p['in_progress'] }}</td>
                                <td>{{ $p['on_hold'] }}</td>
                                <td class="text-info">{{ $p['testing'] }}</td>
                                <td class="text-success">{{ $p['completed'] }}</td>
                                <td>{{ $p['cancelled'] }}</td>
                                <td class="text-danger">{{ $p['overdue'] }}</td>
                                <td class="text-danger">{{ $p['delayed'] }}</td>
                                <td style="min-width:100px;">
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar bg-success"
                                            style="width: {{ $p['completion_rate'] }}%"></div>
                                    </div>
                                    <small>{{ $p['completion_rate'] }}%</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="card">
        <h5 class="card-header"><i class="bx bx-list-check"></i> Task Detail</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Project</th>
                        <th>Due Date</th>
                        <th>Completed</th>
                        <th>Status</th>
                        <th>Delay</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $i => $row)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $row['title'] }}</td>
                            <td>{{ $row['project'] }}</td>
                            <td>{{ $row['due_date'] }}</td>
                            <td>{{ $row['completed_at'] }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $row['status'])) }}</td>
                            <td><span class="badge {{ $delayBadge[$row['delay_status']] }}">{{ $delayLabel[$row['delay_status']] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No tasks found for the selected period</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('layouts.footer')

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
    const delayChartLabels = @json($chartLabels);
    const delayChartData = @json($chartData);

    new Chart(document.getElementById('delayStatusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: delayChartLabels,
            datasets: [{
                data: delayChartData,
                backgroundColor: ['#22c55e', '#dc2626', '#f59e0b', '#0ea5e9', '#64748b'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
