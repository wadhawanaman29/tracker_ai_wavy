@include('layouts.header')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                Attendance Settings
            </h4>

            
        </div>

    </div>

    <form action="{{ route('attendance.settings.update') }}" method="POST">

        @csrf

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">

                <div class="card h-100 border-0 shadow-sm">

                    <div class="card-header  text-white">

                        <h5 class="mb-0">
                            <i class="fa fa-clock me-2"></i>
                            Late Settings
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                              Number of Late Entries Allowed
                            </label>

                            <input
                                type="number"
                                name="late_allow"
                                class="form-control"
                                value="{{ $settings->late_allow ?? 3 }}"
                            >

                        </div>

                        <div>

                            <label class="form-label fw-semibold">
                                Deduction Per Late Points
                            </label>

                            <input
                                type="number"
                                name="late_deduction"
                                class="form-control"
                                value="{{ $settings->late_deduction ?? 10 }}"
                            >

                        </div>

                    </div>

                </div>

            </div>
            <div class="col-lg-4 col-md-6 mb-4">

                <div class="card h-100 border-0 shadow-sm">

                    <div class="card-header  text-white">

                        <h5 class="mb-0">
                            <i class="fa fa-hourglass-half me-2"></i>
                            Short Leave Settings
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Number of Short Leaves Allowed
                            </label>

                            <input
                                type="number"
                                name="short_leave_allow"
                                class="form-control"
                                value="{{ $settings->short_leave_allow ?? 2 }}"
                            >

                        </div>

                        <div>

                            <label class="form-label fw-semibold">
                                Deduction Per Short Leave Points
                            </label>

                            <input
                                type="number"
                                name="short_leave_deduction"
                                class="form-control"
                                value="{{ $settings->short_leave_deduction ?? 5 }}"
                            >

                        </div>

                    </div>

                </div>

            </div>

            {{-- HALF DAY SETTINGS --}}
            <div class="col-lg-4 col-md-6 mb-4">

                <div class="card h-100 border-0 shadow-sm">

                    <div class="card-header  text-white">

                        <h5 class="mb-0">
                            <i class="fa fa-calendar-day me-2"></i>
                            Half Day Settings
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                               Number of Half Days Allowed
                            </label>

                            <input
                                type="number"
                                name="half_day_allow"
                                class="form-control"
                                value="{{ $settings->half_day_allow ?? 1 }}"
                            >

                        </div>

                        <div>

                            <label class="form-label fw-semibold">
                                Deduction Per Half Day Points
                            </label>

                            <input
                                type="number"
                                name="half_day_deduction"
                                class="form-control"
                                value="{{ $settings->half_day_deduction ?? 10 }}"
                            >

                        </div>

                    </div>

                </div>

            </div>

            {{-- ABSENT SETTINGS --}}
            <div class="col-lg-6 col-md-6 mb-4">

                <div class="card h-100 border-0 shadow-sm">

                    <div class="card-header  text-white">

                        <h5 class="mb-0">
                            <i class="fa fa-user-times me-2"></i>
                            Absent Settings
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">
                                   Number of Absences Allowed
                                </label>

                                <input
                                    type="number"
                                    name="absent_allow"
                                    class="form-control"
                                    value="{{ $settings->absent_allow ?? 1 }}"
                                >

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">
                                    Deduction Per Absent Points
                                </label>

                                <input
                                    type="number"
                                    name="absent_deduction"
                                    class="form-control"
                                    value="{{ $settings->absent_deduction ?? 15 }}"
                                >

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- SANDWICH SETTINGS --}}
            <div class="col-lg-6 col-md-6 mb-4">

                <div class="card h-100 border-0 shadow-sm">

                    <div class="card-header  text-white">

                        <h5 class="mb-0">
                            <i class="fa fa-layer-group me-2"></i>
                            Sandwich Policy
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Deduction Per Sandwich Points
                            </label>

                            <input
                                type="number"
                                name="sandwich_deduction"
                                class="form-control"
                                value="{{ $settings->sandwich_deduction ?? 15 }}"
                            >

                        </div>

                        <div class="alert alert-primary mb-0">

                            <i class="fa fa-info-circle me-2"></i>

                            Sandwich deduction applies when leave exists
                            between weekends or holidays.

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- SAVE BUTTON --}}
        <div class="text-end mt-2">

            <button class="btn btn-primary px-5">

                <i class="fa fa-save me-2"></i>

                Save Settings

            </button>

        </div>

    </form>

</div>

@include('layouts.footer')