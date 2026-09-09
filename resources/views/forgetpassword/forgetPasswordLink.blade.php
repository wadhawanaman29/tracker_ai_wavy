


<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>{{ config('app.name') }} | forget password</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    {{-- <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" /> --}}
    <link rel="shortcut icon" type="image/x-icon" href="https://wavyinformatics.com/wp-content/uploads/2022/01/logo-10m.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="../assets/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
  </head>

  <body>
    <!-- Content -->

    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register -->
          <div class="card">

            {{-- @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
               {{ Session::get('message') }}
           </div> --}}

           
        @if(empty($email) || Session::has('message'))
           @if(empty($email))
               <div class="alert alert-danger" role="alert">
                   You already changed your password.
               </div>
           @endif

           @if(Session::has('message'))
               <div class="alert alert-success" role="alert">
                   {{ Session::get('message') }}
               </div>
           @endif
       @else

            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                
                  
                   
                  {{-- <span class="app-brand-text demo text-body fw-bolder">{{ config('app.name') }}</span> --}}
                  <div class="main-logo text-center"><img src="{{ asset('../images/Wavy_new_logo.png') }}" alt="..."
                    class="img-circle profile_img" style=""></div>
                
              </div>
              <div class="card-header">Reset Password</div>
                  <div class="card-body">
  
                      <form action="{{ route('reset.password.post') }}" method="POST">
                          @csrf
                          <input type="hidden" name="token" value="{{ $token }}">
  
                          <div class="mb-3">
                              <label for="email_address" class="col-form-label text-md-right">E-Mail Address</label>
                             
                                  <input type="text" id="email_address" class="form-control" value="{{ $email }}" name="email" readonly autofocus>
                                  @if ($errors->has('email'))
                                      <span class="text-danger">{{ $errors->first('email') }}</span>
                                  @endif
                             
                          </div>
  
                          <div class="mb-3">
                              <label for="password" class="col-form-label text-md-right">Password</label>
                              
                                  <input type="password" id="password" class="form-control" name="password" required autofocus>
                                  @if ($errors->has('password'))
                                      <span class="text-danger">{{ $errors->first('password') }}</span>
                                  @endif
                              
                          </div>
  
                          <div class="mb-3">
                              <label for="password-confirm" class="col-form-label text-md-right">Confirm Password</label>
                            
                                  <input type="password" id="password-confirm" class="form-control" name="password_confirmation" required autofocus>
                                  @if ($errors->has('password_confirmation'))
                                      <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                                  @endif
                             
                          </div>
  
                          {{-- <div class="col-md-6 offset-md-4 mt-3">
                              <button type="submit" class="btn btn-primary">
                                  Reset Password
                              </button>
                          </div> --}}
                          <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                Reset Password
                            </button>
                          </div>
                      </form>
                        
                  </div>

            </div>
            @endif
          </div>
          <!-- /Register -->
        </div>
      </div>
    </div>

    

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->


  </body>
</html>