@include('layouts.header')


<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Change Password</h4>

    <div class="row">
      <div class="col-md-6">
        <div class="card mb-4">
          {{-- <h5 class="card-header">Default</h5> --}}
            <div class="card-body">
               
                <form action="{{ route('storeChangePassword') }}" method="post">
                    @csrf
                
                    <div class="form-group mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control {{ $errors->has('current_password') ? 'is-invalid' : '' }}" id="current_password" name="current_password">
                        @if ($errors->has('current_password'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('current_password') }}</strong>
                            </span>
                        @endif
                    </div>
                
                    <div class="form-group mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control {{ $errors->has('new_password') ? 'is-invalid' : '' }}" id="new_password" name="new_password">
                        @if ($errors->has('new_password'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('new_password') }}</strong>
                            </span>
                        @endif
                    </div>
                
                    <div class="form-group mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control {{ $errors->has('new_password_confirmation') ? 'is-invalid' : '' }}" id="new_password_confirmation" name="new_password_confirmation">
                        @if ($errors->has('new_password_confirmation'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('new_password_confirmation') }}</strong>
                            </span>
                        @endif
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