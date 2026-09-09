@include('layouts.header')
<?php
// echo"<pre>";
// print_r($data);
// die("ppp");
?>


<div class="container-xxl flex-grow-1 container-p-y" style="max-width:1500px;">

    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span>Daily Attendence Report</h4>
    <div class="card">
        <div class="card-body">
            <form class="" action="" method="">
                <div class="row">


                    <div class="col-md-4 p-1">
                        <label for="user_id" class="form-label font-weight-bold">Employee<span
                                class="text-danger text-center">*</span></label>
                        <select name="user_id" class="form-control" id="user_id" required>
                            @php
                                $getUsers = getUsers();
                                $selectedUserId = isset($_GET['user_id']) ? $_GET['user_id'] : ''; // Get selected user_id from URL query parameter
                            @endphp
                            <option disabled selected>Select employee</option>
                            @foreach ($getUsers as $val)
                                <option value="{{ $val['id'] }}"
                                    {{ $val['id'] == $selectedUserId ? 'selected' : '' }}>
                                    {{ ucfirst($val['name']) . ' (' . $val['email'] . ')' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @php
                        $months = [];
                        $selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('m-Y');

                        $start = new DateTime();
                        $start->modify('-1 year');

                        $end = new DateTime();

                        while ($start <= $end) {
                            $monthNumber = $start->format('m');
                            $yearNumber = $start->format('Y');

                            $monthName = $start->format('F') . ' ' . $yearNumber;

                            $months["$yearNumber-$monthNumber"] = $monthName;

                            $start->modify('+1 month');
                        }

                        $months = array_reverse($months);
                    @endphp

                    <div class="col-md-4 p-1">
                        <label for="month" class="form-label font-weight-bold">Month<span
                                class="text-danger text-center">*</span></label>
                        <select name="month" class="form-control" id="month" required>
                            <option value="">Select</option>
                            @foreach ($months as $monthYear => $monthName)
                                @php
                                    [$yearNumber, $monthNumber] = explode('-', $monthYear);
                                @endphp
                                <option value="{{ $monthNumber . '-' . $yearNumber }}"
                                    {{ $monthNumber . '-' . $yearNumber == $selectedMonth ? 'selected' : '' }}>
                                    {{ $monthName }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-2 p-1">
                        <input class="btn btn-primary pt-2" style="margin-top: 1.4rem!important" type="submit"
                            value="Submit ">
                    </div>
                </div>
            </form>
        </div>


    </div>
    <br>
    <?php
        $user_id = $data[0]['user_id'];
        if($user_id != 14){               
    ?>

    <div class="card">
        <div class="card-header">
            <h4>
                @php
                    // Get selected user name
                    $selectedUserName = '';
                    foreach ($getUsers as $user) {
                        if ($user['id'] == $selectedUserId) {
                            $selectedUserName = ucfirst($user['name']);
                            break;
                        }
                    }

                    if ($selectedUserName != '') {
                        // Get selected month name
                        $selectedMonthName = isset($selectedMonth) ? $selectedMonth : '';
                        [$monthNumber, $yearNumber] = explode('-', $selectedMonthName);
                        $month = (int) $monthNumber;
                        $year = (int) $yearNumber;
                        // Convert month number to month name
                        $dateTime = DateTime::createFromFormat('!m', $month);
                        $selectedMonth = $dateTime->format('F'); // 'F' format gives the full month
                    }
                @endphp
                @if ($selectedUserName != '')
                    {{ $selectedMonth . ' ' . $year . ' Report Generated For : ' . $selectedUserName }}
                @endif
            </h4>

        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        {{-- <th>Name</th> --}}
                        <th>Date</th>
                        <th>Office&nbsp;In</th>
                        <th>Office&nbsp;Out</th>
                        <th>Lunch&nbsp;In</th>
                        <th>Lunch&nbsp;Out</th>
                        <th>Active Hours</th>
                        <th>InActive Hours</th>
                        <th>Office Hours</th>
                        <th>Action</th>
                        {{-- <th>View History</th> --}}
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">

                    @if (empty($data))

                        <tr class="text-center">
                            <td colspan="12">No attendance records found.</td>
                        </tr>
                    @else
                        @foreach ($data as $index => $record)
                            @if ($record['is_sunday'] || $record['is_saturday'])
                                <tr style="background-color: #e9ecef;">

                                    <td>{{ $index + 1 }}</td>

                                    <td>{{ date('d M Y', strtotime($record['date'])) }}</td>

                                    <td colspan="7" class="weekend-text">

                                        {{ $record['is_sunday'] ? 'Sunday' : 'Saturday' }}

                                    </td>
                                    <td>
                                        {{-- Edit --}}

                                    </td>
                                </tr>
                            @elseif (
                                \Carbon\Carbon::parse($record['date'])->lt(\Carbon\Carbon::today()) &&
                                    $record['office_in'] == 'N/A' &&
                                    $record['office_out'] == 'N/A')
                                <tr style="background-color: #f8d7da;">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ date('d M Y', strtotime($record['date'])) }}</td>
                                    <td colspan="7" class="weekend-text">
                                        Absent
                                    </td>
                                    <td>
                                        {{-- Edit --}}
                                        <a href="#" class="edit-record" data-date="{{ $record['date'] }}"
                                            data-record-id="{{ $record['user_id'] }}"
                                            data-office-in="{{ $record['office_in'] }}"
                                            data-office-out="{{ $record['office_out'] }}"
                                            data-lunch-in="{{ $record['lunch_in'] }}"
                                            data-lunch-out="{{ $record['lunch_out'] }}"
                                            data-work-hours="{{ $record['work_hours'] }}" data-toggle="modal"
                                            data-target="#exampleModal">
                                            <i class="bx bx-edit-alt me-1"></i>
                                        </a>

                                        {{-- View --}}
                                        <a href="javascript:void(0)" class="btn btn-sm view-day-tracker"
                                            data-user-id="{{ $record['user_id'] }}" data-name="{{ $record['name'] }}"
                                            data-date="{{ \Carbon\Carbon::parse($record['date'])->format('Y-m-d') }}">
                                            <i class="fa-regular fa-eye" style="color: #696cff;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @else
                                <tr
                                    @if ($record['status'] == 'short_leave') style="background-color:#fff3cd;"
                    @elseif($record['status'] == 'half_day')
                      style="background-color: #fae5ff;"
                    @elseif($record['status'] == 'late')
                        style="background-color:#fff3cd;" @endif>

                                    <td>{{ $index + 1 }}</td>

                                    <td>{{ date('d M Y', strtotime($record['date'])) }}</td>

                                    <td class="{{ $record['office_in'] == 'N/A' ? '' : 'text-success' }}">

                                        {{ $record['office_in'] }}

                                    </td>

                                    <td class="{{ $record['office_out'] == 'N/A' ? '' : 'text-success' }}">

                                        {{ $record['office_out'] }}

                                    </td>

                                    <td class="{{ $record['lunch_in'] == 'N/A' ? '' : 'text-success' }}">

                                        {{ $record['lunch_in'] }}

                                    </td>

                                    <td class="{{ $record['lunch_out'] == 'N/A' ? '' : 'text-success' }}">

                                        {{ $record['lunch_out'] }}

                                    </td>

                                    <td class="{{ $record['user_active_time'] == '00:00:00' ? '' : 'text-success' }}">

                                        {{ $record['user_active_time'] !== '00:00:00' ? $record['user_active_time'] : 'N/A' }}

                                    </td>

                                    <td
                                        class="{{ $record['user_inactive_time'] == '00:00:00' ? '' : 'text-success' }}">

                                        {{ $record['user_inactive_time'] !== '00:00:00' ? $record['user_inactive_time'] : 'N/A' }}

                                    </td>

                                    <td class="{{ $record['work_hours'] == 'N/A' ? '' : 'text-success' }}">

                                        {{ $record['work_hours'] }}

                                    </td>

                                    <td>

                                        {{-- Edit --}}
                                        <a href="#" class="edit-record" data-date="{{ $record['date'] }}"
                                            data-record-id="{{ $record['user_id'] }}"
                                            data-office-in="{{ $record['office_in'] }}"
                                            data-office-out="{{ $record['office_out'] }}"
                                            data-lunch-in="{{ $record['lunch_in'] }}"
                                            data-lunch-out="{{ $record['lunch_out'] }}"
                                            data-work-hours="{{ $record['work_hours'] }}" data-toggle="modal"
                                            data-target="#exampleModal">

                                            <i class="bx bx-edit-alt me-1"></i>

                                        </a>

                                        {{-- View --}}
                                        <a href="javascript:void(0)" class="btn btn-sm view-day-tracker"
                                            data-user-id="{{ $record['user_id'] }}" data-name="{{ $record['name'] }}"
                                            data-date="{{ \Carbon\Carbon::parse($record['date'])->format('Y-m-d') }}">

                                            <i class="fa-regular fa-eye" style="color: #696cff;"></i>

                                        </a>

                                    </td>

                                </tr>
                            @endif
                        @endforeach

                        {{-- TOTAL ROW --}}
                        <tr class="text-end">

                            <td colspan="10">

                                <strong>Total Work Hours: {{ $totalWorkHours }}</strong>

                            </td>

                        </tr>

                    @endif

                </tbody>
            </table>
        </div>
    </div>
    <?php
      }
    ?>
</div>
<!-- Bootstrap CSS -->
{{-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> --}}
<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-2" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Edit Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form to edit record fields -->
                <form id="editRecordForm" action="{{ route('edit_user_attendance') }}" method="POST">
                    @csrf
                    <input type="hidden" name="record_id" id="recordId"
                        value="{{ old('record_id', $record->id ?? '') }}">
                    <input type="hidden" name="date" id="date"
                        value="{{ old('date', $record->date ?? '') }}">
                    <div class="form-group bootstrap-timepicker">
                        <label for="officeIn">Office In:</label>
                        <input type="text" class="form-control timepicker" id="officeIn" name="office_in"
                            value="{{ old('office_in', $record->office_in ?? '') }}">
                    </div>
                    <div class="form-group bootstrap-timepicker">
                        <label for="officeOut">Office Out:</label>
                        <input type="text" class="form-control timepicker" id="officeOut" name="office_out"
                            value="{{ old('office_out', $record->office_out ?? '') }}">
                    </div>
                    <div class="form-group bootstrap-timepicker">
                        <label for="lunchIn">Lunch In:</label>
                        <input type="text" class="form-control timepicker" id="lunchIn" name="lunch_in"
                            value="{{ old('lunch_in', $record->lunch_in ?? '') }}">
                    </div>
                    <div class="form-group bootstrap-timepicker">
                        <label for="lunchOut">Lunch Out:</label>
                        <input type="text" class="form-control timepicker" id="lunchOut" name="lunch_out"
                            value="{{ old('lunch_out', $record->lunch_out ?? '') }}">
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-outline-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">

<!-- Timepicker JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script> --}}

