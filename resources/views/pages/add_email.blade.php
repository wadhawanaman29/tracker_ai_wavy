@include('layouts.header')


<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Email Setting</h4>

    <div class="row">
      <div class="col-md-6">
        <div class="card mb-4">
        
            <div class="card-body">

              <form action="{{ route('store-email') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="email" class="form-label">Email(s):</label>
                    <textarea id="email" name="email" class="form-control" rows="3" required>@if($emails){{ implode(', ', $emails) }}@endif</textarea>
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <br>
                    <small class="form-text text-muted">Enter multiple emails separated by commas (e.g., email1@example.com, email2@example.com)</small>
                  
                  </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-outline-primary">Add</button>
                </div>
            </form>
            
            </div>
        </div>
      </div>
     
    </div>
  </div>

 
@include('layouts.footer')
