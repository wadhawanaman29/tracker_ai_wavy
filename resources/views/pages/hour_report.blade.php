<style>
.mt-3.me-3.d-flex.filter-custom {
    width: 170px;
    height: 40px;
}
</style>
@include('layouts.header')



<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Daily Report</h4>
    <div class="card">

        {{-- <div class="d-flex justify-content-between">
            <div class="">
                <h5 class="card-header ">Assignment Lists</h5>
            </div>
    
            <div class="mt-3 me-3 d-flex ">
                <label for="">Filter by :</label><select class="form-control">
                    <option value="">Select</option>
                    <option value="days">Days</option>
                    <option value="week">Week</option>
                    <option value="month">Months</option>
                    <option value="6_months">6 Months</option>
                    <option value="year">Year</option>
                </select>
            </div>
        </div>
        

        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Project&nbsp;Name</th>
                        <th>Time</th>
                        <th>Created Date</th>
                        <th>Actions</th>
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
                        @foreach ($data as $assinment)
                            <tr>
                                <td>{{ $startSerialNumber++ }}</td>
                                <td>{{ $assinment->project_name }}</td>
                                <td>{{ $assinment->project_time }} Hour</td>
                                <td>{{ date('d.m.Y', strtotime($assinment->created_at)) }} </td>
                                <td>{{ '-' }} </td>
                               
                            </tr>
                        @endforeach
                    @endif
                </tbody>

            </table>
        </div> --}}

        <div class="d-flex justify-content-between">
            <div class="">
                <h5 class="card-header">Assignment Lists</h5>
            </div>
        
            <div class="mt-3 me-3 d-flex filter-custom">
                <label for="">Filter by :</label>
                <select class="form-control" id="filterSelect">
                    <option value="">Select</option>
                    <option value="days">Days</option>
                    <option value="week">Week</option>
                    <option value="month">Months</option>
                    <option value="6_months">6 Months</option>
                    <option value="year">Year</option>
                </select>
            </div>
        </div>
        
        <div class="table-responsive text-nowrap">
            <table class="table table-striped" id="assignmentTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Project&nbsp;Name</th>
                        <th>Time</th>
                        <th>Created&nbsp;Date</th>
                        <th>Actions</th>
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
                        @foreach ($data as $assignment)
                            <tr>
                                <td>{{ $startSerialNumber++ }}</td>
                                <td>{{ $assignment->project_name }}</td>
                                <td>{{ $assignment->project_time }} Hour</td>
                                <td>{{ date('d.m.Y', strtotime($assignment->created_at)) }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('editAssignment', $assignment->id) }}"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                            <form action="{{ route('deleteAssignment', $assignment->id) }}" method="get" id="deleteForm" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="dropdown-item delete-btn">
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
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

<script>
    document.getElementById('filterSelect').addEventListener('change', function() {
        var filterValue = this.value;
        var rows = document.getElementById('assignmentTable').getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var timeValue = parseInt(row.getElementsByTagName('td')[2].innerText.split(' ')[0]);

            if (filterValue === '') {
                row.style.display = '';
            } else if (filterValue === 'days' && timeValue <= 24) {
                row.style.display = '';
            } else if (filterValue === 'week' && timeValue <= 168) {
                row.style.display = '';
            } else if (filterValue === 'month' && timeValue <= 720) {
                row.style.display = '';
            } else if (filterValue === '6_months' && timeValue <= 4320) {
                row.style.display = '';
            } else if (filterValue === 'year' && timeValue <= 8760) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
</script>

