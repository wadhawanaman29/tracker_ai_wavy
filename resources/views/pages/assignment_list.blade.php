@include('layouts.header')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Daily Report</h4>
    <div class="d-flex justify-content-end">
        <div class="d-flex justify-content-end gap-2">
            <form id="filterForm" class="d-flex gap-2" method="GET" action="{{ route('assignment_list') }}">
                <input type="date" name="date" value="{{ request('date') }}" class="form-control" />
                <input type="search" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="Search" />
                <button class="btn btn-primary" type="submit" id="Apply_btn">Apply</button>
                <button type="button" id="Reset_btn" class="btn  btn-primary"
                    data-url="{{ route('assignment_list') }}">Reset </button>
            </form>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Assignment Lists</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <!-- <th>Project&nbsp;Name</th>
                    <th>Task&nbsp;Date</th> -->
                        {{-- <th>
                            <div class="sort" data-sort="project_name" data-direction="asc">
                                Project&nbsp;Name
                                <i class="bx bx-sort"></i>
                            </div>
                        </th> --}}
                        {{-- <th>
                            <div class="sort" data-sort="created_at" data-direction="asc">
                                Task&nbsp;Date
                                <i class="bx bx-sort"></i>
                            </div>
                        </th> --}}
                        @php
                            $currentSort = request('sort');
                            $currentDirection = request('direction') === 'asc' ? 'desc' : 'asc';
                            $isSortedByName = $currentSort === 'project_name';
                        @endphp

                        <th>
                            <a href="{{ route('assignment_list', ['sort' => 'project_name', 'direction' => $isSortedByName ? $currentDirection : 'asc']) }}"
                                style="color: #566a7f">
                                Project&nbsp;Name
                                @if ($isSortedByName)
                                    <i class="bx {{ request('direction') === 'asc' ? 'bx-sort' : 'bx-sort' }}"></i>
                                @else
                                    <i class="bx bx-sort"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            @php
                                $currentSort = request('sort');
                                $currentDirection = request('direction') === 'asc' ? 'desc' : 'asc';
                                $isCurrent = $currentSort === 'project_date';
                            @endphp
                            <a href="{{ route('assignment_list', ['sort' => 'project_date', 'direction' => $isCurrent ? $currentDirection : 'asc']) }}"
                                style="color: #566a7f">
                                Task&nbsp;Date
                                @if ($isCurrent)
                                    <i class="bx {{ request('direction') === 'asc' ? 'bx-sort' : 'bx-sort' }}"></i>
                                @else
                                    <i class="bx bx-sort"></i>
                                @endif
                            </a>
                        </th>
                        <th>Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody class="table-border-bottom-0" id="data_list">
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
                                <td>{{ date('d.m.Y', strtotime($assinment->project_date)) }} </td>
                                @php
                                    // $hours = floor($assinment->project_time / 100);
                                    // $minutes = $assinment->project_time % 100;

                                    $hours = intdiv($assinment->project_time, 60); // Integer division to get hours
                                    $minutes = $assinment->project_time % 60;
                                @endphp
                                <td>{{ sprintf('%d:%02d', $hours, $minutes) }} Hour</td>
                                {{-- <td>{{ $assinment->project_time }} Hour</td> --}}

                                <td class="">

                                    <a class="" href="{{ route('editAssignment', $assinment->id) }}">
                                        <i class="bx bx-edit-alt me-1"></i>
                                    </a>
                                    {{-- <a href="{{ route('deleteAssignment', $assinment->id) }}" 
                                       class="delete-btn" 
                                       onclick="event.preventDefault(); if (confirm('Are you sure you want to delete this assignment?')) { document.getElementById('deleteForm').submit(); }">
                                        <i class="bx bx-trash me-1"></i>
                                    </a>
                                    
                                    <form id="deleteForm" action="{{ route('deleteAssignment', $assinment->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form> --}}
                                    <a href="javascript:void(0);" class="delete-btn"
                                        data-form-id="deleteForm{{ $assinment->id }}"
                                        onclick="submitDeleteForm({{ $assinment->id }})">
                                        <i class="bx bx-trash me-1"></i>
                                    </a>

                                    <form id="deleteForm{{ $assinment->id }}"
                                        action="{{ route('deleteAssignment', $assinment->id) }}" method="POST"
                                        style="display: none;">
                                        @csrf

                                    </form>

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
                    {{-- {{ $data->links() }} --}}
                    {{ $data->appends(['sort' => request('sort'), 'direction' => request('direction'), 'search' => request('search')])->links() }}
                </div>
            </div>
        </div>
    </div>

</div>
@include('layouts.footer')

<script>
    var assignmentListUrl = "{{ route('assignment_list') }}";
</script>
