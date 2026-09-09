@include('layouts.header')



<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Today Attendence Report Status</h4>
    <div class="card">
        <h5 class="card-header">Attendence Report</h5>
        <div class="table-responsive text-nowrap">
            @if (empty($data))
                <p>No attendance records found.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            {{-- <th>Date</th> --}}
                            <th>Office&nbsp;In</th>
                            <th>Office&nbsp;Out</th>
                            <th>Lunch&nbsp;In</th>
                            <th>Lunch&nbsp;Out</th>
                            {{-- <th>Break&nbsp;In</th>
                        <th>Break&nbsp;Out</th> --}}
                            <th>Active</th>
                            <th>InActive</th>

                            <th>Work&nbsp;Hour</th>
                            <th>view History</th>
                            {{-- <th>IP&nbsp;Address</th> --}}
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @foreach ($data as $index => $record)
                        <?php 
                            // echo"<pre>";
                            //     print_r($record);
                            //     die("ppp");
                        ?>
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $record['name'] }}</td>
                                {{-- <td>{{ date('d M Y' , strtotime($record['date'])) }}</td> --}}
                                <td class="{{ $record['office_in'] == 'N/A' ? '' : 'text-success' }}">
                                    {{ $record['office_in'] }}</td>
                                <td class="{{ $record['office_out'] == 'N/A' ? '' : 'text-success' }}">
                                    {{ $record['office_out'] }}</td>
                                <td class="{{ $record['lunch_in'] == 'N/A' ? '' : 'text-success' }}">
                                    {{ $record['lunch_in'] }}</td>
                                <td class="{{ $record['lunch_out'] == 'N/A' ? '' : 'text-success' }}">
                                    {{ $record['lunch_out'] }}</td>
                                {{-- <td class="{{ $record['break_in_time'] == 'N/A' ? '' : 'text-success' }}">{{ $record['break_in_time'] }}</td>
                                    <td class="{{ $record['break_out_time'] == 'N/A' ? '' : 'text-success' }}">{{ $record['break_out_time'] }}</td> --}}


                                 <td class="{{ $record['user_active_time'] == '00:00:00' ? '' : 'text-success' }}">{{ $record['user_active_time'] !== '00:00:00' ? $record['user_active_time'] : 'N/A'}}</td>
                                <td class="{{ $record['user_inactive_time'] == '00:00:00' ? '' : 'text-success' }}">{{ $record['user_inactive_time'] !== '00:00:00' ? $record['user_inactive_time'] : 'N/A' }}</td>
                                <td class="{{ ($record['work_hours'] ?? 'N/A') === 'N/A' ? '' : 'text-success' }}">
                                    {{ $record['work_hours'] ?? 'N/A' }}
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="btn btn-sm btn-primary view-tracker"
                                        data-user-id="{{ $record['user_id'] }}" data-name="{{ $record['name'] }}">
                                        View
                                    </a>
                                </td>


                            </tr>
                        @endforeach

                    </tbody>
                </table>
                {{-- <div>
                <h4>Total Work Hours: {{ $totalWorkHours }}</h4>
            </div> --}}
            @endif
        </div>
    </div>



</div>



@include('layouts.footer')
