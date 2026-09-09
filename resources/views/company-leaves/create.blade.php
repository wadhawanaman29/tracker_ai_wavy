@include('layouts.header')

<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Success Message --}}
    {{-- @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-2"></i>
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
    @endif --}}

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">

            <strong>
                <i class="bx bx-error-circle me-1"></i>
                Please fix the following errors:
            </strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
    @endif

    <div class="col-lg-8 mx-auto">
        <div class="card leave-card">

            <div class="leave-header">
                <h4>
                    <i class="bx bx-calendar-event me-2"></i>
                    Add Relaxation Leave
                </h4>
            </div>

            <div class="card-body p-4">

                <form action="{{ route('add-leave.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Relaxation Title </label>
                        <input type="text" name="name" class="form-control" placeholder=""
                            value="{{ old('name') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Relaxation  Type</label>

                        <select name="leave_type" id="leaveType" class="form-select">

                            <option value="">Select Relaxation  Type</option>

                            {{-- <option value="full_day"
                                {{ old('leave_type') == 'full_day' ? 'selected' : '' }}>
                                Full Day
                            </option> --}}

                            <option value="half_day" {{ old('leave_type') == 'half_day' ? 'selected' : '' }}>
                                Half Day
                            </option>

                            <option value="short_leave" {{ old('leave_type') == 'short_leave' ? 'selected' : '' }}>
                                Short Leave
                            </option>

                        </select>
                    </div>

                    <!-- Full Day -->
                    <div id="fullDayFields" class="leave-section" style="display:none;">

                        <h6 class="mb-3 fw-bold text-primary">
                            Full Day Leave Details
                        </h6>

                        <div class="row">

                            <div class="col-md-6">
                                <label class="form-label">
                                    Start Date
                                </label>

                                <input type="date" name="start_date" class="form-control"
                                    value="{{ old('start_date') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    End Date
                                </label>

                                <input type="date" name="end_date" class="form-control"
                                    value="{{ old('end_date') }}">
                            </div>

                        </div>
                    </div>

                    <!-- Half Day / Short Leave -->
                    <div id="timeFields" class="leave-section" style="display:none;">

                        <h6 class="mb-3 fw-bold text-primary">
                            Time Based Leave Details
                        </h6>

                        <div class="mb-3">
                            <label class="form-label">
                                Leave Date
                            </label>

                            <input type="date" name="leave_date" class="form-control"
                                value="{{ old('leave_date') }}">
                        </div>

                        <div class="row">

                            <div class="col-md-6">
                                <label class="form-label">
                                    Start Time
                                </label>

                                <input type="time" name="start_time" class="form-control"
                                    value="{{ old('start_time') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    End Time
                                </label>

                                <input type="time" name="end_time" class="form-control"
                                    value="{{ old('end_time') }}">
                            </div>

                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">

                            <i class="bx bx-save me-1"></i>
                            Save Leave

                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</div>

@include('layouts.footer')

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const leaveType = document.getElementById('leaveType');
        const fullDayFields = document.getElementById('fullDayFields');
        const timeFields = document.getElementById('timeFields');

        function toggleFields() {

            fullDayFields.style.display = 'none';
            timeFields.style.display = 'none';

            if (leaveType.value === 'full_day') {
                fullDayFields.style.display = 'block';
            }

            if (
                leaveType.value === 'half_day' ||
                leaveType.value === 'short_leave'
            ) {
                timeFields.style.display = 'block';
            }
        }

        toggleFields();

        leaveType.addEventListener('change', toggleFields);

    });
</script>
