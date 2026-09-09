@include('layouts.header')


<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">{{ Route::currentRouteName() === 'editProject' ? 'Edit Project' : 'Add Project' }}</h4>

    <div class="row">
      <div class="col-md-6">
        <div class="card mb-4">
          {{-- <h5 class="card-header">Default</h5> --}}
            <div class="card-body">
               
                <form action="{{ Route::currentRouteName() === 'editProject' ? route('updateProject', ['id' => $id]) : route('storeProject') }}" method="post">
                    @csrf
                    {{-- @if(Route::currentRouteName() === 'editProject')
                        @method('PUT')
                    @endif --}}
                
                    <div class="form-group mb-3">
                        <label for="project_name" class="form-label">Project Name</label>
                        <input type="text" class="form-control {{ $errors->has('project_name') ? 'is-invalid' : '' }}" id="project_name" name="project_name" value="{{ old('project_name', $id->project_name ?? '') }}" autofocus placeholder="Name" aria-describedby="nameHelp">
                        <div id="nameHelp" class="form-text"></div>
                        @if ($errors->has('project_name'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('project_name') }}</strong>
                            </span>
                        @endif
                    </div>
                
                    <div class="form-group mb-3">
                        <label for="project_description" class="form-label">Project Description</label>
                        {{-- <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email" name="email" value="{{ old('email', $id->email ?? '') }}" placeholder="Email" aria-describedby="emailHelp"> --}}
                        <textarea name="project_description" id="project_description" cols="30" rows="5" class="form-control {{ $errors->has('project_description') ? 'is-invalid' : '' }}" autofocus>{{ old('project_description', $id->project_description ?? '') }}</textarea>
                        <div id="emailHelp" class="form-text"></div>
                        @if ($errors->has('project_description'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('project_description') }}</strong>
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
