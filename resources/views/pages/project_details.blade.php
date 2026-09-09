@include('layouts.header')

@php
    use Illuminate\Support\Str;
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Project Report by Employee </h4>
    
    {{-- <div class="container"> --}}
        <div class="card mb-2">
            <h5 class="card-header">Project Details</h5>
            <div class="px-4">
                @foreach ($data as $project)
                   
                    <div class="row">
                        <div class="col-md-6"><p><strong>Project Name:</strong> {{ $project->project_name }}</p></div>
                        <div class="col-md-6"><p><strong>Total Hours:</strong>

                            @php
                                // $hours = floor($project->total_project_time / 100);
                                // $minutes = $project->total_project_time % 100;

                                $hours = intdiv($project->total_project_time, 60); // Integer division to get hours
                                $minutes = $project->total_project_time % 60; 
                            @endphp
                              {{ $hours . ' Hour' . ($minutes > 0 ? ' ' . $minutes . ' Minutes' : '') }}
                             {{-- {{ $project->total_project_time }} &nbsp;Hours</p> --}}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
   
            @if(count($employees) > 0 )
            @foreach ($employees as $employee)
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="20">Sr.No</th>
                                    <th width="20">Employee&nbsp;Name</th>
                                    <th width="20">Total&nbsp;Hours</th>
                                    {{-- <th width="20">Employee Comment</th> --}}
                                    <th width="20">Task&nbsp;Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <tr>
                                    <td width="20">{{ $loop->iteration }}</td>
                                    <td width="20">{{ $employee->employee_name }}</td>
                                    @php
                                        // $hours = floor($employee->project_time / 100);
                                        // $minutes = $employee->project_time % 100;
                                        $hours = intdiv($employee->project_time, 60); // Integer division to get hours
                                        $minutes = $employee->project_time % 60; 
                                    @endphp
                                    <td width="20">{{ sprintf('%d:%02d', $hours, $minutes) }} Hour</td>
                                    {{-- <td width="20">{{ $employee->project_time }} hour</td> --}}
                                    {{-- <td width="20">{{ Str::words(strip_tags($employee->employee_comment), 10) }} &nbsp;<a href="{{ route('view_employee_full_report', $employee->assignment_id) }}">View</a></td> --}}
                                    <td width="20">{{ date('d-m-Y' , strtotime($employee->task_date)) }}</td>
                                    <td width="20"><a href="{{ route('view_employee_full_report', $employee->assignment_id) }}">View</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
           <br>
            @endforeach
            @else
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Sr.No</th>
                                    <th>Employee Name</th>
                                    <th>Total Hours</th>
                                    <th>Employee Comment</th>
                                    <th>Task Date</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <tr class="text-center">
                                   <td colspan="5">Not worked yet!</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>
    {{-- </div> --}}
</div>
 
    

@include('layouts.footer')