<script>
    $(document).ready(function() {
        $('.edit-record').click(function(e) {
            var date = $(this).data('date');
            var recordId = $(this).data('record-id');
            var officeIn = $(this).data('office-in');
            var officeOut = $(this).data('office-out');
            var lunchIn = $(this).data('lunch-in');
            var lunchOut = $(this).data('lunch-out');
            var workHours = $(this).data('work-hours');
            $('#date').val(date);
            $('#recordId').val(recordId);
            $('#officeIn').val(officeIn);
            $('#officeOut').val(officeOut);
            $('#lunchIn').val(lunchIn);
            $('#lunchOut').val(lunchOut);
            $('#workHours').val(workHours);
            $('#exampleModal').modal('show');
        });
        
        $('#editRecordForm').submit(function(e) {
            e.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                success: function(response) {

                    if (response.status == 'success') {
                        console.log(response.message);
                        showToast(response.message, response.status);
                    } else {
                        showToast(response.message, response.status);
                    }
                    $('#exampleModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });


        });
    });


    $('#editRecordForm .timepicker').timepicker({
        timeFormat: 'h:mm p',
        interval: 1,
        minTime: '09',
        maxTime: '07:00pm',
        defaultTime: '11',
        startTime: '09:00',
        dynamic: false,
        dropdown: true,
        scrollbar: true
    });
</script>

@include('layouts.footer')
