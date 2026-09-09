@include('layouts.header')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="page-title mb-1">
                Leave Management
            </h2>

            {{-- <p class="page-subtitle mb-0">
                Track and manage leave requests
            </p> --}}
        </div>

    </div>

    @if (Auth::user()->user_type == '1')
        <div class="row mb-4 justify-content-center">

            <!-- Monthly Leave (one combined quota — 1 full day + 0.5 day
                 usable as a half day or 2 short leaves) -->
            <div class="col-md-3 mb-3">
                <div class="leave-card purple-card">

                    <h5 class="leave-title">
                        Monthly Leave
                    </h5>

                    <div class="leave-stats-box">

                        <div>
                            <small>Allocated</small>
                            <h2>{{ $monthlyLeaveDayQuota }}</h2>
                        </div>

                        <div class="text-end">
                            <small>Used</small>
                            <h2>{{ $usedLeaveDays }}</h2>
                        </div>

                    </div>

                    <div class="leave-footer-box">
                        Used: {{ $usedLeaveDays }}/{{ $monthlyLeaveDayQuota }} days |
                        Remaining: {{ $remainingLeaveDays }} days
                    </div>

                </div>
            </div>

            <!-- Short Leave (draws from the same shared quota above —
                 shown here just as an occurrence count, 0.25 day each) -->
            <div class="col-md-3 mb-3">
                <div class="leave-card orange-card">

                    <h5 class="leave-title">
                        Short Leave
                    </h5>

                    <div class="leave-stats-box">

                        <div>
                            <small>Value each</small>
                            <h2>0.25d</h2>
                        </div>

                        <div class="text-end">
                            <small>Taken</small>
                            <h2>{{ $usedShortLeaves }}</h2>
                        </div>

                    </div>

                    <div class="leave-footer-box">
                        Taken this period: {{ $usedShortLeaves }} (shares the
                        Monthly Leave quota, not a separate allowance)
                    </div>

                </div>
            </div>

            <!-- Half Day (draws from the same shared quota above — shown
                 here just as an occurrence count, 0.5 day each) -->
            <div class="col-md-3 mb-3">
                <div class="leave-card green-card">

                    <h5 class="leave-title">
                        Half Day
                    </h5>

                    <div class="leave-stats-box">

                        <div>
                            <small>Value each</small>
                            <h2>0.5d</h2>
                        </div>

                        <div class="text-end">
                            <small>Taken</small>
                            <h2>{{ $usedHalfDays }}</h2>
                        </div>

                    </div>

                    <div class="leave-footer-box">
                        Taken this period: {{ $usedHalfDays }} (shares the
                        Monthly Leave quota, not a separate allowance)
                    </div>

                </div>
            </div>

        </div>
    @endif

    @if (Auth::user()->user_type == '0')

        <div class="card filter-card mb-4">

            <div class="card-body">

                <form action="{{ route('leave-requests') }}" method="GET">

                    <div class="row align-items-end">

                        <div class="col-md-3 mb-3">

                            <label class="filter-label">
                                Employee
                            </label>

                            <select name="user_id" class="form-control" id="user_id">

                                @php
                                    $getUsers = getUsers();
                                    $selectedUserId = isset($_GET['user_id']) ? $_GET['user_id'] : '';
                                @endphp

                                <option value="all">
                                    All
                                </option>

                                @foreach ($getUsers as $val)
                                    <option value="{{ $val['id'] }}"
                                        {{ $val['id'] == $selectedUserId ? 'selected' : '' }}>

                                        {{ ucfirst($val['name']) . ' (' . $val['email'] . ')' }}

                                    </option>
                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-2 mb-3">

                            <label class="filter-label">
                                Start Date
                            </label>

                            <input type="date" name="start" class="form-control"
                                value="{{ old('start', $_REQUEST['start'] ?? date('Y-m-d')) }}" required>

                        </div>

                        <div class="col-md-2 mb-3">

                            <label class="filter-label">
                                End Date
                            </label>

                            <input type="date" name="end" class="form-control"
                                value="{{ old('end', $_REQUEST['end'] ?? date('Y-m-d')) }}" required>

                        </div>

                        <div class="col-md-2 mb-3">

                            <label class="filter-label">
                                Status
                            </label>

                            <select name="status" class="form-control">

                                <option value="all">
                                    All
                                </option>

                                <option value="pending">
                                    Pending
                                </option>

                                <option value="approved">
                                    Approved
                                </option>

                                <option value="rejected">
                                    Rejected
                                </option>

                            </select>

                        </div>

                        <div class="col-md-3 mb-3">

                            <button type="submit" class="btn btn-primary btn-modern w-100">

                                Apply Filter

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    @endif

    <div class="card table-card">

        <div class="card-header bg-white border-0 pt-4">

            <h4 class="fw-bold mb-0">
                All Leave Requests
            </h4>

        </div>

        <div class="table-responsive text-nowrap">

            <table class="table align-middle mb-0">

                <thead>

                    <tr>

                        <th>Sr No.</th>

                        @if (Auth::user()->user_type == '0')
                            <th>Requested By</th>
                        @endif

                        <th>Reason</th>

                        <th>Leave Type</th>

                        <th>Applied Date</th>

                        <th>Status</th>

                        <th class="text-center">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @if (Auth::user()->user_type == '0')

                        @php
                            $startSerialNumber = ($leaveRequests->currentPage() - 1) * $leaveRequests->perPage() + 1;
                        @endphp

                        @forelse ($leaveRequests as $leaveRequest)
                            <tr>

                                <td>
                                    {{ $startSerialNumber++ }}
                                </td>

                                <td>

                                    <div class="fw-semibold">
                                        {{ $leaveRequest->user->name }}
                                    </div>

                                    <a href="#" class="view-reason text-primary"
                                        data-reason="{{ htmlspecialchars($leaveRequest->reason) }}"
                                        data-startdate="{{ date('d F Y', strtotime($leaveRequest->start_date)) }}"
                                        data-enddate="{{ date('d F Y', strtotime($leaveRequest->end_date)) }}"
                                        data-createdate="{{ date('d F Y', strtotime($leaveRequest->created_at)) }}"
                                        data-name="{{ $leaveRequest->user->name }}"
                                        data-actionreason="{{ $leaveRequest->action_reason }}"
                                        data-leavetype="{{ $leaveRequest->leave_type }}"
                                        data-fromtime="{{ $leaveRequest->from_time }}"
                                        data-totime="{{ $leaveRequest->to_time }}" data-toggle="modal"
                                        data-target="#exampleModalCenter">

                                        View Details

                                    </a>

                                </td>

                                <td>

                                    <div class="reason-content">

                                        @php
                                            $reason = $leaveRequest->reason;

                                            $trimmedReason = implode(' ', array_slice(explode(' ', $reason), 0, 10));

                                            $isLong = str_word_count($reason) > 10;
                                        @endphp

                                        <span class="trimmed-text">
                                            {{ htmlspecialchars($trimmedReason) }}
                                        </span>

                                        <span class="full-text" style="display:none;">

                                            {{ htmlspecialchars($reason) }}

                                        </span>

                                        @if ($isLong)
                                            <a href="#" class="btn-show-more text-primary">

                                                More...

                                            </a>
                                        @endif

                                    </div>

                                </td>

                                <td>

                                    @if ($leaveRequest->leave_type == 1)
                                        <span class="badge bg-label-primary">
                                            Full Day
                                        </span>

                                        <div class="small text-muted mt-1">
                                            {{ $leaveRequest->total_leave_days }} Day(s)
                                        </div>
                                    @elseif ($leaveRequest->leave_type == 2)
                                        <span class="badge bg-label-warning">
                                            Half Day
                                        </span>

                                        <div class="small text-muted mt-1">
                                            {{ date('h:i A', strtotime($leaveRequest->from_time)) }}
                                            -
                                            {{ date('h:i A', strtotime($leaveRequest->to_time)) }}
                                        </div>
                                    @elseif ($leaveRequest->leave_type == 3)
                                        <span class="badge bg-label-info">
                                            Short Leave
                                        </span>

                                        <div class="small text-muted mt-1">
                                            {{ date('h:i A', strtotime($leaveRequest->from_time)) }}
                                            -
                                            {{ date('h:i A', strtotime($leaveRequest->to_time)) }}
                                        </div>
                                    @endif

                                </td>

                                <td>
                                    {{ date('d F Y', strtotime($leaveRequest->created_at)) }}
                                </td>

                                <td>

                                    @if ($leaveRequest->status == 'pending')
                                        <span class="badge bg-label-info">
                                            Pending
                                        </span>
                                    @elseif($leaveRequest->status == 'approved')
                                        <span class="badge bg-label-success">
                                            Approved
                                        </span>
                                    @else
                                        <span class="badge bg-label-danger">
                                            Rejected
                                        </span>
                                    @endif

                                    {{-- @if (!empty($leaveRequest->action_reason))
                                        <div class="mt-1">
                                            <small class="text-muted d-block">
                                                Remark:
                                            </small>

                                            <span class="text-dark">
                                                {{ $leaveRequest->action_reason }}
                                            </span>
                                        </div>
                                    @endif --}}

                                </td>

                                <td class="text-center">

                                    @if ($leaveRequest->status == 'pending')
                                        <button type="button" class="btn btn-sm action-btn open-action-modal"
                                            data-id="{{ $leaveRequest->id }}" data-action="approve">

                                            <i class='bx bx-check text-success'></i>

                                        </button>

                                        <button type="button" class="btn btn-sm action-btn open-action-modal"
                                            data-id="{{ $leaveRequest->id }}" data-action="reject">

                                            <i class='bx bx-x text-danger'></i>

                                        </button>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center py-5">

                                    No leave requests found

                                </td>

                            </tr>
                        @endforelse
                    @else
                        @forelse ($userleaveRequests as $leaveRequest)
                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>

                                    <div class="reason-content">

                                        @php
                                            $reason = $leaveRequest->reason;

                                            $trimmedReason = implode(' ', array_slice(explode(' ', $reason), 0, 10));

                                            $isLong = str_word_count($reason) > 10;
                                        @endphp

                                        <span class="trimmed-text">
                                            {{ htmlspecialchars($trimmedReason) }}
                                        </span>

                                        <span class="full-text" style="display:none;">
                                            {{ htmlspecialchars($reason) }}
                                        </span>

                                        @if ($isLong)
                                            <a href="#" class="btn-show-more text-primary">

                                                More...

                                            </a>
                                        @endif

                                    </div>

                                </td>

                                <td>

                                    @if ($leaveRequest->leave_type == 1)
                                        <span class="badge bg-label-primary">
                                            Full Day
                                        </span>

                                        <div class="small text-muted mt-1">
                                            {{ $leaveRequest->total_leave_days }} Day(s)
                                        </div>
                                    @elseif ($leaveRequest->leave_type == 2)
                                        <span class="badge bg-label-warning">
                                            Half Day
                                        </span>

                                        <div class="small text-muted mt-1">

                                            {{ date('h:i A', strtotime($leaveRequest->from_time)) }}
                                            -
                                            {{ date('h:i A', strtotime($leaveRequest->to_time)) }}

                                        </div>
                                    @elseif ($leaveRequest->leave_type == 3)
                                        <span class="badge bg-label-info">
                                            Short Leave
                                        </span>

                                        <div class="small text-muted mt-1">

                                            {{ date('h:i A', strtotime($leaveRequest->from_time)) }}
                                            -
                                            {{ date('h:i A', strtotime($leaveRequest->to_time)) }}

                                        </div>
                                    @endif

                                </td>

                                <td>

                                    {{ date('d F Y', strtotime($leaveRequest->created_at)) }}

                                </td>

                                <td>

                                    @if ($leaveRequest->status == 'pending')
                                        <span class="badge bg-label-info">
                                            Pending
                                        </span>
                                    @elseif($leaveRequest->status == 'approved')
                                        <span class="badge bg-label-success">
                                            Approved
                                        </span>
                                    @else
                                        <span class="badge bg-label-danger">
                                            Rejected
                                        </span>
                                    @endif



                                </td>

                                <td>

                                    @if ($leaveRequest->status == 'pending')
                                        <form action="{{ route('leave-requests.delete', $leaveRequest->id) }}"
                                            method="POST" style="display:inline-block;"
                                            onsubmit="return confirm('Are you sure you want to delete this leave request?');">

                                            @csrf
                                            @method('GET')

                                            <button type="submit" class="btn btn-sm action-btn" title="Delete">

                                                <i class='bx bx-trash text-danger'></i>

                                            </button>

                                        </form>
                                    @else
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary view-action-reason"
                                            data-reason="{{ $leaveRequest->action_reason ?? 'No remark available' }}">

                                            View Remark

                                        </button>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="text-center py-5">

                                    No leave requests found

                                </td>

                            </tr>
                        @endforelse

                    @endif

                </tbody>

            </table>

        </div>

        @if (Auth::user()->user_type == '0')
            <div class="p-4">

                {{ $leaveRequests->links() }}

            </div>
        @endif

    </div>

