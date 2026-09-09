@include('layouts.header')


<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <h5 class="card-header">Employee Full Report</h5>
    <div class="px-4">
        <p><strong>Employee Name:</strong> {{ $employee->name }}</p>
        <p><strong>Email:</strong> {{ $employee->email }}</p>
        <!-- Add other employee details as needed -->
    </div>
    
    <div class="card mt-3">
        <div class="card-body">
             <h5 class="card-title">Project Details</h5>
            <p><strong>Project Name:</strong> {{ $project->project_name }}</p>
            @php
              
                $hours = intdiv($assignment->project_time, 60);
                $minutes = $assignment->project_time % 60; 
            @endphp
           {{-- <td width="20">{{ sprintf('%d:%02d', $hours, $minutes) }} Hour</td> --}}
           <p><strong>Total Hour:</strong> {{ sprintf('%d:%02d', $hours, $minutes) }} Hour</p>
            {{-- <p><strong>Total Hour:</strong> {{ $assignment->project_time }}</p> --}}
            {{-- <p><strong>Employee Comment:</strong> {{ trim(preg_replace('/\s+/', ' ', str_replace('&nbsp;', ' ', strip_tags($project->comment)))) }}</p> --}}
            <p><strong>Employee Comment:</strong></p>
            <div id="editor">
              {!! $assignment->comment !!}
            </div>
            <!-- Add other project details as needed -->
        </div>
    </div>
</div>
</div>
@include('layouts.footer')
