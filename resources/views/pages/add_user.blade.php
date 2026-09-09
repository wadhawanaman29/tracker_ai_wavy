@include('layouts.header')


<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">{{ Route::currentRouteName() === 'editUser' ? 'Edit User' : 'Add User' }}</h4>

    <div class="row">
      <div class="col-md-6">
        <div class="card mb-4">
          {{-- <h5 class="card-header">Default</h5> --}}
            <div class="card-body">
                {{-- <form action="{{ Route::currentRouteName() === 'editUser' ? route('updateUser', ['id' => $id]) : route('storeUser') }}" method="post">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" id="name" name="name" value="{{ old('name', $id->name ?? '') }}" autofocus placeholder="Name" aria-describedby="nameHelp">
                        <div id="nameHelp" class="form-text"></div>
                        @if ($errors->has('name'))
                        <span class="invalid feedback text-danger" role="alert">
                            <strong>{{ $errors->first('name') }}.</strong>
                        </span>
                    @endif
                    </div>
                
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" name="email" value="{{ old('email', $id->email ?? '') }}" autofocus placeholder="Email" aria-describedby="emailHelp">
                        <div id="emailHelp" class="form-text"></div>
                        @if ($errors->has('email'))
                            <span class="invalid feedback text-danger" role="alert">
                                <strong>{{ $errors->first('email') }}.</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group mb-3">
                        <label for="designation" class="form-label">Designation</label>
                        <input type="designation" class="form-control {{ $errors->has('designation') ? ' is-invalid' : '' }}" id="designation" name="designation" value="{{ old('designation', $id->designation ?? '') }}" autofocus placeholder="designation" aria-describedby="designationHelp">
                        <div id="designationHelp" class="form-text"></div>
                        @if ($errors->has('designation'))
                            <span class="invalid feedback text-danger" role="alert">
                                <strong>{{ $errors->first('designation') }}.</strong>
                            </span>
                        @endif
                    </div>
                
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" id="password" name="password" value="{{ old('password', $id->password ?? '') }}" autofocus placeholder="Password" aria-describedby="passwordHelp">
                        <div id="passwordHelp" class="form-text"></div>
                        @if ($errors->has('password'))
                            <span class="invalid feedback text-danger" role="alert">
                                <strong>{{ $errors->first('password') }}.</strong>
                            </span>
                        @endif
                    </div>
                
                    <div class="form-group">
                        <input class="btn btn-outline-primary" type="submit" value="Submit">
                    </div>
                </form> --}}

                <form action="{{ Route::currentRouteName() === 'editUser' ? route('updateUser', ['id' => $id]) : route('storeUser') }}" method="post">
                    @csrf
                    {{-- @if(Route::currentRouteName() === 'editUser')
                        @method('PUT')
                    @endif --}}
                
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" name="name" value="{{ old('name', $id->name ?? '') }}" autofocus placeholder="Name" aria-describedby="nameHelp">
                        <div id="nameHelp" class="form-text"></div>
                        @if ($errors->has('name'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email" name="email" value="{{ old('email', $id->email ?? '') }}" placeholder="Email" aria-describedby="emailHelp">
                        <div id="emailHelp" class="form-text"></div>
                        @if ($errors->has('email'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group mb-3">
                        <label for="designatione" class="form-label">Designation</label>
                    
                        {{-- <select name="designation" id="designation" class="form-control {{ $errors->has('designation') ? 'is-invalid' : '' }}">
                            <option value="">Select</option>
                            @foreach (getDesignations() as $key => $designation)
                                <option value="{{ $key }}" >{{ $designation }}</option>
                            @endforeach
                            @if ($errors->has('designation'))
                                <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('designation') }}</strong>
                                </span>
                            @endif 
                        </select> --}}

                        {{-- <select class="form-control{{ $errors->has('designation') ? ' is-invalid' : '' }}"
                            name="designation" value="{{ old('designation') }}" autofocus name="designation"
                            id="designation">
                            <option value="">Select</option>
                           
                            @foreach ($roles = getUOM(); as $role)
                            <option value="{{ $role->designation }}"
                                {{ isset($designation) && $designation == $role->designation ? 'selected' : '' }}>
                                {{ $role->role }}
                            </option>
                            @endforeach 
                        </select> --}}

                        <select class="form-control{{ $errors->has('designation') ? ' is-invalid' : '' }}"
                            name="designation" id="designation" autofocus>
                            <option value="">Select</option>
                            @foreach (getDesignations() as $designation => $role)
                                <option value="{{ $designation }}" {{ old('designation', $id->designation ?? '') == $designation ? 'selected' : '' }}>
                                    {{ $role }}
                                </option>
                            @endforeach
                        </select>
                        
                        @if ($errors->has('designation'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('designation') }}</strong>
                            </span>
                        @endif

                    </div>

                    <div class="form-group mb-3">
                        <label for="employment_status" class="form-label">Employment Status</label>
                        <select class="form-control{{ $errors->has('employment_status') ? ' is-invalid' : '' }}"
                            name="employment_status" id="employment_status">
                            @php $currentStatus = old('employment_status', $id->employment_status ?? 'permanent'); @endphp
                            <option value="permanent" {{ $currentStatus == 'permanent' ? 'selected' : '' }}>Permanent</option>
                            <option value="probation" {{ $currentStatus == 'probation' ? 'selected' : '' }}>Probation</option>
                        </select>
                        @if ($errors->has('employment_status'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('employment_status') }}</strong>
                            </span>
                        @endif
                    </div>

                    {{-- added password field for update user --}}
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" id="password" name="password" placeholder="Password" aria-describedby="passwordHelp">
                        <div id="passwordHelp" class="form-text"></div>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>

                
                    {{-- @if(Route::currentRouteName() === 'addUser')
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" id="password" name="password" placeholder="Password" aria-describedby="passwordHelp">
                        <div id="passwordHelp" class="form-text"></div>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                    @endif --}}
                
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
