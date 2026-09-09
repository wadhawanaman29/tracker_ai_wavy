@include('layouts.header')



<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Attendence Report</h4>
    <div class="card">
        <h5 class="card-header">Attendence Report List</h5>

        <div class="table-responsive text-nowrap">
            @if (empty($data))
                <p>No attendance records found.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Office&nbsp;In</th>
                            <th>Office&nbsp;Out</th>
                            <th>Lunch&nbsp;In</th>
                            <th>Lunch&nbsp;Out</th>
                            <th>Break&nbsp;Time</th>
                            <th>Active</th>
                            <th>Inactive</th>
                            <th>Office&nbsp;Hours</th>
                            {{-- <th>IP&nbsp;Address</th> --}}
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">

                        <?php
                        
                        // echo"<pre>";
                        //     print_r($data);
                        //     die("op");
                        ?>
                        @foreach ($data as $index => $record)
                            @if ($record['is_sunday'] || $record['is_saturday'])
                                <tr style="background-color: #e9ecef;">

                                    <td>{{ $index + 1 }}</td>

                                    <td>{{ $record['date'] }}</td>

                                    <td colspan="8" class="weekend-text">

                                        {{ $record['is_sunday'] ? 'Sunday' : 'Saturday' }}

                                    </td>

                                </tr>
                            @elseif ($record['is_holiday'])
                                <tr style="background-color: #d1ecf1;">

                                    <td>{{ $index + 1 }}</td>

                                    <td>{{ $record['date'] }}</td>

                                    <td colspan="8" class="fw-bold text-info weekend-text">

                                        {{ $record['holiday_name'] . ' Holiday' }}

                                    </td>

                                </tr>
                            @elseif (
                                \Carbon\Carbon::parse($record['date'])->lt(\Carbon\Carbon::today()) &&
                                    $record['office_in'] == 'N/A' &&
                                    $record['office_out'] == 'N/A')
                                <tr style="background-color: #f8d7da;">

                                    <td>{{ $index + 1 }}</td>

                                    <td>{{ $record['date'] }}</td>

                                    <td colspan="8" class="text-danger fw-bold weekend-text">

                                        Absent

                                    </td>

                                </tr>
                            @else
                                <tr
                                    @if ($record['status'] == 'short_leave') style="background-color: #fff3cd;"
                                        @elseif($record['status'] == 'half_day')
                                            style="background-color: #fae5ff;" @endif>
                                    <td>{{ $index + 1 }}</td>

                                    <td>{{ $record['date'] }}</td>

                                    <td class="{{ $record['office_in'] == 'N/A' ? '' : 'text-color' }}">
                                        {{ $record['office_in'] }}
                                    </td>

                                    <td class="{{ $record['office_out'] == 'N/A' ? '' : 'text-color' }}">
                                        {{ $record['office_out'] }}
                                    </td>

                                    <td class="{{ $record['lunch_in'] == 'N/A' ? '' : 'text-color' }}">
                                        {{ $record['lunch_in'] }}
                                    </td>

                                    <td class="{{ $record['lunch_out'] == 'N/A' ? '' : 'text-color' }}">
                                        {{ $record['lunch_out'] }}
                                    </td>

                                    <td class="{{ $record['break'] == 'N/A' ? '' : 'text-color' }}">
                                        {{ $record['break'] == 'N/A' ? 'N/A' : $record['break'] . ' min' }}
                                    </td>

                                    <td class="{{ $record['active_time'] == 'N/A' ? '' : 'text-color' }}">
                                        {{ $record['active_time'] }}
                                    </td>

                                    <td class="{{ $record['inactive_time'] == 'N/A' ? '' : 'text-color' }}">
                                        {{ $record['inactive_time'] }}
                                    </td>

                                    <td class="{{ $record['work_hours'] == 'N/A' ? '' : 'text-color' }}">
                                        {{ $record['work_hours'] }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                        <tr>
                            <td colspan="7">
                                Total Work Hours: {{ $totalWorkHours }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>

    </div>



</div>
@include('layouts.footer')
