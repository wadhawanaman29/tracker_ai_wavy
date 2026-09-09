@include('layouts.header')


<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Add Holiday</h4>

    <div class="row">
      <div class="col-md-6">
        <div class="card mb-4">
        
            <div class="card-body">

                    <form action="{{ route('store-holiday') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Holiday Name:</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="date" class="form-label">Date:</label>
                            <input type="date" id="date" name="date" value="{{ date('Y-m-d') }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                          <button type="submit" class="btn btn-outline-primary">Add Holiday</button>
                      </div>
                       
                    </form>

            </div>
        </div>
      </div>
     
    </div>
  </div>

@include('layouts.footer')
