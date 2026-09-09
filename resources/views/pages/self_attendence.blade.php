@include('layouts.header')
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/flipclock/0.7.8/flipclock.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flipclock/0.7.8/flipclock.min.js"></script>
<style>
    .clock {
        display: flex;
    }

    .digit {
        position: relative;
        width: 50px;
        height: 100px;
        margin: 0 2px;
        text-align: center;
    }

    .base {
        font-size: 80px;
        line-height: 100px;
    }

    .flap {
        position: absolute;
        width: 100%;
        height: 100%;
    }

    .front,
    .back,
    .under {
        display: none;
    }

    .btn {
        margin: 5px;
    }
</style>

<link rel="stylesheet" href="https://api.chipware.co.za/css/flipclock.css">
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- <h4 class="fw-bold py-3 mb-4">Attendence </h4> --}}

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                {{-- <h5 class="card-header">Default</h5> --}}
                <div class="card-body">

                    <div class="container mt-0">
                        <div class="cip_clock" id="cip_clock">
                            <div class="row align-items-center">
                                <div class="col-md-6 clock-flap">

                                    <div class="clock ms-0 me-0" id="clock1"></div>
                               
                                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                                    <script src="https://cdnjs.cloudflare.com/ajax/libs/flipclock/0.7.8/flipclock.min.js"></script>
                                    <script>
                                        function updateClock() {
                                            // Get the current time
                                            var currentTime = new Date();
                                            var start = new Date(currentTime);
                                            start.setDate(start.getDate());
                                            start.setHours(0, 0, 0, 0);
                                            var now = new Date(currentTime);
                                            var diff = (now.getTime() - start.getTime()) / 1000;
                                            $('#clock1').FlipClock(diff, {
                                                clockFace: 'HourlyCounter', // Display format of the clock
                                                countdown: false, // Whether to count down or up
                                                showSeconds: true // Whether to display seconds
                                            });
                                            // setInterval(updateClock, 1000);
                                        }
                                        updateClock();
                                        setTimeout(updateClock, 1000);
                                    </script>


                                </div>
                                
                             

                                <div class="col-md-6 clock-group-btn pull-right text-start text-xl-end">

                                    @php
                                        $officeIn = !$attendance->where('office_in_time', '!=', null)->isEmpty();
                                        $officeOut = !$attendance->where('office_out_time', '!=', null)->isEmpty();
                                        $lunchIn = !$attendance->where('lunch_in_time', '!=', null)->isEmpty();
                                        $lunchOut = !$attendance->where('lunch_out_time', '!=', null)->isEmpty();
                                        $breakin = !$attendance->where('break_in_time', '!=', null)->isEmpty();
                                        $breakout = !$attendance->where('break_out_time', '!=', null)->isEmpty();
                                        $initialDisable = true;
                                    @endphp

                                    <button type="button" id="office-in-btn"
                                        class="btn peach-gradient btn-md btn btn-success"
                                        onclick="return OfficeClockInOut('office-in');"
                                        {{ $initialDisable ? 'disabled' : '' }} {{ $officeIn ? 'disabled' : '' }}>
                                        <i class="fas fa-sign-in" aria-hidden="true"></i> Office IN
                                    </button>

                                    <button type="button" id="office-out-btn"
                                        class="btn purple-gradient btn-md btn btn-danger"
                                        onclick="return OfficeClockInOut('office-out');"
                                        {{ $initialDisable ? 'disabled' : '' }}
                                        {{ !$officeIn || $officeOut || ($lunchIn && !$lunchOut) ? 'disabled' : '' }}>
                                        <i class="fas fa-sign-out" aria-hidden="true"></i> Office Out
                                    </button>

                                    <button type="button" id="lunch-in-btn"
                                        class="btn peach-gradient btn-md btn btn-success"
                                        onclick="return LunchClockInOut('lunch-in');"
                                        {{ $initialDisable ? 'disabled' : '' }}
                                        {{ $lunchIn || !$officeIn || $officeOut ? 'disabled' : '' }}>
                                        <i class="fas fa-sign-in" aria-hidden="true"></i> Lunch In
                                    </button>

                                    @php
                                        $isDisabled = $lunchOut || !$lunchIn ? 'disabled' : '';
                                    @endphp

                                    <button type="button" id="lunch-out-btn"
                                        class="btn purple-gradient btn-md btn btn-danger"
                                        onclick="return LunchClockInOut('lunch-out');"
                                        {{ $initialDisable ? 'disabled' : '' }} {{ $isDisabled }}>
                                        <i class="fas fa-sign-out" aria-hidden="true"></i> Lunch Out
                                    </button>

                                    <br>

                                    <button type="button" id="break-btn"
                                        class="btn orange-gradient btn-sm btn btn-warning"
                                        onclick="handleBreak('break');"
                                        @if ($breakin) style="display: none;" @endif
                                        {{ $initialDisable ? 'disabled' : '' }} {{ $officeIn ? '' : 'disabled' }}
                                        {{ $officeOut ? 'disabled' : '' }}>
                                        <i class="fas fa-coffee" aria-hidden="true"></i> Take Break
                                    </button>

                                    <button type="button" id="stop-btn" class="btn red-gradient btn-sm btn btn-danger"
                                        onclick="handleBreak('stop');"
                                        @if ($breakout) style="display: none;" @endif
                                        {{ $initialDisable ? 'disabled' : '' }} {{ $breakin ? '' : 'disabled' }}
                                        {{ $officeOut ? 'disabled' : '' }}>
                                        <i class="fas fa-stop-circle" aria-hidden="true"></i> Stop Break
                                    </button>

                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @php
                $officeIn = $attendance->whereNotNull('office_in_time')->first();
                $officeOut = $attendance->whereNotNull('office_out_time')->first();
                $lunchIn = $attendance->whereNotNull('lunch_in_time')->first();
                $lunchOut = $attendance->whereNotNull('lunch_out_time')->first();

            @endphp
            @if ($officeInTime)
                <div id="office-in-result" class="alert alert-info">
                    Your office working session was started at
                    <strong>{{ \Carbon\Carbon::parse($officeInTime->office_in_time)->format('h:i A') }}</strong>
                </div>
            @endif

            @if ($officeOutTime)
                <div id="office-out-result" class="alert alert-info">
                    Your office working session ended at
                    <strong>{{ \Carbon\Carbon::parse($officeOutTime->office_out_time)->format('h:i A') }}</strong>
                </div>
            @endif
            @if ($lunchInTime)
                <div id="lunch-in-result" class="alert alert-info">
                    Your lunch break started at
                    <strong>{{ \Carbon\Carbon::parse($lunchInTime->lunch_in_time)->format('h:i A') }}</strong>
                </div>
            @endif

            @if ($lunchOutTime)
                <div id="lunch-out-result" class="alert alert-info">
                    Your lunch break ended at
                    <strong>{{ \Carbon\Carbon::parse($lunchOutTime->lunch_out_time)->format('h:i A') }}</strong>
                </div>
            @endif

            <!-- Break In/Out Result -->
            @if ($breakInTime)
                <div id="lunch-in-result" class="alert alert-info">
                    Your break started at
                    <strong>{{ \Carbon\Carbon::parse($breakInTime->break_in_time)->format('h:i A') }}</strong>
                </div>
            @endif

            @if ($breakOutTime)
                <div id="lunch-out-result" class="alert alert-info">
                    Your break ended at
                    <strong>{{ \Carbon\Carbon::parse($breakOutTime->break_out_time)->format('h:i A') }}</strong>
                </div>
            @endif

        </div>

    </div>
