@include('layouts.header')

@php
    $avatarColors = ['primary', 'success', 'info', 'warning', 'danger'];
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Task Management /</span>
        Employee Report
    </h4>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('employee_report') }}" class="row g-3 align-items-end">
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

    <div class="card">
        <h5 class="card-header"><i class="bx bx-group"></i> Employees</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Designation</th>
                        <th>Total Tasks</th>
                        <th>Completed</th>
                        <th>Delayed</th>
                        <th>Overdue</th>
                        <th>Completion</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($summaries as $i => $s)
                        @php
                            $emp = $s['employee'];
                            $initials = collect(explode(' ', trim($emp->name)))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                            $avatarColor = $avatarColors[$emp->id % count($avatarColors)];
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm">
                                        <span
                                            class="avatar-initial rounded-circle bg-label-{{ $avatarColor }}">{{ $initials }}</span>
                                    </div>
                                    <span class="fw-semibold">{{ $emp->name }}</span>
                                </div>
                            </td>
                            <td>{{ $emp->designation ?: '-' }}</td>
                            <td>{{ $s['total'] }}</td>
                            <td class="text-success">{{ $s['completed'] }}</td>
                            <td class="{{ $s['delayed'] > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                                {{ $s['delayed'] }}
                            </td>
                            <td class="{{ $s['overdue'] > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                                {{ $s['overdue'] }}
                            </td>
                            <td style="min-width:120px;">
                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar bg-success" style="width: {{ $s['completion_rate'] }}%">
                                    </div>
                                </div>
                                <small>{{ $s['completion_rate'] }}%</small>
                            </td>
                            <td>
                                <a href="{{ route('employee_report.show', array_merge(['user' => $emp->id], request()->only(['filter_type', 'start_date', 'end_date']))) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    View Report
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No employees found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('layouts.footer')
