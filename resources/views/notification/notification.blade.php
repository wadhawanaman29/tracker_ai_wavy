@include('layouts.header')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Notification</h4>

        <div class="col-md mb-4 mb-md-0">
          <div class="card">
            <h5 class="card-header">All Notifications</h5>
            <div class="table-responsive text-nowrap">
              <table class="table">
                <thead>
                  <tr>
                    <th>Notification</th>
                    <th>Start&nbsp;Date</th>
                    <th>End&nbsp;Date</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @if ($deposits->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center">No data found</td>
                    </tr>
                  @else
                  @foreach($deposits as $index => $notification)
                      @php
                          $currentDate = now()->startOfDay(); // Get the current date and time
                          $startDate = \Carbon\Carbon::parse($notification->startdate);
                          $endDate = \Carbon\Carbon::parse($notification->enddate);
                          $status = ''; // Initialize status variable
              
                          if ($currentDate->between($startDate, $endDate)) {
                              $status = '<span class="badge bg-label-info me-1">Active</span>';
                          } elseif ($currentDate->lt($startDate)) {
                              $status = '<span class="badge bg-label-warning me-1">Coming Soon</span>';
                          } elseif ($currentDate->gt($endDate)) {
                              $status = '<span class="badge bg-label-success me-1">Expired</span>';
                          }
              
                          // Determine the class for the <tr> based on index parity
                          $rowClass = $index % 2 === 0 ? 'table-primary' : 'table-active';
                      @endphp
                      <tr class="{{ $rowClass }}">
                          <td><i class="fab fa-react fa-lg text-info me-3"></i> <strong>{{ ucfirst($notification->notification) }}</strong></td>
                          <td>{{ $startDate->format('d-m-Y') }}</td>
                          <td>{{ $endDate->format('d-m-Y') }}</td>
                          <td>{!! $status !!}</td>
                          <td>
                            <a href="javascript:void(0);" class="delete-btn" data-form-id="deleteForm{{ $notification->id }}" onclick="submitDeleteForm({{ $notification->id }})">
                              <i class="bx bx-trash me-1"></i>
                          </a>
                          <form action="{{ route('deletenotification', $notification->id) }}" method="get" id="deleteForm{{ $notification->id }}" style="display: none;">
                              @csrf
                              @method('DELETE')
                          </form>
                              {{-- <a class="delete-btn text-danger red" href="{{ route('deletenotification', $notification->id) }}" ><i class="bx bx-trash me-1"></i></a>   --}}
                          </td>
                      </tr>
                  @endforeach
                  @endif
              </tbody>
              
              

              </table>
            </div>
          </div>
          </div>

</div>

<?php
// echo "<pre>";
//   print_r($deposits);
// echo "</pre>";
?>

@include('layouts.footer')


