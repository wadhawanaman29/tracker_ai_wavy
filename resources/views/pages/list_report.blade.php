@include('layouts.header')
@php
    // dd($project);
@endphp
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span>List Report</h4>
    <div class="card">

        <div class="card-body">
            {{-- <form id="filterForm"> --}}
                <form method="GET" action="{{ route('list_report') }}">

                <div class="row">

                    <!-- Hidden inputs to store the current sorting state -->
                    <input type="hidden" id="current-sort" value="project_assignment.created_at">
                    <input type="hidden" id="current-direction" value="asc">
                    <!-- User Select -->
                    <div class="col-md-3 p-1">
                        <label for="user_id" class="form-label font-weight-bold">Employee<span
                                class="text-danger text-center">*</span></label>
                        <select name="user_id" class="form-control" id="user_id">
                            @php
                                $getUsers = getUsers();
                                $selectedUserId = isset($_GET['user_id']) ? $_GET['user_id'] : '';
                                $currentDate = new DateTime();
                                $currentDate->modify('-1 day');
                                $oneDayBack = $currentDate->format('Y-m-d');
                            @endphp
                            <option value="all">All</option> <!-- Adding the "All" option -->
                            @foreach ($getUsers as $val)
                                <option value="{{ $val['id'] }}"
                                    {{ $val['id'] == $selectedUserId ? 'selected' : '' }}>
                                    {{ ucfirst($val['name']) . ' (' . $val['email'] . ')' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 p-1">
                        <label for="project" class="form-label font-weight-bold">
                            Project <span class="text-danger text-center">*</span>
                        </label>
                        <select name="project" class="form-control {{ $errors->has('project') ? 'is-invalid' : '' }}"
                            id="project">
                            @php
                                $selectedProjectId = isset($_GET['project']) ? $_GET['project'] : '';
                            @endphp
                            <option value="all">All</option>
                            @foreach ($project as $item)
                                <option value="{{ $item->id }}"
                                    {{ $item->id == $selectedProjectId ? 'selected' : '' }}>
                                    {{ $item->project_name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('project'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('project') }}</strong>
                            </span>
                        @endif
                    </div>


                    <!-- Date Filter Select -->
                    <div class="col-md-3 p-1">
                        <label for="date_filter" class="form-label font-weight-bold">Date period<span
                                class="text-danger text-center">*</span></label>
                        <div class="input-group">
                            <select class="form-select" name="date_filter" id="date_filter">
                                <option value="">All Dates</option>
                                <option value="date"
                                    {{ isset($dateFilter) && $dateFilter == 'date' ? 'selected' : '' }}>Date</option>
                                <option value="today"
                                    {{ isset($dateFilter) && $dateFilter == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="yesterday"
                                    {{ isset($dateFilter) && $dateFilter == 'yesterday' ? 'selected' : '' }}>Yesterday
                                </option>
                                <option value="this_week"
                                    {{ isset($dateFilter) && $dateFilter == 'this_week' ? 'selected' : '' }}>This Week
                                </option>
                                <option value="last_week"
                                    {{ isset($dateFilter) && $dateFilter == 'last_week' ? 'selected' : '' }}>Last Week
                                </option>
                                <option value="this_month"
                                    {{ isset($dateFilter) && $dateFilter == 'this_month' ? 'selected' : '' }}>This
                                    Month
                                </option>
                                <option value="last_month"
                                    {{ isset($dateFilter) && $dateFilter == 'last_month' ? 'selected' : '' }}>Last
                                    Month
                                </option>
                                <option value="this_year"
                                    {{ isset($dateFilter) && $dateFilter == 'this_year' ? 'selected' : '' }}>This Year
                                </option>
                                <option value="last_year"
                                    {{ isset($dateFilter) && $dateFilter == 'last_year' ? 'selected' : '' }}>Last Year
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Date Input -->
                    <div class="col-md-3 p-1" id="date_input_div" style="display:none;">
                        <label for="month" class="form-label font-weight-bold">Date<span
                                class="text-danger text-center">*</span></label>
                        <div class="input-group">
                            <input type="date" name="month" class="form-control" id="month"
                                value="{{ old('month', $_REQUEST['month'] ?? $oneDayBack) }}"
                                max="{{ date('Y-m-d') }}" required />
                        </div>
                    </div>

                    <script>
                        document.getElementById('date_filter').addEventListener('change', function() {
                            var dateInputDiv = document.getElementById('date_input_div');
                            if (this.value === 'date') {
                                dateInputDiv.style.display = 'block';
                            } else {
                                dateInputDiv.style.display = 'none';
                            }
                        });

                        // Trigger change event on page load to handle pre-selected option
                        document.getElementById('date_filter').dispatchEvent(new Event('change'));
                    </script>


                    <!-- Submit Button -->
                    <div class="col-md-2 p-1">
                        <input class="btn btn-primary pt-2" style="margin-top: 1.4rem!important" type="submit"
                            value="Submit">
                    </div>
                </div>
            </form>
        </div>

    </div>

    <br>
    <div class="card">
        <div class="table-responsive text-nowrap ">

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        {{-- <th>
                                <div
                                class="sort" 
                                data-sort="name" 
                                data-direction="asc" 
                                data-id="{{ isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '' }}" 
                                data-filter="{{ isset($_REQUEST['date_filter']) ? $_REQUEST['date_filter'] : '' }}" 
                                data-month="{{ isset($_REQUEST['month']) ? $_REQUEST['month'] : '' }}">
                                    Name
                                    <i id="sort-icon" class="bx bx-sort"></i>
                            </div>
                            </th> --}}
                        <th>
                            <div class="sortlist" data-sort="project.project_name" data-direction="asc"
                                data-id="{{ isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '' }}"
                                data-filter="{{ isset($_REQUEST['date_filter']) ? $_REQUEST['date_filter'] : '' }}"
                                data-month="{{ isset($_REQUEST['month']) ? $_REQUEST['month'] : '' }}">
                                Project&nbsp;<i id="sort-icon" class="bx bx-sort"></i>
                            </div>
                        </th>
                        <th>
                            <div class="sortlist" data-sort="project_assignment.created_at" data-direction="asc"
                                data-id="{{ isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '' }}"
                                data-filter="{{ isset($_REQUEST['date_filter']) ? $_REQUEST['date_filter'] : '' }}"
                                data-month="{{ isset($_REQUEST['month']) ? $_REQUEST['month'] : '' }}">
                                Task&nbsp;Date&nbsp;<i id="sort-icon" class="bx bx-sort"></i>
                            </div>
                        </th>
                        {{-- <th>Task&nbsp;Date</th>                            --}}
                        {{-- <th>Time</th> --}}

                        <th>
                            <div class="sortlist" data-sort="project_assignment.project_time" data-direction="asc"
                                data-id="{{ isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '' }}"
                                data-filter="{{ isset($_REQUEST['date_filter']) ? $_REQUEST['date_filter'] : '' }}"
                                data-month="{{ isset($_REQUEST['month']) ? $_REQUEST['month'] : '' }}">
                                Time&nbsp;<i id="sort-icon" class="bx bx-sort"></i>
                            </div>
                        </th>
                        <th>Report</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0" id="table-container" id="data_list">
                    <?php
                    $html = '';
                    $startSerialNumber = ($data->currentPage() - 1) * $data->perPage() + 1;
                    
                    if ($data->isEmpty()) {
                        $html .= '<tr><td colspan="7" class="text-center">No records found</td></tr>';
                    } else {
                        foreach ($data as $index => $record) {
                            $hours = intdiv($record->project_time, 60);
                            $minutes = $record->project_time % 60;
                            $formattedDate = date('d/m/Y', strtotime($record->project_date));
                            $formattedTime = sprintf('%d:%02d', $hours, $minutes);
                            $escapedComment = htmlspecialchars($record->comment, ENT_QUOTES, 'UTF-8');
                            $strippedComment = str_replace('&nbsp;', ' ', strip_tags($record->comment));
                    
                            $html .=
                                '<tr>
                                                                                                                                            <td>' .
                                $startSerialNumber++ .
                                '</td>
                                                                                                                                            <td>' .
                                check_user_name($record->assigned_to) .
                                '</td>
                                                                                                                                            <td>' .
                                $record->project_name .
                                '</td>
                                                                                                                                            <td>' .
                                $formattedDate .
                                '</td>
                                                                                                                                            <td>' .
                                $formattedTime .
                                '</td>
                                                                                                                                            <td>' .
                                $record->comment .
                                '</td>
                                                                                                                                            <td>
                                                                                                                                                <span class="view-reason" 
                                                                                                                                                    data-reason="' .
                                $escapedComment .
                                '"
                                                                                                                                                    data-reason="' .
                                $strippedComment .
                                '"
                                                                                                                                                    data-toggle="modal"
                                                                                                                                                    data-target="#exampleModalCenter"
                                                                                                                                                    style="font-size: 14px; cursor: pointer;">View</span>
                                                                                                                                            </td>
                                                                                                                                        </tr>';
                        }
                    }
                    echo $html;
                    
                    ?>
                </tbody>
            </table>

        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12 col-sm-12 mx-auto">
            <div class="custom-pagination-wrapper">
                <div class="pagination justify-content-center" id="pagination-links">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Report Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="reasonDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.min.js"></script> --}}

<!-- AJAX Script -->
{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle date filter visibility
        document.getElementById('date_filter').addEventListener('change', function() {
            var dateInputDiv = document.getElementById('date_input_div');
            dateInputDiv.style.display = this.value === 'date' ? 'block' : 'none';
        });

        // Trigger change event on page load to handle pre-selected option
        document.getElementById('date_filter').dispatchEvent(new Event('change'));

        // Handle form submission
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            loadTableData(); // Load the table data when the form is submitted
        });

        function loadTableData(page = 1, sort = 'project_assignment.project_time', direction = 'asc') {
            var form = document.getElementById('filterForm');
            var formData = new FormData(form); // Collect form data
            formData.append('page', page);
            formData.append('sort', sort); // Add sort parameter
            formData.append('direction', direction); // Add direction parameter

            fetch('{{ route('employee') }}', {
                    method: 'POST', // Use POST
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token
                    },
                    body: formData // Send form data in the body
                })
                .then(response => response.json()) // Expect JSON response
                .then(data => {
                    var tableContainer = document.getElementById('table-container');
                    var pagination = document.getElementById('pagination-links');

                    if (tableContainer) {
                        tableContainer.innerHTML = data.html; // Update table HTML with the response HTML
                    }
                    if (pagination) {
                        pagination.innerHTML = data.pagination;

                        attachPaginationEvents(sort, direction);
                        // attachPaginationEvents(); // Re-attach event listeners after updating the pagination
                    }

                    // Handle sorting
                    document.querySelectorAll('.sortlist').forEach(function(sortElement) {
                        sortElement.addEventListener('click', function() {
                            var sort = this.dataset.sort;
                            var direction = this.dataset.direction;
                            var newDirection = direction === 'asc' ? 'desc' : 'asc';

                            loadSortedData(sort, newDirection, data.user_id, data.month,
                                data.datefilter);
                            this.dataset.direction =
                                newDirection; // Update the sort direction for the next click
                        });
                    });

                    // Re-attach event listeners for viewing reasons
                    attachViewReasonEvents();
                })
                .catch(error => console.error('Error:', error));
        }

        function loadSortedData(sort, direction, userId, month, datefilter) {
            var formData = new FormData();
            formData.append('sort', sort);
            formData.append('direction', direction);
            formData.append('id', userId);
            formData.append('month', month);
            formData.append('filter', datefilter);

            fetch('{{ route('employeedata') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('table-container').innerHTML = data.html;
                    document.getElementById('pagination-links').innerHTML = data.pagination;
                    attachPaginationEvents(sort, direction);
                    // attachPaginationEvents(); // Re-attach event listeners after updating the pagination
                    attachViewReasonEvents(); // Re-attach event listeners for viewing reasons
                })
                .catch(error => console.error('Error:', error));
        }

        // Function to attach click events to pagination links
        function attachPaginationEvents(sort, direction) {
            document.querySelectorAll('#pagination-links a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    let url = new URL(this.href);
                    let page = url.searchParams.get('page');
                    loadTableData(page, sort,
                        direction); // Pass sort and direction to loadTableData
                });
            });
        }


        // Function to attach click events for viewing reasons
        function attachViewReasonEvents() {
            document.querySelectorAll('.view-reason').forEach(function(element) {
                element.addEventListener('click', function() {
                    var reasonText = this.dataset.reason;
                    var formattedText = reasonText
                        .replace(/<\/?p>/g, '\n') // Replace <p> tags with newline
                        .replace(/<br\s*\/?>/g, '\n') // Replace <br> tags with newline
                        .replace(/&nbsp;/g,
                            ' ') // Replace non-breaking spaces with regular spaces
                        .trim(); // Remove any trailing whitespace

                    document.getElementById('reasonDetails').innerHTML = formattedText.replace(
                        /\n/g, '<br>');
                });
            });
        }
    });
</script> --}}

@include('layouts.footer')