</div>

@include('layouts.footer')

<script>
  

    function OfficeClockInOut(action) {
        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to ${action.replace('-', ' ')}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, do it!',
            cancelButtonText: 'No, cancel!',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/attendance/office`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            action
                        })
                    }).then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire('Success!', data.message, 'success').then(() => {
                            location.reload(); // Reload the page after showing the success message
                        });
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'There has been a problem with your fetch operation: ' + error,
                            'error');
                    });
            }
        });
        return false;
    }

    function LunchClockInOut(action) {
        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to ${action.replace('-', ' ')}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, do it!',
            cancelButtonText: 'No, cancel!',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/attendance/lunch`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            action
                        })
                    }).then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire('Success!', data.message, 'success').then(() => {
                            location.reload(); // Reload the page after showing the success message
                        });
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'There has been a problem with your fetch operation: ' + error,
                            'error');
                    });
            }
        });
        return false;
    }

    function handleBreak(action) {
        let confirmationText, successMessage;

        if (action === 'break') {
            confirmationText = 'Do you want to take a break?';
            successMessage = 'Break Taken. Enjoy your break!';
        } else if (action === 'stop') {
            confirmationText = 'Do you want to stop the break?';
            successMessage = 'Break Stopped. Welcome back!';
        }

        Swal.fire({
            title: 'Are you sure?',
            text: confirmationText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: action === 'break' ? 'Yes, take a break' : 'Yes, stop break',
            cancelButtonText: 'No, cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/attendance/breaktime`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            action
                        })
                    }).then(response => {
                        if (!response.ok) {
                            // throw new Error('Network response was not ok ' + response.statusText);
                            throw new Error('Please first start Office In');
                        }
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire(successMessage, '', 'success').then(() => {
                            location.reload(); // Reload the page after showing the success message

                        });
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'There has been a problem with your fetch operation: ' + error,
                            'error');
                    });
            }
        });
    }
</script>
<script>
$(document).ready(function () {

    var currentTime = new Date();
    var start = new Date(currentTime);

    start.setHours(0, 0, 0, 0);

    var diff = Math.floor(
        (currentTime.getTime() - start.getTime()) / 1000
    );

    var clock = $('#clock1').FlipClock(diff, {
        clockFace: 'HourlyCounter',
        countdown: false,
        autoStart: true
    });

});
</script>
