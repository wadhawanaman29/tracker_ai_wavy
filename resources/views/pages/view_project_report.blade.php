@include('layouts.header')

<?php
// echo "<pre>";
// print_r($data);
// echo "</pre>";
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Daily Report</h4>
    <div class="card">
        <h5 class="card-header">View Daily Report</h5>
        <div class="table-responsive text-nowrap" >
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Sr.No</th>
                    <th>Project&nbsp;Name</th>
                    <th>Total&nbsp;Hours</th>
                    <th>Status</th>
                </tr>
                </thead>
            
                <tbody class="table-border-bottom-0">
                    @php 
                    $startSerialNumber = ($data->currentPage() - 1) * $data->perPage() + 1;    
                    @endphp
                
                    @if ($data->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">No data found</td>
                        </tr>
                    @else
                        @foreach ($data as $projectdata)                                              

                        <tr>
                            <td>{{ $startSerialNumber++ }}</td>
                            <td>{{ $projectdata['name'][0]['project_name'] }}</td>
                            @php
                            // $hours = floor($projectdata->project_time / 100);
                            // $minutes = $projectdata->project_time % 100;

                            $hours = intdiv($projectdata->project_time, 60); // Integer division to get hours
                            $minutes = $projectdata->project_time % 60; 
                        @endphp
                        {{-- <td width="20">{{ sprintf('%d:%02d', $hours, $minutes) }} Hour</td> --}}

                             <td>{{ sprintf('%d:%02d', $hours, $minutes) }} Hour</td>
                            <td>
                                @if ($projectdata->project_status == 1)
                                    <span class="badge bg-label-success me-1">Active</span>
                                @else
                                    <span class="badge bg-label-danger me-1">Inactive</span>
                                @endif
                            </td>
                           
                        </tr>
                        @endforeach
                    @endif
                </tbody>
                
            </table>
        </div>

       
    </div>

    <div class="row mt-4">
        <div class="col-md-12 col-sm-12 mx-auto">
            <div class="custom-pagination-wrapper">
                <div class="pagination justify-content-center">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
    
</div>
@include('layouts.footer')
