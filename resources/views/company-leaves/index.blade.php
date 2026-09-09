@include('layouts.header')

<div class="container-xxl flex-grow-1 container-p-y">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bx bx-check-circle me-1"></i>
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    <div class="row mb-4">

        <div class="col-md-6">
            {{-- 
            <div class="card stats-card">
                <div class="card-body">

                    <h6 class="text-muted mb-2">
                        Total Relaxation  Leaves
                    </h6>

                    <h2 class="mb-0">
                        {{ $leaves->total() }}
                    </h2>

                </div>
            </div> --}}

        </div>

        <div class="col-md-6 d-flex justify-content-end align-items-center">

            <a href="{{ route('add-leave') }}" class="btn btn-primary">

                <i class="bx bx-plus me-1"></i>
                Add Leave

            </a>

        </div>

    </div>

    <div class="card leave-list-card">

        <div class="leave-list-header">
            <h4>
                <i class="bx bx-calendar-check me-2"></i>
                Relaxation   List
            </h4>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover custom-table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Relaxation  Title</th>
                            <th>Relaxation  Type</th>
                            <th>Date Details</th>
                            <th>Time Details</th>
                            <th>Created On</th>
                            <th width="130">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($leaves as $leave)
                            <tr>

                                <td>
                                    {{ $loop->iteration + ($leaves->currentPage() - 1) * $leaves->perPage() }}
                                </td>

                                <td>
                                    <strong>{{ $leave->name }}</strong>
                                </td>

                                <td>

                                    @if ($leave->leave_type == 'full_day')
                                        <span class="badge-full">
                                            Full Day
                                        </span>
                                    @elseif($leave->leave_type == 'half_day')
                                        <span class="badge-half">
                                            Half Day
                                        </span>
                                    @else
                                        <span class="badge-short">
                                            Short Leave
                                        </span>
                                    @endif

                                </td>

                                <td>

                                    @if ($leave->leave_type == 'full_day')
                                        <div class="leave-date-box">

                                            <div>
                                                <strong>From:</strong>
                                                {{ date('d M Y', strtotime($leave->start_date)) }}
                                            </div>

                                            <div>
                                                <strong>To:</strong>
                                                {{ date('d M Y', strtotime($leave->end_date)) }}
                                            </div>

                                        </div>
                                    @else
                                        <div class="leave-date-box">

                                            {{ date('d M Y', strtotime($leave->leave_date)) }}

                                        </div>
                                    @endif

                                </td>

                                <td>

                                    @if ($leave->leave_type != 'full_day')
                                        <span class="text-success fw-semibold">
                                            {{ date('h:i A', strtotime($leave->start_time)) }}
                                        </span>

                                        <br>

                                        <span class="text-danger fw-semibold">
                                            {{ date('h:i A', strtotime($leave->end_time)) }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            N/A
                                        </span>
                                    @endif

                                </td>

                                <td>
                                    {{ $leave->created_at ? $leave->created_at->format('d M Y') : '-' }}
                                </td>

                                <td>

                                    {{-- <a href="{{ route('leave.edit', $leave->id) }}"
                                       class="btn btn-warning btn-sm action-btn"
                                       title="Edit">

                                        <i class="bx bx-edit"></i>

                                    </a> --}}

                                    <form action="{{ route('destroy', $leave->id) }}" method="POST" class="d-inline">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn  btn-sm action-btn"
                                            onclick="return confirm('Delete this leave?')">
                                            <i class="bx bx-trash"></i>
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7">

                                    <div class="empty-state text-center">

                                        <i class="bx bx-calendar-x"></i>

                                        <h5 class="mt-3">
                                            No Leave Records Found
                                        </h5>

                                        <p class="text-muted">
                                            Click the "Add Leave" button to create your first leave.
                                        </p>

                                    </div>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-4">
                {{ $leaves->links() }}
            </div>

        </div>

    </div>

</div>

@include('layouts.footer')
