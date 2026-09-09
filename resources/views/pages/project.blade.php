@include('layouts.header')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Project</h4>
    {{-- <div class="d-flex justify-content-end">
        <div class="w-25">
            <form class="input-group">
                <input type="search" name="search" value="{{ isset($search) ? $search : '' }}" class="form-control" placeholder="Search..." /> 
                <button class="btn btn-icon btn-primary"><i class="bx bx-search me-1"></i></button>
            </form>
        </div>
    </div> --}}
    <div class="d-flex justify-content-end">
        <div class="d-flex justify-content-end gap-2">
            <form id="filterForm" class="d-flex gap-2" method="GET" action="{{ route('project') }}">
                <input type="date" name="date" value="{{ request('date') }}" class="form-control" />
                <input type="search" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="Search " />
                <button class="btn btn-primary" type="submit" id="Apply_btn">Apply</button>
                <button type="button" id="Reset_btn" class="btn  btn-primary" data-url="{{ route('project') }}">Reset
                </button>
            </form>
            
        </div>
    </div>

    <div class="card">
        {{-- <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Project List</h5>

            <a href="{{ route('addProject') }}" class="text-muted float-end">
                Add Project
            </a>
        </div> --}}

        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        {{-- <th>Project&nbsp;Name</th> --}}
                        {{-- <th>
                            <div class="sort" data-sort="project_name" data-direction="asc">
                                Project&nbsp;Name
                                <i id="sort-icon" class="bx bx-sort"></i>
                                </a>
                        </th> --}}
                        @php
                            $currentSort = request('sort');
                            $currentDirection = request('direction') === 'asc' ? 'desc' : 'asc';
                            $isSortedByName = $currentSort === 'project_name';
                        @endphp

                        <th>
                            <a href="{{ route('project', ['sort' => 'project_name', 'direction' => $isSortedByName ? $currentDirection : 'asc']) }}"
                                style="color: #566a7f">
                                Project&nbsp;Name
                                @if ($isSortedByName)
                                    <i class="bx {{ request('project') === 'asc' ? 'bx-sort' : 'bx-sort' }}"></i>
                                @else
                                    <i class="bx bx-sort"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <div class="sort" data-sort="project_time" data-direction="asc">
                                Project&nbsp;Time
                                <i id="sort-icon" class="bx bx-sort"></i>
                            </div>
                        </th>
                        <th>
                            <div class="sort" data-sort="created_at" data-direction="asc">
                                Created&nbsp;Date
                                <i id="sort-icon" class="bx bx-sort"></i>
                            </div>
                        </th>
                        <!-- <th>Status</th> -->
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody id="project-data" class="table-border-bottom-0">
                    @php
                        $startSerialNumber = ($data->currentPage() - 1) * $data->perPage() + 1;
                    @endphp
                    @forelse($data as $projectdata)
                        @php
                            $hours = intdiv($projectdata->total_project_time, 60);
                            $minutes = $projectdata->total_project_time % 60;
                        @endphp
                        <tr>
                            <td>{{ $startSerialNumber++ }}</td>
                            <td>{{ $projectdata->project_name }}</td>
                            <td>{{ $hours . ' Hour' . ($minutes > 0 ? ' ' . $minutes . ' Minutes' : '') }}</td>
                            <td>{{ date('d-m-Y', strtotime($projectdata->created_at)) }}</td>
                            <td class="">
                                <a class="" href="{{ route('editProject', $projectdata->id) }}"><i
                                        class="bx bx-edit-alt me-1"></i></a>
                                {{-- <a href="{{ route('deleteProject', $projectdata->id) }}" class="delete-btn"
                                    data-form-id="deleteForm{{ $projectdata->id }}"
                                    onclick="submitDeleteForm({{ $projectdata->id }})">
                                    <i class="bx bx-trash me-1"></i>
                                </a> --}}
                                <a href="javascript:void(0);" class="delete-btn" data-form-id="deleteForm{{ $projectdata->id }}"> <i class="bx bx-trash me-1"></i></a>
                                <form id="deleteForm{{ $projectdata->id }}"
                                    action="{{ route('deleteProject', $projectdata->id) }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <a href="{{ route('view_project_detail', $projectdata->id) }}"><i
                                        class="bx bx-show me-1"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No Data Found!</td>
                        </tr>
                    @endforelse
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


<script type="text/javascript">
    function submitDeleteForm(projectId) {
        console.log('submitDeleteForm called with projectId:', projectId);
        var formId = 'deleteForm' + projectId;
        var form = document.getElementById(formId);

        if (confirm('Are you sure you want to delete this project?')) {
            form.submit();
        }
    }

    // $(document).ready(function() {
    //     // Load initial project data
    //     // loadProjectData();

    //     // Function to load project data
    //     function loadProjectData(sort = 'project_name', direction = 'asc', page = 1) {
    //         $.ajax({
    //             url: "{{ route('project') }}",
    //             type: "GET",
    //             data: {
    //                 sort: sort,
    //                 direction: direction,
    //                 page: page,
    //                 ajax: true
    //             },
    //             success: function(data) {
    //                 $('#project-data').html(data.html);
    //                 $('#pagination-links').html(data.pagination);
    //                 updateSortIcons(sort, direction);
    //             }
    //         });
    //     }

    //     // Update sort icons based on the current sort column and direction
    //     function updateSortIcons(currentSort, currentDirection) {
    //         $('.sort').each(function() {
    //             let sortColumn = $(this).data('sort');
    //             let icon = $(this).find('i');

    //             if (sortColumn === currentSort) {
    //                 if (currentDirection === 'asc') {
    //                     icon.removeClass('bx-sort-down').addClass('bx-sort-up');
    //                 } else {
    //                     icon.removeClass('bx-sort-up').addClass('bx-sort-down');
    //                 }
    //             } else {
    //                 icon.removeClass('bx-sort-up bx-sort-down').addClass('bx-sort');
    //             }
    //         });
    //     }

    // Sort link click handler
    //     $(document).on('click', '.sort', function() {
    //         let sort = $(this).data('sort');
    //         let direction = $(this).data('direction');

    //         // Toggle the direction
    //         direction = (direction === 'asc') ? 'desc' : 'asc';
    //         $(this).data('direction', direction);

    //         loadProjectData(sort, direction);
    //     });

    //     // Pagination link click handler
    //     // $(document).on('click', '.pagination a', function(e) {
    //     //     e.preventDefault();

    //     //     // Get the page number from the link
    //     //     let page = $(this).attr('href').split('page=')[1];

    //     //     // Get current sort and direction from the active sort link
    //     //     let sort = $('.sort[data-sort]').data('sort');
    //     //     let direction = $('.sort[data-sort]').data('direction');

    //     //     loadProjectData(sort, direction, page);
    //     // });
    // });
</script>
