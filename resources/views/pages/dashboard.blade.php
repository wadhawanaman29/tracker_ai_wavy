<!-- <div id="data-container">
-->
@include('layouts.header')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="d-flex align-items-start row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Welcome {{ Auth::user()->name }}! 🎉</h5>
                            {{-- <p class="mb-4">
                                You have assigned <span class="fw-bold">72%</span> more sales today. Check your new badge
                                in
                                your profile.
                            </p> --}}

                            @if (Auth::user()->user_type == '1')
                                <a href="{{ route('assignment_list') }}" class="btn btn-sm btn-outline-primary">View Your
                                    assinment</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="../assets/img/illustrations/man-with-laptop-light.png" height="140"
                                alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                data-app-light-img="illustrations/man-with-laptop-light.png" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (Auth::user()->user_type == '1')

            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="../assets/img/icons/unicons/chart-success.png" alt="chart success"
                                    class="rounded">
                            </div>
                            <div class="dropdown">
                                <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                    <a class="dropdown-item" href="{{ route('assignment_list') }}">View More</a>
                                </div>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total hours in this week</span>
                        <h6 class="card-title mb-2">
                            @php
                                $hourcount = getWeekHourCount();
                                if (isset($hourcount[0]['total_hours'])) {
                                    $givenHours = floor($hourcount[0]['total_hours']);
                                    $hours = intdiv($givenHours, 60);
                                    $minutes = $givenHours % 60;
                                } else {
                                    $hours = 0;
                                    $minutes = 0;
                                }
                            @endphp

                            @if (isset($hourcount[0]['total_hours']))
                                {{ $hours . ' H' . ($minutes > 0 ? ' ' . $minutes . ' M' : '') }} / 40 Hours
                            @else
                                0/40
                            @endif

                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="../assets/img/icons/unicons/chart-success.png" alt="chart success"
                                    class="rounded">
                            </div>
                            <div class="dropdown">
                                <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                    <a class="dropdown-item" href="{{ route('assignment_list') }}">View More</a>
                                </div>
                            </div>

                        </div>
                        <span class="fw-semibold d-block mb-1">Total hours in this month</span>

                        <h6 class="card-title mb-2">
                            @php
                                $monthCount = getMonthHourCount();

                                if (isset($monthCount[0]['total_hours'])) {
                                    $totalHours = floor($monthCount[0]['total_hours']);
                                    $hours = intdiv($totalHours, 60);
                                    $minutes = $totalHours % 60;
                                } else {
                                    $hours = 0;
                                    $minutes = 0;
                                }
                            @endphp
                            @if (isset($monthCount[0]['total_hours']))
                                {{ $hours . ' H' . ($minutes > 0 ? ' ' . $minutes . ' M' : '') }} / 180 Hours
                            @else
                                0/180
                            @endif

                        </h6>
                    </div>
                </div>
            </div>

        @endif
    </div>


    @if (Auth::user()->user_type == '1')
        <br><br>
        <div class="row mb-4">
            <!-- Basic Alerts -->
            <div class="col-md mb-4 mb-md-0">
                <div class="card">
                    <h5 class="card-header">All Notifications</h5>
                    <div class="card-body">
                        @if ($deposits->isEmpty())
                            <div class="alert alert-info" role="alert">There is no notification.</div>
                        @else
                            @foreach ($deposits as $notification)
                                <div class="alert alert-primary" role="alert">{{ $notification->notification }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!--/ Basic Alerts -->
            <!-- Dismissible Alerts -->
            <div class="col-md">
                <div class="card">
                    <h5 class="card-header">Holiday List</h5>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Holiday Name</th>
                                    <th>Date</th>
                                    <th>Day</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @if ($holidays->isEmpty())
                                    <tr class="text-center">
                                        <td colspan="2">No holiday found!</td>
                                    </tr>
                                @else
                                    @foreach ($holidays as $index => $holiday)
                                        <tr>
                                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i>
                                                <strong>{{ $holiday->name }}</strong>
                                            </td>
                                            <td>{{ date('d M Y', strtotime($holiday->date)) }}</td>
                                            <td>{{ date('l', strtotime($holiday->date)) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--/ Dismissible Alerts -->
        </div>
    @endif
    @if (Auth::user()->user_type == '0')

        <div class="row justify-content-end text-end mt-0">

            <form id="filter-form">
                <meta name="csrf-token" content="{{ csrf_token() }}" />
                @csrf <!-- CSRF protection -->

                <label for="calendar-date">Select Date:</label>
                @php
                    $selectedDate = isset($_REQUEST['selected_date'])
                        ? $_REQUEST['selected_date']
                        : (old('selected_date')
                            ? old('selected_date')
                            : date('Y-m-d'));
                @endphp
                <input type="date" id="calendar-date" value="{{ $selectedDate }}" name="selected_date"
                    max="{{ date('Y-m-d') }}">
                <input class="btn btn-primary" type="submit" value="Submit">

            </form>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
          <script>
                jQuery(document).ready(function($) {
                    fetchDashboardData();
                    $('#filter-form').on('submit', function(event) {
                        event.preventDefault();
                        fetchDashboardData();
                    });
                    function fetchDashboardData() {
                        var selectedDate = $('#calendar-date').val();

                        $.ajax({
                            url: "{{ route('dashboarddata') }}",
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                selected_date: selectedDate
                            },
                            success: function(response) {
                                var dataContainer = $('#data-container');
                                dataContainer.empty();

                                var html = '<div class="row mt-3">';

                                response.forEach(function(userData, index) {
                                    var totalProjectMinutes = userData.weeks.reduce(function(acc,
                                        weekData) {
                                        return acc + (weekData.total_hours || 0);
                                    }, 0);

                                    var totalAttendanceMinutes = userData.weeks.reduce(function(acc,
                                        weekData) {
                                        return acc + (weekData.total_hours_attendance || 0);
                                    }, 0);

                                    var projectHours = Math.floor(totalProjectMinutes / 60);
                                    var projectMinutes = totalProjectMinutes % 60;

                                    var attendanceHours = Math.floor(totalAttendanceMinutes / 60);
                                    var attendanceMinutes = totalAttendanceMinutes % 60;

                                    var userName = userData.user_name;
                                    var userId = userData.user_id;

                                    html += `
                        <div class="col-md-6 col-lg-4 col-xl-6 order-0 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                                    <div class="card-title mb-0">
                                        <small class="text-muted">Employee Name: ${userName}</small>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="/report/${userId}">View Report</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3" style="position: relative;">
                                        <div class="d-flex flex-column align-items-start gap-1 mt-4">
                                            <h5>Project</h5>
                                            <h5>40 Hours</h5>
                                            <span class="mb-2 fw-semibold">
                                                Spend Time: ${projectHours} H${projectMinutes > 0 ? ' ' + projectMinutes + ' M' : ''}
                                            </span>
                                            <div id="projectChart-${index}" class="d-flex flex-column align-items-end gap-1 mt-4"></div>
                                        </div>
                                        <div class="d-flex flex-column align-items-end gap-1 mt-4">
                                            <h5>Working</h5>
                                            <h5 class="mb-2">40 Hours</h5>
                                            <span class="mb-2 fw-semibold">
                                                Spend Time: ${attendanceHours} H${attendanceMinutes > 0 ? ' ' + attendanceMinutes + ' M' : ''}
                                            </span>
                                            <div id="attendanceChart-${index}" class="d-flex flex-column align-items-end gap-1 mt-4"></div>
                                        </div>
                                    </div>
                                    <ul class="p-0 m-0">`;

                                    userData.weeks.forEach(function(weekData) {
                                        html += `
                            <li class="d-flex mb-4 pb-1">
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">Current Week: (${formatDate(weekData.start_of_week)} - ${formatDate(weekData.end_of_week)})</h6><br>
                                        <ul class="p-0 m-0 mt-2">`;

                                        ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday',
                                            'Saturday'
                                        ].forEach(function(day) {
                                            var projectTime = weekData.days_project[
                                                day] || 0;
                                            var attendanceTime = weekData
                                                .days_attendance[day] || 0;

                                            var projectHr = Math.floor(projectTime /
                                            60);
                                            var projectMin = projectTime % 60;

                                            var attendHr = Math.floor(attendanceTime /
                                                60);
                                            var attendMin = attendanceTime % 60;

                                            html += `
                                <li class="d-flex mb-2 pb-1 align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-${getColorClass(day)}">${day.charAt(0)}</span>
                                    </div>
                                    <div class="me-5" style="width: 100px;">
                                        <h6 class="mb-0">${day}</h6>
                                    </div>
                                    <div class="user-progress me-5" style="min-width: 140px;">
                                        <label class="text-muted d-block mb-0">Project Time</label>
                                        <small class="fw-semibold">${projectHr}h ${projectMin}m</small>
                                    </div>
                                    <div class="user-progress" style="min-width: 140px;">
                                        <label class="text-muted d-block mb-0">Attendance Time</label>
                                        <small class="fw-semibold">${attendHr} H${attendMin > 0 ? ' ' + attendMin + ' M' : ''}</small>
                                    </div>
                                </li>`;
                                        });

                                        html += `</ul></div></div></li>`;
                                    });

                                    html += `</ul></div></div></div>`;
                                });

                                html += '</div>';
                                dataContainer.html(html);

                                // Render charts
                                response.forEach(function(userData, index) {
                                    var totalProjectMinutes = userData.weeks.reduce(function(acc,
                                        weekData) {
                                        return acc + (weekData.total_hours || 0);
                                    }, 0);
                                    var totalAttendanceMinutes = userData.weeks.reduce(function(acc,
                                        weekData) {
                                        return acc + (weekData.total_hours_attendance || 0);
                                    }, 0);

                                    var totalProjectHours = Math.floor(totalProjectMinutes / 60);
                                    var totalAttendanceHours = Math.floor(totalAttendanceMinutes / 60);

                                    var projectChartElement = document.querySelector('#projectChart-' +
                                        index);
                                    var attendanceChartElement = document.querySelector(
                                        '#attendanceChart-' + index);

                                    if (projectChartElement) {
                                        var projectChart = new ApexCharts(projectChartElement, {
                                            chart: {
                                                height: 165,
                                                width: 130,
                                                type: 'donut'
                                            },
                                            labels: ['Working Hours', 'Pending Hours'],
                                            series: [totalProjectHours, Math.max(40 -
                                                totalProjectHours, 0)],
                                            colors: ['#007bff', '#6c757d'],
                                            stroke: {
                                                width: 5,
                                                colors: '#fff'
                                            },
                                            dataLabels: {
                                                enabled: false
                                            },
                                            legend: {
                                                show: false
                                            },
                                            grid: {
                                                padding: {
                                                    top: 0,
                                                    bottom: 0,
                                                    right: 15
                                                }
                                            },
                                            plotOptions: {
                                                pie: {
                                                    donut: {
                                                        size: '75%',
                                                        labels: {
                                                            show: true,
                                                            value: {
                                                                fontSize: '1.5rem',
                                                                fontFamily: 'Public Sans',
                                                                color: '#333',
                                                                offsetY: -15,
                                                                formatter: val => val + '%'
                                                            },
                                                            name: {
                                                                offsetY: 20,
                                                                fontFamily: 'Public Sans'
                                                            },
                                                            total: {
                                                                show: true,
                                                                fontSize: '0.8125rem',
                                                                color: '#666',
                                                                label: 'Total Hours',
                                                                formatter: () => ((
                                                                        totalProjectHours /
                                                                        40) * 100).toFixed(0) +
                                                                    '%'
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                        projectChart.render();
                                    }

                                    if (attendanceChartElement) {
                                        var attendanceChart = new ApexCharts(attendanceChartElement, {
                                            chart: {
                                                height: 165,
                                                width: 130,
                                                type: 'donut'
                                            },
                                            labels: ['Attendance Hours', 'Pending Hours'],
                                            series: [totalAttendanceHours, Math.max(40 -
                                                totalAttendanceHours, 0)],
                                            colors: ['#28a745', '#6c757d'],
                                            stroke: {
                                                width: 5,
                                                colors: '#fff'
                                            },
                                            dataLabels: {
                                                enabled: false
                                            },
                                            legend: {
                                                show: false
                                            },
                                            grid: {
                                                padding: {
                                                    top: 0,
                                                    bottom: 0,
                                                    right: 15
                                                }
                                            },
                                            plotOptions: {
                                                pie: {
                                                    donut: {
                                                        size: '75%',
                                                        labels: {
                                                            show: true,
                                                            value: {
                                                                fontSize: '1.5rem',
                                                                fontFamily: 'Public Sans',
                                                                color: '#333',
                                                                offsetY: -15,
                                                                formatter: val => val + '%'
                                                            },
                                                            name: {
                                                                offsetY: 20,
                                                                fontFamily: 'Public Sans'
                                                            },
                                                            total: {
                                                                show: true,
                                                                fontSize: '0.8125rem',
                                                                color: '#666',
                                                                label: 'Total Hours',
                                                                formatter: () => ((
                                                                        totalAttendanceHours /
                                                                        40) * 100).toFixed(0) +
                                                                    '%'
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                        attendanceChart.render();
                                    }
                                });
                            },
                            error: function(xhr, status, error) {
                                $('#data-container').html(
                                    '<div class="alert alert-danger">An error occurred while fetching data. Please try again later.</div>'
                                    );
                                console.error('Error:', status, error);
                            }
                        });
                    }

                    function formatDate(date) {
                        return new Date(date).toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                    }

                    function getColorClass(day) {
                        switch (day) {
                            case 'Monday':
                                return 'primary';
                            case 'Tuesday':
                                return 'success';
                            case 'Wednesday':
                                return 'warning';
                            case 'Thursday':
                                return 'secondary';
                            case 'Friday':
                                return 'danger';
                            case 'Saturday':
                                return 'info';
                            default:
                                return 'dark';
                        }
                    }
                });
            </script>


        </div>

        <div id="data-container">
            <div class="row mt-3">
                @php
                    $totalHours = 0;
                    foreach ($data as $userData) {
                        foreach ($userData['weeks'] as $weekData) {
                            $totalHours += $weekData['total_hours'];
                        }
                    }
                @endphp
                @php
                    function getColorClass($day)
                    {
                        switch ($day) {
                            case 'Monday':
                                return 'primary';
                            case 'Tuesday':
                                return 'success';
                            case 'Wednesday':
                                return 'info';
                            case 'Thursday':
                                return 'warning';
                            case 'Friday':
                                return 'danger';
                            case 'Saturday':
                                return 'secondary';
                            default:
                                return 'dark';
                        }
                    }
                @endphp
                @foreach ($data as $index => $userData)
                    @php
                        $user_name = check_user_name($userData['user_id']);
                    @endphp
                    <div class="col-md-6 col-lg-4 col-xl-6 order-0 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                                <div class="card-title mb-0">
                                    <small class="text-muted">Employee Name: {{ $user_name }}</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="userStatistics"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userStatistics">
                                        <a class="dropdown-item"
                                            href="{{ route('report', ['user_id' => $userData['user_id']]) }}">View
                                            Report</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3"
                                    style="position: relative;">
                                    <div class="d-flex flex-column align-items-start gap-1 mt-4">
                                        @php
                                            $attendanceTotal = 0;
                                            foreach ($userData['weeks'][0]['days_attendance'] as $day => $value) {
                                                $attendanceTotal += $value;
                                            }
                                            $attendanceHours = intdiv($attendanceTotal, 60);
                                            $attendanceMinutes = $attendanceTotal % 60;
                                            $givenMinutes = $userData['weeks'][0]['total_hours'] ?? 0;
                                            $projectTotalHours = intdiv($givenMinutes, 60);
                                            $projectRemainingMinutes = $givenMinutes % 60;
                                        @endphp
                                        <h5>Project</h5>
                                        <h5 class="mb-2">40 Hours</h5>
                                        <p>
                                            Spend Hours:
                                            <strong>
                                                {{ $projectTotalHours }}
                                                H{{ $projectRemainingMinutes > 0 ? ' ' . $projectRemainingMinutes . ' M' : '' }}
                                            </strong>
                                        </p>
                                    </div>
                                    <div id="orderStatisticsChart-{{ $index }}"
                                        class="d-flex flex-column align-items-end gap-1 mt-4">
                                        <h5>Working</h5>
                                        <h5 class="mb-2">40 Hours</h5>
                                        <p>
                                            Spend Hours:
                                            <strong>
                                                {{ $attendanceHours }}
                                                H{{ $attendanceMinutes > 0 ? ' ' . $attendanceMinutes . ' M' : '' }}
                                            </strong>
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div id="userStatisticsChart"></div>
                                </div>
                                <ul class="p-0 m-0">
                                    @foreach ($userData['weeks'] as $weekData)
                                        <li class="d-flex mb-4 pb-1">
                                            <div
                                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0">
                                                        Current Week :
                                                        ({{ date('d M Y', strtotime($weekData['start_of_week'])) }} -
                                                        {{ date('d M Y', strtotime($weekData['end_of_week'])) }})
                                                    </h6><br>

                                                    <ul class="p-0 m-0 mt-2">
                                                        @php
                                                            $days = [
                                                                'Monday',
                                                                'Tuesday',
                                                                'Wednesday',
                                                                'Thursday',
                                                                'Friday',
                                                                'Saturday',
                                                            ];
                                                        @endphp

                                                        @foreach ($days as $day)
                                                            <li class="d-flex mb-2 pb-1 align-items-center">
                                                                <div class="avatar flex-shrink-0 me-3">
                                                                    <span
                                                                        class="avatar-initial rounded bg-label-{{ getColorClass($day) }}">
                                                                        {{ substr($day, 0, 1) }}
                                                                    </span>
                                                                </div>
                                                                <div class="me-5" style="width: 100px;">
                                                                    <h6 class="mb-0">{{ $day }}</h6>
                                                                </div>

                                                                <div class="user-progress me-5"
                                                                    style="min-width: 140px;">
                                                                    @php
                                                                        $projectMinutes =
                                                                        $weekData['days_project'][$day] ?? 0;
                                                                        $projectHours = intdiv($projectMinutes, 60);
                                                                        $projectRemainingMinutes = $projectMinutes % 60;
                                                                    @endphp
                                                                    <label class="text-muted d-block mb-0">Project
                                                                        Time</label>
                                                                    <small class="fw-semibold">
                                                                        {{ $projectHours }}
                                                                        H{{ $projectRemainingMinutes > 0 ? ' ' . $projectRemainingMinutes . ' M' : '' }}
                                                                    </small>
                                                                </div>

                                                                <div class="user-progress" style="min-width: 140px;">
                                                                    @php
                                                                        $attendanceMinutes =
                                                                            $weekData['days_attendance'][$day] ?? 0;
                                                                        $attendanceHours = intdiv(
                                                                            $attendanceMinutes,
                                                                            60,
                                                                        );
                                                                        $attendanceRemainingMinutes =
                                                                            $attendanceMinutes % 60;
                                                                    @endphp
                                                                  <label class="text-muted d-block mb-0 small">Attendance Time</label>

                                                                    <small class="fw-semibold">
                                                                        {{ $attendanceHours }}
                                                                        H{{ $attendanceRemainingMinutes > 0 ? ' ' . $attendanceRemainingMinutes . ' M' : '' }}
                                                                    </small>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    @endif
</div>
<!-- </div> -->


<!-- / Content -->



@include('layouts.footer')


<script type="text/javascript">
    @if (Auth::user()->user_type == '0')

        'use strict';

        let cardColor = config.colors.white,
            headingColor = config.colors.headingColor,
            axisColor = config.colors.axisColor,
            borderColor = config.colors.borderColor;

        // Getting total hours from PHP
        const totalHours = {{ $totalHours }};

        // Loop through each chart container and render the chart
        //    @foreach ($data as $index => $userData)
        //        const chartOrderStatistics{{ $index }} = document.querySelector('#orderStatisticsChart-{{ $index }}'),
        //            orderChartConfig{{ $index }} = {
        //                chart: {
        //                    height: 165,
        //                    width: 130,
        //                    type: 'donut'
        //                },
        //             labels: ['Working Hours', 'Pending Hours'],

        //             series: [
        //                         <?php
        //                             $givenHours = floor($userData['weeks'][0]['total_hours']);
        //                             $totalHours = intdiv($givenHours, 60); // Integer division to get hours
        //                             $remainingMinutes = $givenHours % 60; //
        //
        ?>
        //                         {{ $totalHours ?? 0 }}, 
        //                         40 - {{ $totalHours ?? 0 }}
        //                     ],

        //             colors: [config.colors.primary, config.colors.secondary],
        //                stroke: {
        //                    width: 5,
        //                    colors: cardColor
        //                },
        //                dataLabels: {
        //                    enabled: false,
        //                    formatter: function (val, opt) {
        //                        return val + ' hours'; // Update to show hours
        //                    }
        //                },
        //                legend: {
        //                    show: false
        //                },
        //                grid: {
        //                    padding: {
        //                        top: 0,
        //                        bottom: 0,
        //                        right: 15
        //                    }
        //                },
        //                plotOptions: {
        //                    pie: {
        //                        donut: {
        //                            size: '75%',
        //                            labels: {
        //                                show: true,
        //                                value: {
        //                                    fontSize: '1.5rem',
        //                                    fontFamily: 'Public Sans',
        //                                    color: headingColor,
        //                                    offsetY: -15,
        //                                    formatter: function (val) {
        //                                        return val + '%'; // Show percentage in the center
        //                                    }
        //                                },
        //                                name: {
        //                                    offsetY: 20,
        //                                    fontFamily: 'Public Sans'
        //                                },                         
        //                                     total: {
        //                                             show: true,
        //                                             fontSize: '0.8125rem',
        //                                             color: axisColor,
        //                                             label: 'Total Hours',
        //                                             formatter: function (w) {
        //                                                 // PHP calculations
        //                                                  let givenHours = Math.floor({{ $userData['weeks'][0]['total_hours'] }});                                    
        //                                                  let totalHours = Math.floor(givenHours / 60);

        //                                                 // Calculate percentage of total hours
        //                                                 let percentage = Math.round((totalHours / 40) * 100);
        //                                                 return percentage + '%';
        //                                             }
        //                                         }

        //                            }
        //                        }
        //                    }
        //                }
        //            };

        //        if (typeof chartOrderStatistics{{ $index }} !== undefined && chartOrderStatistics{{ $index }} !== null) {
        //            const statisticsChart{{ $index }} = new ApexCharts(chartOrderStatistics{{ $index }}, orderChartConfig{{ $index }});
        //            statisticsChart{{ $index }}.render();
        //        }
        //    @endforeach
    @endif
</script>
