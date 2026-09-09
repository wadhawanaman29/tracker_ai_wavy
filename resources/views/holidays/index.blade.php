@include('layouts.header')

<div class="container-xxl flex-grow-1 container-p-y">
<div class="card">
    <h5 class="card-header">Holidays List</h5>
    <div class="table-responsive text-nowrap">
      <table class="table table-striped">
        <thead>
          <tr>
            <!-- <th>Holiday&nbsp;Name</th>
            <th>Date</th> -->
                <th>
                    <div class="sort" data-sort="name" data-direction="asc">
                    Holiday&nbsp;Name
                        <i class="bx bx-sort"></i>
                    </div>
                </th>
                <th>
                    <div class="sort" data-sort="date" data-direction="asc">
                        Date
                        <i class="bx bx-sort"></i>
                    </div>
                </th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0" id="data_list">
        @if ($holidays->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">No data found</td>
                        </tr>
        @else
          @foreach ($holidays as $index => $holiday)
          <tr>
            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{ $holiday->name }}</strong></td>
            <td>{{ date('d-m-Y', strtotime($holiday->date)) }}</td>
            <td>
                         <a href="javascript:void(0);" class="delete-btn" data-form-id="deleteForm{{ $holiday->id }}" onclick="submitDeleteForm({{ $holiday->id }})">
                              <i class="bx bx-trash me-1"></i>
                          </a>
                          <form action="{{ route('deleteholiday', $holiday->id) }}" method="get" id="deleteForm{{ $holiday->id }}" style="display: none;">
                              @csrf
                              @method('DELETE')
                          </form>
                <!-- <a class="delete-btn text-danger red" href="{{ route('deleteholiday', $holiday->id) }}" ><i class="bx bx-trash me-1"></i></a>                       -->
            </td>
            
          </tr> 
          @endforeach 
          @endif          
        </tbody>
      </table>
    </div>
  </div>
</div>

@include('layouts.footer')