</div>
<div class="modal fade" id="remarkModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-primary text-white">

                <h5 class="modal-title-remark">
                    Admin Remark
                </h5>

                <button type="button" class="close text-white" data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <p id="remarkText" class="mb-0"></p>

            </div>

        </div>

    </div>

</div>
<!-- Modal -->
<div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <form id="actionForm" method="POST">

                @csrf
                @method('PUT')

                <div class="modal-header bg-primary text-white">

                    <h5 class="modal-title-remark fw-bold" id="actionTitle">
                        Leave Action
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body p-4">

                    <div class="text-center mb-3">

                        <i class="bx bx-message-square-detail fs-1 text-primary"></i>

                        <h5 class="mt-2 mb-1">
                            Add Your Remark
                        </h5>

                    </div>

                    <div class="form-group">

                        <label class="font-weight-bold mb-2">
                            Reason <span class="text-danger">*</span>
                        </label>

                        <textarea name="action_reason" class="form-control" rows="5" required placeholder="Type your remarks here..."></textarea>

                    </div>

                </div>

                <div class="modal-footer border-0">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary px-4">
                        Submit
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-dialog-centered" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Leave Details
                </h5>

                <button type="button" class="close" data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <p>
                    <strong>User Name :</strong>
                    <span id="name"></span>
                </p>

                <p>
                    <strong>Reason :</strong>
                    <span id="reasonDetails"></span>
                </p>

                <p>
                    <strong>Leave Type :</strong>
                    <span id="leaveType"></span>
                </p>

                <p>
                    <strong>Time :</strong>
                    <span id="leaveTime"></span>
                </p>

                <p>
                    <strong>Created Date :</strong>
                    <span id="createdate"></span>
                </p>

                <p>
                    <strong>Start Date :</strong>
                    <span id="startDate"></span>
                </p>

                <p>
                    <strong>End Date :</strong>
                    <span id="endDate"></span>
                </p>

                <p>
                    <strong>Admin Remark :</strong>
                    <span id="actionReason"></span>
                </p>

            </div>

        </div>

    </div>

