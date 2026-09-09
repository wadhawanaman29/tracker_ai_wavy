@include('layouts.header')



<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Users</h4>
    <div class="d-flex justify-content-end">
        <div class="d-flex justify-content-end gap-2">
            <form id="filterForm" class="d-flex gap-2" method="GET" action="{{ route('users') }}">
                {{-- <input type="date" name="date" value="{{ request('date') }}" class="form-control" /> --}}
                <input type="search" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="Search" />
                <button class="btn btn-primary" type="submit" id="Apply_btn">Apply</button>
                <button type="button" id="Reset_btn" class="btn  btn-primary" data-url="{{ route('users') }}">Reset
                </button>
            </form>

        </div>
    </div>
    <div class="card">
        <h5 class="card-header">Users List</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>
                            <a href="{{ route('users', array_merge(request()->all(), ['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                style="color: #566a7f">
                                Name <i class="bx bx-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('users', array_merge(request()->all(), ['sort' => 'email', 'direction' => request('sort') == 'email' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                style="color: #566a7f">
                                Email <i class="bx bx-sort"></i>
                            </a>
                        </th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $startSerialNumber = ($data->currentPage() - 1) * $data->perPage() + 1;
                    @endphp

                    @forelse ($data as $userdata)
                        <tr>
                            <td>{{ $startSerialNumber++ }}</td>
                            <td>{{ $userdata->name }}</td>
                            <td>{{ $userdata->email }}</td>
                            <td>
                                @if ($userdata->user_status == 1)
                                    <span class="badge bg-label-success me-1 status" data-user="{{ $userdata->id }}"
                                        style="cursor:pointer">Active</span>
                                @else
                                    <span class="badge bg-label-danger me-1 status" data-user="{{ $userdata->id }}"
                                        style="cursor:pointer">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('editUser', $userdata->id) }}"><i
                                        class="bx bx-edit-alt me-1"></i></a>

                                <a href="javascript:void(0);" class="delete-btn"
                                    onclick="submitDeleteForm({{ $userdata->id }})">
                                    <i class="bx bx-trash me-1"></i>
                                </a>

                                <form action="{{ route('deleteUser', $userdata->id) }}" method="POST"
                                    id="deleteForm{{ $userdata->id }}" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No data found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-12 col-sm-12 mx-auto">
            <div class="custom-pagination-wrapper d-flex justify-content-center">
                {{-- {{ $data->links() }} --}}
                {{ $data->appends(['sort' => request('sort'), 'direction' => request('direction')])->links() }}

            </div>
        </div>
    </div>
</div>

@include('layouts.footer')
{{-- <script>
    $(document).ready(function() {


        $(document).on('click', '.status', async function(e) {
            // e.preventDefault();

            let userId = $(this).data('user');

            Swal.fire({
                title: "Are you sure?",
                text: "User Status Will be Updated!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Update it!"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    let url = `/updateStatus/${userId}`

                    let response = await ajaxCall({}, 'POST', url)


                    if (response.success) {
                        Swal.fire({
                            title: "Updated!",
                            text: response.message,
                            icon: "success"
                        });

                        if (response.status === 'active') {
                            $(this).removeClass('bg-label-danger');
                            $(this).addClass('bg-label-success');
                            $(this).text(response.status);
                        } else if (response.status === 'inactive') {
                            $(this).addClass('bg-label-danger');
                            $(this).removeClass('bg-label-success');
                            $(this).text(response.status);
                        }
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: response.message,
                            icon: "error"
                        });
                    }


                }
            });

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to ${action.replace('-', ' ')}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, do it!',
                cancelButtonText: 'No, cancel!',
            }).then((result) => {

            });



        });
    });
</script> --}}
<script>
    $(document).ready(function () {
        $(document).on('click', '.status', async function (e) {
            let userId = $(this).data('user');
            let badgeElement = $(this); 

            Swal.fire({
                title: "Are you sure?",
                text: "User Status Will be Updated!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Update it!"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    let url = `/updateStatus/${userId}`;
                    let response = await ajaxCall({}, 'POST', url);

                    if (response.success) {
                        Swal.fire({
                            title: "Updated!",
                            text: response.message,
                            icon: "success"
                        });

                        if (response.status === 'active') {
                            badgeElement.removeClass('bg-label-danger').addClass('bg-label-success').text('Active');
                        } else if (response.status === 'inactive') {
                            badgeElement.removeClass('bg-label-success').addClass('bg-label-danger').text('Inactive');
                        }
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: response.message,
                            icon: "error"
                        });
                    }
                }
            });
        });
    });
</script>
