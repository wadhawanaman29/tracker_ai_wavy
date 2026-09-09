<!-- Footer -->
<footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
        <div class="pull-right">
            {{ config('app.name') }} by <a href="https://wavyinformatics.com/">Wavy Informatics</a>
        </div>
        {{-- <div class="mb-2 mb-md-0">
                    ©
                    <script>
                      document.write(new Date().getFullYear());
                    </script>
                    , made with ❤️ by
                    <a href="https://themeselection.com" target="_blank" class="footer-link fw-bolder">ThemeSelection</a>
                  </div>
                  <div>
                    <a href="https://themeselection.com/license/" class="footer-link me-4" target="_blank">License</a>
                    <a href="https://themeselection.com/" target="_blank" class="footer-link me-4">More Themes</a>
  
                    <a
                      href="https://themeselection.com/demo/sneat-bootstrap-html-admin-template/documentation/"
                      target="_blank"
                      class="footer-link me-4"
                      >Documentation</a
                    >
  
                    <a
                      href="https://github.com/themeselection/sneat-html-admin-template-free/issues"
                      target="_blank"
                      class="footer-link me-4"
                      >Support</a
                    >
                  </div> --}}
    </div>
</footer>
<!-- / Footer -->

<div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
</div>
<!-- / Layout page -->
</div>

<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>
</div>


<!-- Tracker History Modal -->
<div class="modal fade" id="trackerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Tracker History <span id="trackerUserName"></span>
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="text-end mb-3 text-success" id="simpletext"></div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tracker Start</th>
                            <th>Tracker Stop</th>
                            <th>Reason</th>
                            <th>Active</th>
                            <th>Inactive</th>
                            <th>Mouse Clicks</th>
                            <th>Key Presses</th>
                        </tr>
                    </thead>
                    <tbody id="trackerHistoryBody">
                        <tr>
                            <td colspan="8" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-end mt-3 text-success" id="totalActiveTime"></div>
                <div class="text-end mt-3 text-success" id="totalInactiveTime"></div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="dayTrackerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Daily Tracker History - <span id="dayTrackerUserName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="text-end mb-3 text-success" id="dailysimpletext"></div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tracker Start</th>
                            <th>Tracker Stop</th>
                            <th>Reason</th>
                            <th>Active</th>
                            <th>Inactive</th>
                            <th>Mouse Clicks</th>
                            <th>Key Presses</th>
                        </tr>
                    </thead>
                    <tbody id="dayTrackerHistoryBody">
                        <tr>
                            <td colspan="8" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>

                <div class="text-end mt-3 text-success" id="dailytotalActiveTime"></div>
                <div class="text-end mt-1 text-success" id="dailytotalInactiveTime"></div>

                </table>
            </div>

        </div>
    </div>
</div>



<!-- build:js assets/vendor/js/core.js -->
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>

<script src="{{asset('assets/js/main.js?ver=62.9') }}"></script>
{{-- <script src="../assets/js/main.js?ver=62.9"></script> --}}
<script src="{{ asset('assets/js/sweetalert.js') }}"></script>
<script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>
<script src="{{ asset('assets/js/custom-theme.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.css" />
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}}


<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


<script>
    function showToast(message, status) {
        $.toast({
            text: message,
            heading: status,
            icon: status,
            showHideTransition: 'fade',
            allowToastClose: true,
            hideAfter: 3000,
            stack: 5,
            position: 'top-right',
            textAlign: 'left',
            loader: true,
            loaderBg: '#9EC600',
            beforeShow: function() {},
            afterShown: function() {},
            beforeHide: function() {},
            afterHidden: function() {
                // Additional actions after the toast is hidden
            }
        });
    }
</script>

@if ($message = Session::get('success'))
    <script>
        $(document).ready(function() {
            $.toast({
                text: "{{ $message }}",
                heading: 'Success',
                icon: 'success',
                showHideTransition: 'fade',
                allowToastClose: true,
                hideAfter: 3000,
                stack: 5,
                position: 'top-right',
                textAlign: 'left',
                loader: true,
                loaderBg: '#9EC600',
                beforeShow: function() {},
                afterShown: function() {},
                beforeHide: function() {},
                afterHidden: function() {}
            });
        });
    </script>
@elseif ($message = Session::get('error'))
    <script>
        $(document).ready(function() {
            $.toast({
                text: "{{ $message }}",
                heading: 'Error',
                icon: 'error',
                showHideTransition: 'fade',
                allowToastClose: true,
                hideAfter: 3000,
                stack: 5,
                position: 'top-right',
                textAlign: 'left',
                loader: true,
                loaderBg: '#FF0000',
                beforeShow: function() {},
                afterShown: function() {},
                beforeHide: function() {},
                afterHidden: function() {}
            });
        });
    </script>
@endif