</div>

@include('layouts.footer')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {

        // View Leave Details
        $('.view-reason').on('click', function() {

            $('#reasonDetails').text($(this).data('reason'));
            $('#createdate').text($(this).data('createdate'));
            $('#startDate').text($(this).data('startdate'));
            $('#endDate').text($(this).data('enddate'));
            $('#name').text($(this).data('name'));

            let leaveType = $(this).data('leavetype');

            if (leaveType == 1) {

                $('#leaveType').text('Full Day');
                $('#leaveTime').text('-');

            } else if (leaveType == 2) {

                $('#leaveType').text('Half Day');

                $('#leaveTime').text(
                    $(this).data('fromtime') +
                    ' - ' +
                    $(this).data('totime')
                );

            } else {

                $('#leaveType').text('Short Leave');

                $('#leaveTime').text(
                    $(this).data('fromtime') +
                    ' - ' +
                    $(this).data('totime')
                );

            }

            $('#actionReason').text(
                $(this).data('actionreason') || 'N/A'
            );

        });
        // Show More / Less
        $('.btn-show-more').on('click', function(e) {

            e.preventDefault();

            let $btn = $(this);
            let $reasonContent = $btn.closest('.reason-content');

            $reasonContent.find('.trimmed-text').toggle();
            $reasonContent.find('.full-text').toggle();

            $btn.text(
                $btn.text() === 'More...' ? 'Less' : 'More...'
            );

        });

        // Filter Toggle
        function toggleCustomRange() {

            if ($('#filter_type').val() === 'custom_range') {
                $('.custom-range-box').show();
            } else {
                $('.custom-range-box').hide();
            }

        }

        toggleCustomRange();

        $('#filter_type').change(function() {
            toggleCustomRange();
        });

        // Approve / Reject Modal Open
        $(document).on('click', '.open-action-modal', function() {

            let id = $(this).data('id');
            let action = $(this).data('action');

            let baseUrl = "{{ url('leave-requests') }}";

            let url = '';
            let title = '';
            let buttonText = '';
            let buttonClass = '';

            if (action === 'approve') {

                url = baseUrl + '/' + id + '/approve';
                title = 'Approve Leave';
                buttonText = 'Approve';
                buttonClass = 'btn-success';

            } else {

                url = baseUrl + '/' + id + '/reject';
                title = 'Reject Leave';
                buttonText = 'Reject';
                buttonClass = 'btn-danger';

            }

            $('#actionTitle').text(title);

            $('#actionForm').attr('action', url);

            $('#actionForm textarea[name="action_reason"]').val('');

            let submitBtn = $('#actionForm button[type="submit"]');

            submitBtn
                .removeClass('btn-success btn-danger btn-primary')
                .addClass(buttonClass)
                .text(buttonText);

            $('#actionModal').modal('show');

        });

        // Close Modal
        $(document).on('click', '.close, [data-dismiss="modal"]', function() {

            $('#actionModal').modal('hide');

        });
        $(document).on('click', '.close, [data-dismiss="modal"]', function() {

            $('#remarkModal').modal('hide');

        });

        // Reset Form When Modal Closed
        $('#actionModal').on('hidden.bs.modal', function() {

            $('#actionForm')[0].reset();

        });

    });
    $(document).on('click', '.view-action-reason', function() {

        let reason = $(this).data('reason');

        $('#remarkText').text(reason);

        $('#remarkModal').modal('show');

    });
</script>
