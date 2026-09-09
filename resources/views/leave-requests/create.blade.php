@include('layouts.header')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Add Leave Request</h4>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('leave-requests-add') }}" method="POST" id="leaveRequestForm">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="leave_type" class="form-label">Leave Type:</label>
                            <select name="leave_type" id="leave_type" class="form-control">
                                <option value="1" {{ old('leave_type') == '1' ? 'selected' : '' }}>Full Day
                                </option>
                                <option value="2" {{ old('leave_type') == '2' ? 'selected' : '' }}>Half Day
                                </option>
                                <option value="3" {{ old('leave_type') == '3' ? 'selected' : '' }}>Short Leave
                                </option>
                            </select>
                            @error('leave_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="start_date" class="form-label">Start Date:</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ old('start_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
                            @error('start_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3" id="endDateField">
                            <label for="end_date" class="form-label">End Date:</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                min="{{ date('Y-m-d') }}">
                            @error('end_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- <div class="form-group mb-3" id="leaveDateField" style="display:none;">
                            <label for="leave_date" class="form-label">Date *</label>
                            <input type="date" name="leave_date" id="leave_date" class="form-control"
                                min="{{ date('Y-m-d') }}">
                        </div> --}}

                        <div class="form-group mb-3" id="fromTimeField" style="display:none;">
                            <label for="from_time" class="form-label">From Time *</label>
                            <input type="time" name="from_time" id="from_time" class="form-control">
                        </div>

                        <div class="form-group mb-3" id="toTimeField" style="display:none;">
                            <label for="to_time" class="form-label">To Time *</label>
                            <input type="time" name="to_time" id="to_time" class="form-control">
                            <div id="timeError" class="text-danger mt-1"></div>
                        </div>

                        <div class="form-group mb-3" id="timeField" style="display: none;">
                            <label for="leave_time" class="form-label">Time</label>&nbsp;<span
                                style="font-size: 12px;">(Time in hour)</span>
                            <input type="number" class="form-control " id="leave_time" name="leave_time" autofocus
                                placeholder="Time" min="1" max="3">
                            <div id="nameHelp" class="form-text"></div>
                            @if ($errors->has('leave_time'))
                                <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('leave_time') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group mb-3">
                            <label for="reason" class="form-label">Reason:</label>
                            <textarea name="reason" id="reason" placeholder="Message for the manager and HR department" class="form-control">{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-primary">Submit</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {

        function updateVisibilityBasedOnLeaveType() {

            var leaveType = $('#leave_type').val();

            $('#endDateField').hide();
            $('#timeField').hide();
            $('#fromTimeField').hide();
            $('#toTimeField').hide();

            if (leaveType == '1') {
                $('#endDateField').show();
            } else if (leaveType == '2') {
                $('#fromTimeField').show();
                $('#toTimeField').show();
            } else if (leaveType == '3') {
                $('#fromTimeField').show();
                $('#toTimeField').show();
            }
        }

        updateVisibilityBasedOnLeaveType();

        $('#leave_type').on('change', function() {
            updateVisibilityBasedOnLeaveType();
        });

        // Form Validation
        $('#leaveRequestForm').on('submit', function(e) {

            $('#timeError').text('');

            let leaveType = $('#leave_type').val();
            let fromTime = $('#from_time').val();
            let toTime = $('#to_time').val();

            if (leaveType == '2' || leaveType == '3') {

                if (!fromTime || !toTime) {

                    $('#timeError').text('Please select From Time and To Time.');
                    e.preventDefault();
                    return false;
                }

                let start = new Date('1970-01-01T' + fromTime);
                let end = new Date('1970-01-01T' + toTime);

                let diffHours = (end - start) / (1000 * 60 * 60);

                if (diffHours <= 0) {
                    $('#timeError').text('To Time must be greater than From Time.');
                    e.preventDefault();
                    return false;
                }
                if (leaveType == '2' && diffHours > 4) {
                    $('#timeError').text('Half Day leave cannot exceed 4 hours.');
                    $('#to_time').addClass('is-invalid');
                    e.preventDefault();
                    return false;
                }
                if (leaveType == '3' && diffHours > 2) {

                    $('#timeError').text('Short Leave cannot exceed 2 hours.');
                    $('#to_time').addClass('is-invalid');
                    e.preventDefault();
                    return false;
                }
            }

        });

    });
    $('#from_time, #to_time').on('change', function() {

        $('#timeError').text('');
        $('#to_time').removeClass('is-invalid');

    });
</script>


@include('layouts.footer')