<script>
    $(document).on('click', '.view-tracker', function() {

        let userId = $(this).data('user-id');
        let name = $(this).data('name');

        $('#trackerUserName').text(name);

        $('#trackerHistoryBody').html(
            '<tr><td colspan="8" class="text-center">Loading...</td></tr>'
        );

        $('#totalActiveTime').html('');
        $('#totalInactiveTime').html('');
        $('#simpletext').html('');

        $.ajax({
            url: '/tracker-history/' + userId,
            type: 'GET',
            dataType: 'json',

            success: function(response) {
                $('#trackerUserName').html(name + ' - <small>' + response.today_date + '</small>');

                let rows = '';
                let data = response.rows;

                if (!data || data.length === 0) {

                    rows = '<tr><td colspan="8" class="text-center">No tracker data</td></tr>';

                } else {

                    $.each(data, function(index, row) {

                        let stopReason = "Running";

                        if (row.reason_stop === 'manual_stop') {
                            stopReason = "Manual Stop";
                        } else if (row.reason_stop === 'idle_stop') {
                            stopReason = "Idle Stop";
                        }

                        rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${row.start}</td>
                            <td>${row.stop}</td>
                            <td>${stopReason}</td>
                            <td>${row.active}</td>
                            <td>${row.inactive}</td>
                            <td>${row.mouse_clicks ?? 'N/A'}</td>
                            <td>${row.key_presses ?? 'N/A'}</td>
                        </tr>
                    `;
                    });
                }

                $('#trackerHistoryBody').html(rows);

                $('#totalActiveTime').html(
                    `<strong>Active Time: ${response.latest_active}</strong>`
                );
                $('#totalInactiveTime').html(
                    `<strong>InActive Time: ${response.latest_inactive}</strong>`
                );
                $('#simpletext').html(
                    `<strong>Time format: H:i:s</strong>`
                );
            },

            error: function() {
                $('#trackerHistoryBody').html(
                    '<tr><td colspan="8" class="text-danger text-center">Error loading data</td></tr>'
                );
            }
        });

        // Bootstrap 5 Modal
        let modalElement = document.getElementById('trackerModal');
        let modal = new bootstrap.Modal(modalElement);
        modal.show();
    });

    $(document).on('click', '.view-day-tracker', function() {

        let userId = $(this).data('user-id');
        let name = $(this).data('name');
        let date = $(this).data('date');

        let formattedDate = new Date(date).toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });

        $('#dayTrackerUserName').text(name + ' - ' + formattedDate);

        $('#dayTrackerHistoryBody').html(
            '<tr><td colspan="8" class="text-center">Loading...</td></tr>'
        );

        $('#dailytotalActiveTime').html('');
        $('#dailytotalInactiveTime').html('');
        $('#dailysimpletext').html('');

        $.ajax({
            url: '/daily-tracker-history/' + userId,
            type: 'GET',
            data: {
                date: date
            },

            success: function(response) {

                let rows = '';
                let data = response.rows;

                if (!data || data.length === 0) {
                    rows =
                        '<tr><td colspan="8" class="text-center">No tracker data found for this date</td></tr>';
                } else {

                    $.each(data, function(index, row) {

                        let stopReason = "Running";

                        if (row.reason_stop === 'manual_stop') {
                            stopReason = "Manual Stop";
                        } else if (row.reason_stop === 'idle_stop') {
                            stopReason = "Idle Stop";
                        }

                        rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${row.start}</td>
                            <td>${row.stop ? row.stop : 'Running'}</td>
                            <td>${stopReason}</td>
                            <td>${row.active}</td>
                            <td>${row.inactive}</td>
                            <td>${row.mouse_clicks ?? 'N/A'}</td>
                            <td>${row.key_presses ?? 'N/A'}</td>
                        </tr>
                    `;
                    });
                }

                $('#dayTrackerHistoryBody').html(rows);

                $('#dailytotalActiveTime').html(
                    `<strong>Total Active Time: ${response.latest_active}</strong>`
                );

                $('#dailytotalInactiveTime').html(
                    `<strong>Total Inactive Time: ${response.latest_inactive}</strong>`
                );

                $('#dailysimpletext').html(
                    `<strong> Time format: H:i:s</strong>`
                );
            },

            error: function() {
                $('#dayTrackerHistoryBody').html(
                    '<tr><td colspan="8" class="text-danger text-center">Error loading data</td></tr>'
                );
            }
        });

        let modal = new bootstrap.Modal(document.getElementById('dayTrackerModal'));
        modal.show();
    });
</script>

<<script>
    document.addEventListener('DOMContentLoaded', function() {
        const employeeSelect = document.querySelector('select[name="user_id"]');
        const startBox = document.getElementById('startDateBox');
        const endBox = document.getElementById('endDateBox');
        const filterType = document.getElementById('filterType');
        function toggleCustomFields() {
            const isCustom = filterType?.value === 'custom';
            startBox?.classList.toggle('d-none', !isCustom);
            endBox?.classList.toggle('d-none', !isCustom);
        }
        if (employeeSelect) {
            employeeSelect.addEventListener('change', function() {
                toggleCustomFields();
            });
        }
        filterType?.addEventListener('change', toggleCustomFields);
        toggleCustomFields();
    });
</script>

</body>

</html>
