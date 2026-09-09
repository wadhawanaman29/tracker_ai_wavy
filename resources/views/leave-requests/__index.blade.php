@include('layouts.header')

<style>
    .modal-header button.close {
        border: none;
        background: none;
        font-size: 30px;

    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> All Leave Requests</h4>
    <div class="col-md mb-4 mb-md-0">
        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th><strong>Sr.no</strong></th>
                            @if(Auth::user()->user_type == '0')
                                <th><strong>Requested&nbsp;by</strong></th>
                            @endif
                            @if(Auth::user()->user_type == '1')
                            <th><strong>Reason</strong></th>
                            @endif
                            <th><strong>Leave&nbsp;Type</strong></th>
                            <th><strong>Applied&nbsp;Leave</strong></th>
                            <th><strong>Date&nbsp;of&nbsp;Leave</strong></th>
                            <th><strong>Status</strong></th>
                            @if(Auth::user()->user_type == '1')
                            <th><strong>Action</strong></th>
                            @endif
                            @if(Auth::user()->user_type == '0')
                                <th><strong>Action</strong></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if(Auth::user()->user_type == '0')
                        @php 
                            $startSerialNumber = ($leaveRequests->currentPage() - 1) * $leaveRequests->perPage() + 1;    
                        @endphp

                        @if ($leaveRequests->isEmpty())
                            <tr>
                                <td colspan="6" class="text-center">No data found</td>
                            </tr> 
                        @endif
                           
                        @foreach ($leaveRequests as $leaveRequest)
                            <tr>  
                                <td>{{ $startSerialNumber++ }}</td>  
                                @if(Auth::user()->user_type == '0')               
                                <td>
                                    {{ $leaveRequest->user->name }} <br>
                                    <a href="#" class="view-reason" data-reason="{{ htmlspecialchars($leaveRequest->reason) }}" data-toggle="modal" data-target="#exampleModalCenter"  style="font-size: 14px; ">View</a>
                                </td>
                                @endif
                                @if(Auth::user()->user_type == '1')
                                <td>
                                    <div class="reason-content">
                                        @php
                                            $reason = $leaveRequest->reason;
                                            $trimmedReason = implode(' ', array_slice(explode(' ', $reason), 0, 10));
                                            $isLong = str_word_count($reason) > 10;
                                        @endphp
                                        <span class="trimmed-text">{{ htmlspecialchars($trimmedReason) }}</span>
                                        <span class="full-text" style="display: none;">{{ htmlspecialchars($reason) }}</span>
                                        @if ($isLong)
                                            <a href="#" class="btn-show-more">More...</a>
                                        @endif
                                    </div>
                                </td>
                                @endif
                                <td>
                                    @if ($leaveRequest->total_leave_days == 0.5 && $leaveRequest->leave_time == "")
                                        Half&nbsp;day
                                    @elseif ($leaveRequest->total_leave_days == 0.5)
                                        {{ $leaveRequest->leave_time .' Hour' }}
                                    @else
                                        {{ $leaveRequest->total_leave_days .' day' }}
                                    @endif
                                </td> 
                                <td>
                                    @if ($leaveRequest->created_at)
                                        {{ date('d F Y', strtotime($leaveRequest->created_at)) }}
                                    @endif
                                </td>                                                                                                                                                           
                                <td>
                                    @if ($leaveRequest->start_date == $leaveRequest->end_date)
                                        {{ date('d F Y', strtotime($leaveRequest->start_date)) }}
                                    @else
                                        {{ date('d F Y', strtotime($leaveRequest->start_date)) }} to {{ date('d F Y', strtotime($leaveRequest->end_date)) }}
                                    @endif
                                </td>                                
                                <td>
                                    @php
                                        $status = '';
                                        switch ($leaveRequest->status) {
                                            case 'pending':
                                                $status = '<span class="badge bg-label-info me-1">' . $leaveRequest->status . '</span>';
                                                break;
                                            case 'rejected':
                                                $status = '<span class="badge bg-label-warning me-1">' . $leaveRequest->status . '</span>';
                                                break;
                                            case 'approved':
                                                $status = '<span class="badge bg-label-success me-1">' . $leaveRequest->status . '</span>';
                                                break;
                                            default:
                                                $status = $leaveRequest->status;
                                                break;
                                        }
                                    @endphp
                                
                                    {!! $status !!}
                                </td>






                                

                                @if(Auth::user()->user_type == '0')
                                <td>
                                    @if($leaveRequest->status == 'pending')
                                        <form action="{{ route('leave-requests.approve', $leaveRequest->id) }}" method="POST" style="display: inline-block;">
                                            @method('PUT')
                                            @csrf
                                            <button type="submit" class="btn btn-sm" title="Approve"><i class='bx bx-check' style='font-size: 30px; color: green' ></i></button>
                                        </form>
                                        <form action="{{ route('leave-requests.reject', $leaveRequest->id) }}" method="POST" style="display: inline-block;">
                                            @method('PUT')
                                            @csrf
                                            <button type="submit" class="btn btn-sm" title="Reject"><i class='bx bx-x' style='font-size: 30px; color: red'></i></button>
                                        </form>
                                        {{-- <form action="" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Trash">
                                                <i class='fa fa-trash' style='font-size: 30px; color: red'></i>
                                            </button>
                                        </form> --}}
                                        

                                    @elseif($leaveRequest->status == 'approved')
                                        <form action="{{ route('leave-requests.reject', $leaveRequest->id) }}" method="POST" style="display: inline-block;">
                                            @method('PUT')
                                            @csrf
                                            <button type="submit" class="btn btn-sm" title="Reject"><i class='bx bx-x' style='font-size: 30px; color: red'></i></button>
                                        </form>
                                    @else
                                        <form action="{{ route('leave-requests.approve', $leaveRequest->id) }}" method="POST" style="display: inline-block;">
                                            @method('PUT')
                                            @csrf
                                            <button type="submit" class="btn btn-sm" title="Approve"><i class='bx bx-check' style='font-size: 30px; color: green' ></i></button>
                                        </form>
                                    @endif
                                </td>
                                @endif
                                
                            </tr>
                        @endforeach
                        @else
                        @foreach ($userleaveRequests as $leaveRequest)
                        <tr>
                            <td> {{ $loop->iteration }} </td>
                            @if(Auth::user()->user_type == '0')
                            <td><i class="fab fa-react fa-lg text-info me-3"></i> <strong>{{ $leaveRequest->user->name }}</strong></td>
                           @endif
                           <td>
                             <div class="reason-content">
                                @php
                                    $reason = $leaveRequest->reason;
                                    $trimmedReason = implode(' ', array_slice(explode(' ', $reason), 0, 10));
                                    $isLong = str_word_count($reason) > 10;
                                @endphp
                                <span class="trimmed-text">{{ htmlspecialchars($trimmedReason) }}</span>
                                <span class="full-text" style="display: none;">{{ htmlspecialchars($reason) }}</span>
                                @if ($isLong)
                                    <a href="#" class="btn-show-more">More...</a>
                                @endif
                             </div>
                           </td>
                           <td>
                            @if ($leaveRequest->total_leave_days == 0.5 && $leaveRequest->leave_time=="" )
                                Half&nbsp;day
                            @elseif ($leaveRequest->total_leave_days == 0.5 )
                                 {{ $leaveRequest->leave_time .' Hour' }}
                            @else
                                {{ $leaveRequest->total_leave_days .' day' }}
                            @endif
                           </td>  
                           <td>
                            @if ($leaveRequest->created_at)
                                {{ date('d F Y', strtotime($leaveRequest->created_at)) }}
                            @endif
                        </td>  
                            <td>
                                @if ($leaveRequest->start_date == $leaveRequest->end_date)
                                    {{ date('d F Y', strtotime($leaveRequest->start_date)) }}
                                @else
                                    {{ date('d F Y', strtotime($leaveRequest->start_date)) }} to {{ date('d F Y', strtotime($leaveRequest->end_date)) }}
                                @endif
                            </td>                            
                            <td>
                                @php
                                $status = '';
                                switch ($leaveRequest->status) {
                                    case 'pending':
                                        $status = '<span class="badge bg-label-info me-1">' . $leaveRequest->status . '</span>';
                                        break;
                                    case 'rejected':
                                        $status = '<span class="badge bg-label-warning me-1">' . $leaveRequest->status . '</span>';
                                        break;
                                    case 'approved':
                                        $status = '<span class="badge bg-label-success me-1">' . $leaveRequest->status . '</span>';
                                        break;
                                    default:
                                        $status = $leaveRequest->status;
                                        break;
                                }
                                @endphp
                            
                                {!! $status !!}
                            </td>
                            
                            <td>
                                @if(Auth::user()->user_type == '0' && $leaveRequest->status == 'pending')
                                    <form action="{{ route('leave-requests.approve', $leaveRequest->id) }}" method="POST" style="display: inline-block;">
                                        @method('PUT')
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    &nbsp;
                                    <form action="{{ route('leave-requests.reject', $leaveRequest->id) }}" method="POST" style="display: inline-block;">
                                        @method('PUT')
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                    </form>

                                    @else
                                    @if($leaveRequest->status == 'pending')
                                        <form action="{{ route('leave-requests.delete', $leaveRequest->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this leave request?');">
                                            @method('GET')
                                            @csrf
                                            <button type="submit" class="btn btn-sm" title="Delete"><i class='bx bx-x' style='font-size: 30px; color: red'></i></button>
                                        </form>
                                    @endif  
                                @endif
                            </td>

                            {{-- @if(Auth::user()->user_type == '1')
                            <td>                             
                                @if($leaveRequest->status == 'pending')
                                    <form action="{{ route('leave-requests.delete', $leaveRequest->id) }}" method="POST" style="display: inline-block;">
                                        @method('GET')
                                        @csrf
                                        <button type="submit" class="btn btn-sm" title="Delete"><i class='bx bx-x' style='font-size: 30px; color: red'></i></button>
                                    </form>
                                @endif
                            </td>
                            @endif --}}
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>             
                
            </div>
            
            <div class="row mt-4">
                <div class="col-md-12 col-sm-12 mx-auto">
                    <div class="custom-pagination-wrapper">
                        <div class="pagination justify-content-center">
                            @if(Auth::user()->user_type == '0')
                                {{ $leaveRequests->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Reason Details</h5>
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



@include('layouts.footer')

<!-- jQuery and Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        $('.view-reason').on('click', function() {
            var reason = $(this).data('reason');
            $('#reasonDetails').text(reason);
        });

        $('.btn-show-more').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var $reasonContent = $btn.closest('.reason-content');
            var $trimmedText = $reasonContent.find('.trimmed-text');
            var $fullText = $reasonContent.find('.full-text');

            // Toggle between trimmed and full text
            $trimmedText.toggle();
            $fullText.toggle();

            // Change button text based on current state
            if ($btn.text() === 'More...') {
                $btn.text('Less');
            } else {
                $btn.text('More...');
            }
        });
    });
</script>
