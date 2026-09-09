@include('layouts.header')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Users Report</h4>
    <div class="card">
        <h5 class="card-header">View User Report</h5>
        <div class="table-responsive text-nowrap">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Sr.No</th>
                <th>Name</th>
                <th>Total&nbsp;Hours</th>
                <th>Status</th>
                <th>View&nbsp;Report</th>
            </tr>
            </thead>
        

            <tbody class="table-border-bottom-0">
                @php 
                $startSerialNumber = ($data->currentPage() - 1) * $data->perPage() + 1;    
                @endphp
                @foreach ($data as $userdata)
                <tr>
                    <td>{{ $startSerialNumber++ }}</td>
                    <td>{{ $userdata->name }}</td>
                    <td>
                        @if (isset($userdata->monthHourCounts[0]['total_hours']))
                            {{ $userdata->monthHourCounts[0]['total_hours'] }}
                        @else
                            0
                        @endif
                    </td>
            
                    <td>
                        @if ($userdata->user_status == 1)
                            <span class="badge bg-label-success me-1">Active</span>
                        @else
                            <span class="badge bg-label-danger me-1">Inactive</span>
                        @endif
                    </td>
                    <td><a href="{{ route('viewuser', $userdata->id) }}">View</a></td>
                </tr>
            @endforeach
            
            </tbody>
            
            </tbody>
        </table>
        </div>
    </div>
</div>
@include('layouts.footer')
