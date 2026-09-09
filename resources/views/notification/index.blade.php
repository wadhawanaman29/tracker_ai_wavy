@include('layouts.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Write Notification</h4>

    <div class="row">
      <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">              
                <form action="{{ route('deposit') }}" method="post">
                    @csrf
                
                    <div class="form-group mb-3">
                        <label for="notification" class="form-label">Notification</label>
                        <textarea id="notification" class="form-control @error('notification') is-invalid @enderror" 
                                name="notification" autocomplete="off" autofocus>{{ old('notification') }}</textarea>
                        <div id="emailHelp" class="form-text">Enter your notification message here.</div>
                        @error('notification')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                
                    <div class="form-group mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                            name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                        @error('start_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    
                    <div class="form-group mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                               name="end_date" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                        @error('end_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                
                    <div class="form-group">
                        <input class="btn btn-outline-primary" type="submit" value="Submit">
                    </div>
                </form>
                
                
                
                
            </div>
        </div>
      </div>
     
    </div>
  </div>

@include('layouts.footer')
