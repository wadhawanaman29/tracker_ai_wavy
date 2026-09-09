<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />


    <meta name="description" content="" />

    <!-- Favicon -->
    {{-- <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
     --}}

    <link rel="shortcut icon" type="image/x-icon"
        href="https://wavyinformatics.com/wp-content/uploads/2022/01/logo-10m.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/main.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css?v=<?php echo time(); ?>" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <img src="{{ asset('../images/logo-removebg-preview.png') }}" alt="..."
                        class="img-circle profile_img" style="width:150px;">
                </div>

                <div class="menu-inner-shadow"></div>

                @if (Auth::user()->user_type == '0')
                    <ul class="menu-inner py-1">
                        <!-- Dashboard -->
                        <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                                <div data-i18n="Analytics">Dashboard</div>
                            </a>
                        </li>

                        <!-- Layouts -->
                        <li class="menu-item {{ request()->is('users', 'addUser') ? 'active' : '' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons bx bx-user"></i>
                                <div data-i18n="Layouts">Users</div>
                            </a>


                            <ul class="menu-sub">
                                <li class="menu-item {{ request()->is('users') ? 'active' : '' }}">
                                    <a href="{{ route('users') }}" class="menu-link">
                                        <div data-i18n="Without menu">User List</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('addUser') ? 'active' : '' }}">
                                    <a href="{{ route('addUser') }}" class="menu-link">
                                        <div data-i18n="Without menu">Add User</div>
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <li class="menu-item {{ request()->is('project', 'addProject') ? 'active' : '' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons bx bx-chart"></i>
                                <div data-i18n="Layouts">Project</div>
                            </a>

                            <ul class="menu-sub">
                                <li class="menu-item {{ request()->is('project') ? 'active' : '' }}">
                                    <a href="{{ route('project') }}" class="menu-link">
                                        <div data-i18n="Without menu">Project List</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('addProject') ? 'active' : '' }}">
                                    <a href="{{ route('addProject') }}" class="menu-link">
                                        <div data-i18n="Without menu">Add Project</div>
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <li
                            class="menu-item {{ request()->is('assigned_task', 'assigned_task_list', 'assigned_task/*', 'progress_report') ? 'active' : '' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons bx bx-task"></i>
                                <div data-i18n="Layouts">Task Management</div>
                            </a>

                            <ul class="menu-sub">
                                <li class="menu-item {{ request()->is('assigned_task_list') ? 'active' : '' }}">
                                    <a href="{{ route('assigned_task_list') }}" class="menu-link">
                                        <div data-i18n="Without menu">Task Board</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('assigned_task') ? 'active' : '' }}">
                                    <a href="{{ route('assigned_task') }}" class="menu-link">
                                        <div data-i18n="Without menu">New Task</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('progress_report') ? 'active' : '' }}">
                                    <a href="{{ route('progress_report') }}" class="menu-link">
                                        <div data-i18n="Without menu">Delay Report</div>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- <li class="menu-item {{ request()->is('assignment_list') ? 'active' : '' }}">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Report</div>
              </a>

              <ul class="menu-sub">
                <li class="menu-item {{ request()->is('view_report') ? 'active' : '' }}">
                  <a href="{{ route('view_report') }}" class="menu-link">
                    <div data-i18n="Without menu">Hour Report</div>
                  </a>
                </li>
              </ul>

              <ul class="menu-sub">
                <li class="menu-item ">
                  <a href="{{ route('view_user_report') }}" class="menu-link">
                    <div data-i18n="Without menu">View User Report</div>
                  </a>
                </li>
              </ul>

              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="{{ route('view_project_report') }}" class="menu-link">
                    <div data-i18n="Without menu">View Project Report</div>
                  </a>
                </li>
              </ul>

            </li> --}}


                        <li
                            class="menu-item {{ request()->is('today_attendence_report', 'daily_attendence_report') ? 'active' : '' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons bx bx-time"></i>
                                <div data-i18n="Layouts">Clock In Time</div>
                            </a>

                            <ul class="menu-sub">
                                <li class="menu-item {{ request()->is('today_attendence_report') ? 'active' : '' }}">
                                    <a href="{{ route('today_attendence_report') }}" class="menu-link">
                                        <div data-i18n="Without menu">Clock In Report</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('daily_attendence_report') ? 'active' : '' }}">
                                    <a href="{{ route('daily_attendence_report') }}" class="menu-link">
                                        <div data-i18n="Without menu">Daily&nbsp;Attendence&nbsp;Report</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('attendence_graph_report') ? 'active' : '' }}">
                                    <a href="{{ route('attendence_graph_report') }}" class="menu-link">
                                        <div data-i18n="Without menu">Attendence Report</div>
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <li class="menu-item {{ request()->is('mark-as-read', 'notification') ? 'active' : '' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class='menu-icon bx bx-bell'></i>
                                <div data-i18n="Layouts">Notification</div>
                            </a>

                            <ul class="menu-sub">
                                <li class="menu-item {{ request()->is('mark-as-read') ? 'active' : '' }}">
                                    <a href="{{ route('mark-as-read') }}" class="menu-link">
                                        <div data-i18n="Without menu">Add Notification</div>
                                    </a>
                                </li>
                            </ul>

                            <ul class="menu-sub">
                                <li class="menu-item {{ request()->is('notification') ? 'active' : '' }}">
                                    <a href="{{ route('notification') }}" class="menu-link">
                                        <div data-i18n="Without menu">View Notification</div>
                                    </a>
                                </li>
                            </ul>

                        </li>

                        <li class="menu-item {{ request()->is('add-holiday', 'view-holiday') ? 'active' : '' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class='menu-icon bx bx-calendar'></i>
                                <div data-i18n="Layouts">Holiday</div>
                            </a>

                            <ul class="menu-sub">
                                <li class="menu-item {{ request()->is('add-holiday') ? 'active' : '' }}">
                                    <a href="{{ route('add-holiday') }}" class="menu-link">
                                        <div data-i18n="Without menu">Add Holiday</div>
                                    </a>
                                </li>
                            </ul>

                            <ul class="menu-sub">
                                <li class="menu-item {{ request()->is('view-holiday') ? 'active' : '' }}">
                                    <a href="{{ route('view-holiday') }}" class="menu-link">
                                        <div data-i18n="Without menu">Holiday List</div>
                                    </a>
                                </li>
                            </ul>

                        </li>
                        <li class="menu-item {{ request()->is('add_email') ? 'active' : '' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon bx bx-envelope"></i>
                                <div data-i18n="Layouts">General Setting</div>
                            </a>

                            <ul class="menu-sub">
                                <li class="menu-item {{ request()->is('add_email') ? 'active' : '' }}">
                                    <a href="{{ route('add_email') }}" class="menu-link">
                                        <div data-i18n="Without menu">Email Setting</div>
                                    </a>
                                </li>
                            </ul>
                              <ul class="menu-sub">
                                <li class="menu-item {{ request()->is('attendance.settings') ? 'active' : '' }}">
                                    <a href="{{ route('attendance.settings') }}" class="menu-link">
                                        <div data-i18n="Without menu">AttendanceSetting</div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                          <li class="menu-item {{ request()->is('add-leave', 'view-leave') ? 'active open' : '' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class='menu-icon bx bx-calendar'></i>
                                <div>Relaxation Leave</div>
                            </a>

                            <ul class="menu-sub">

                                <li class="menu-item {{ request()->is('add-leave') ? 'active' : '' }}">
                                    <a href="{{ route('add-leave') }}" class="menu-link">
                                        <div>Add Relaxation</div>
                                    </a>
                                </li>

                                {{-- <li class="menu-item {{ request()->is('view-leave') ? 'active' : '' }}">
                                            <a href="{{ route('view-leave') }}" class="menu-link">
                                                <div>Leave List</div>
                                            </a>
                                </li> --}}

                                <li class="menu-item {{ request()->is('view-leave') ? 'active' : '' }}">
                                    <a href="{{ route('view-leave') }}" class="menu-link">
                                        <div>Relaxation List</div>
                                    </a>
                                </li>

                            </ul>
                        </li>




                        <li class="menu-item {{ request()->is('leave-requests') ? 'active' : '' }}">
                            <a href="{{ route('leave-requests') }}" class="menu-link">
                                <i class='menu-icon tf-icons bx bx-calendar-check'></i>
                                <div data-i18n="Analytics">Users Leaves</div>
                            </a>
                        </li>

                        <li class="menu-item {{ request()->is('list_report') ? 'active' : '' }}">
                            <a href="{{ route('list_report') }}" class="menu-link">
                                <i class='menu-icon tf-icons bx bx-chart'></i>
                                <div data-i18n="Analytics">List Report</div>
                            </a>
                        </li>



                        {{-- <li class="menu-item {{ request()->is('assignment_list') ? 'active' : '' }}">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Assignment</div>
              </a>

              <ul class="menu-sub">
                <li class="menu-item {{ request()->is('assignment_list') ? 'active' : '' }}">
                  <a href="{{ route('assignment_list') }}" class="menu-link">
                    <div data-i18n="Without menu">Assignment List</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->is('new_assignment') ? 'active' : '' }}">
                  <a href="{{ route('new_assignment') }}" class="menu-link">
                    <div data-i18n="Without menu">New Assignment</div>
                  </a>
                </li>
                
              </ul>
            </li> --}}

                    </ul>
                @elseif (Auth::user()->user_type == '1')
                    <ul class="menu-inner py-1">
                        <!-- Dashboard -->
                        <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                                <div data-i18n="Analytics">Dashboard</div>
                            </a>
                        </li>

                        <!-- Layouts -->




                        <li class="menu-item {{ request()->is('assignment_list', 'new_assignment') ? 'active' : '' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons bx bx-layout"></i>
                                <div data-i18n="Layouts">Add Daily Status</div>
                            </a>

                            <ul class="menu-sub">
                                <li class="menu-item {{ request()->is('assignment_list') ? 'active' : '' }}">
                                    <a href="{{ route('assignment_list') }}" class="menu-link">
                                        <div data-i18n="Without menu">Report List</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('new_assignment') ? 'active' : '' }}">
                                    <a href="{{ route('new_assignment') }}" class="menu-link">
                                        <div data-i18n="Without menu">Add Report</div>
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <li class="menu-item {{ request()->is('assigned_task_list', 'assigned_task/*') ? 'active' : '' }}">
                            <a href="{{ route('assigned_task_list') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-task"></i>
                                <div data-i18n="Layouts">My Tasks</div>
                            </a>
                        </li>

                        <li
                            class="menu-item {{ request()->is('self_attendence', 'attendence_report') ? 'active' : '' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons bx bx-time"></i>
                                <div data-i18n="Layouts">Clock In Time</div>
                            </a>

                            <ul class="menu-sub">
                                <li class="menu-item {{ request()->is('attendence_report') ? 'active' : '' }}">
                                    <a href="{{ route('attendence_report') }}" class="menu-link">
                                        <div data-i18n="Without menu">Clock In Report</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('self_attendence') ? 'active' : '' }}">
                                    <a href="{{ route('self_attendence') }}" class="menu-link">
                                        <div data-i18n="Without menu">Add Clock In</div>
                                    </a>
                                </li>
                                 <li class="menu-item {{ request()->is('attendence_graph_report') ? 'active' : '' }}">
                                    <a href="{{ route('attendence_graph_report') }}" class="menu-link">
                                        <div data-i18n="Without menu">Attendence Report</div>
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <li
                            class="menu-item {{ request()->is('leave-requests/create', 'leave-requests') ? 'active' : '' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons bx bx-calendar-check"></i>
                                <div data-i18n="Layouts">Leave</div>
                            </a>
                            <ul class="menu-sub">
                                <li class="menu-item {{ request()->is('leave-requests/create') ? 'active' : '' }}">
                                    <a href="{{ route('leave-requests-create') }}" class="menu-link">
                                        <div data-i18n="Without menu">Add Leave</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('leave-requests') ? 'active' : '' }}">
                                    <a href="{{ route('leave-requests') }}" class="menu-link">
                                        <div data-i18n="Without menu">View My Leave</div>
                                    </a>
                                </li>
                            </ul>
                        </li>




                    </ul>
                @endif



                {{-- <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Pages</span>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Account Settings">Account Settings</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="pages-account-settings-account.html" class="menu-link">
                    <div data-i18n="Account">Account</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="pages-account-settings-notifications.html" class="menu-link">
                    <div data-i18n="Notifications">Notifications</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="pages-account-settings-connections.html" class="menu-link">
                    <div data-i18n="Connections">Connections</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-lock-open-alt"></i>
                <div data-i18n="Authentications">Authentications</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="auth-login-basic.html" class="menu-link" target="_blank">
                    <div data-i18n="Basic">Login</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="auth-register-basic.html" class="menu-link" target="_blank">
                    <div data-i18n="Basic">Register</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="auth-forgot-password-basic.html" class="menu-link" target="_blank">
                    <div data-i18n="Basic">Forgot Password</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-cube-alt"></i>
                <div data-i18n="Misc">Misc</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="pages-misc-error.html" class="menu-link">
                    <div data-i18n="Error">Error</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="pages-misc-under-maintenance.html" class="menu-link">
                    <div data-i18n="Under Maintenance">Under Maintenance</div>
                  </a>
                </li>
              </ul>
            </li> --}}
                <!-- Components -->
                {{-- <li class="menu-header small text-uppercase"><span class="menu-header-text">Components</span></li>
            <!-- Cards -->
            <li class="menu-item">
              <a href="cards-basic.html" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Cards</div>
              </a>
            </li>
            <!-- User interface -->
            <li class="menu-item">
              <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-box"></i>
                <div data-i18n="User interface">User interface</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="ui-accordion.html" class="menu-link">
                    <div data-i18n="Accordion">Accordion</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-alerts.html" class="menu-link">
                    <div data-i18n="Alerts">Alerts</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-badges.html" class="menu-link">
                    <div data-i18n="Badges">Badges</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-buttons.html" class="menu-link">
                    <div data-i18n="Buttons">Buttons</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-carousel.html" class="menu-link">
                    <div data-i18n="Carousel">Carousel</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-collapse.html" class="menu-link">
                    <div data-i18n="Collapse">Collapse</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-dropdowns.html" class="menu-link">
                    <div data-i18n="Dropdowns">Dropdowns</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-footer.html" class="menu-link">
                    <div data-i18n="Footer">Footer</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-list-groups.html" class="menu-link">
                    <div data-i18n="List Groups">List groups</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-modals.html" class="menu-link">
                    <div data-i18n="Modals">Modals</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-navbar.html" class="menu-link">
                    <div data-i18n="Navbar">Navbar</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-offcanvas.html" class="menu-link">
                    <div data-i18n="Offcanvas">Offcanvas</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-pagination-breadcrumbs.html" class="menu-link">
                    <div data-i18n="Pagination &amp; Breadcrumbs">Pagination &amp; Breadcrumbs</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-progress.html" class="menu-link">
                    <div data-i18n="Progress">Progress</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-spinners.html" class="menu-link">
                    <div data-i18n="Spinners">Spinners</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-tabs-pills.html" class="menu-link">
                    <div data-i18n="Tabs &amp; Pills">Tabs &amp; Pills</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-toasts.html" class="menu-link">
                    <div data-i18n="Toasts">Toasts</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-tooltips-popovers.html" class="menu-link">
                    <div data-i18n="Tooltips & Popovers">Tooltips &amp; popovers</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ui-typography.html" class="menu-link">
                    <div data-i18n="Typography">Typography</div>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Extended components -->
            <li class="menu-item">
              <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-copy"></i>
                <div data-i18n="Extended UI">Extended UI</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="extended-ui-perfect-scrollbar.html" class="menu-link">
                    <div data-i18n="Perfect Scrollbar">Perfect scrollbar</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="extended-ui-text-divider.html" class="menu-link">
                    <div data-i18n="Text Divider">Text Divider</div>
                  </a>
                </li>
              </ul>
            </li>

            <li class="menu-item">
              <a href="icons-boxicons.html" class="menu-link">
                <i class="menu-icon tf-icons bx bx-crown"></i>
                <div data-i18n="Boxicons">Boxicons</div>
              </a>
            </li>

            <!-- Forms & Tables -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Forms &amp; Tables</span></li>
            <!-- Forms -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-detail"></i>
                <div data-i18n="Form Elements">Form Elements</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="forms-basic-inputs.html" class="menu-link">
                    <div data-i18n="Basic Inputs">Basic Inputs</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="forms-input-groups.html" class="menu-link">
                    <div data-i18n="Input groups">Input groups</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-detail"></i>
                <div data-i18n="Form Layouts">Form Layouts</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="form-layouts-vertical.html" class="menu-link">
                    <div data-i18n="Vertical Form">Vertical Form</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="form-layouts-horizontal.html" class="menu-link">
                    <div data-i18n="Horizontal Form">Horizontal Form</div>
                  </a>
                </li>
              </ul>
            </li>
            <!-- Tables -->
            <li class="menu-item">
              <a href="tables-basic.html" class="menu-link">
                <i class="menu-icon tf-icons bx bx-table"></i>
                <div data-i18n="Tables">Tables</div>
              </a>
            </li>
            <!-- Misc -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Misc</span></li>
            <li class="menu-item">
              <a
                href="https://github.com/themeselection/sneat-html-admin-template-free/issues"
                target="_blank"
                class="menu-link"
              >
                <i class="menu-icon tf-icons bx bx-support"></i>
                <div data-i18n="Support">Support</div>
              </a>
            </li>
            <li class="menu-item">
              <a
                href="https://themeselection.com/demo/sneat-bootstrap-html-admin-template/documentation/"
                target="_blank"
                class="menu-link"
              >
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Documentation">Documentation</div>
              </a>
            </li></ul> --}}

            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            {{-- <div class="nav-item d-flex align-items-center">
                  <i class="bx bx-search fs-4 lh-0"></i>
                  <input
                    type="text"
                    class="form-control border-0 shadow-none"
                    placeholder="Search..."
                    aria-label="Search..."
                  />
                </div> --}}
                            <div class="align-items-center">
                                @if (Auth::check())
                                    {{ 'Welcome ' . Auth::user()->name }}
                                @endif
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Place this tag where you want the button to render. -->
                            {{-- <li class="nav-item lh-1 me-3">
                  <a
                    class="github-button"
                    href="https://github.com/themeselection/sneat-html-admin-template-free"
                    data-icon="octicon-star"
                    data-size="large"
                    data-show-count="true"
                    aria-label="Star themeselection/sneat-html-admin-template-free on GitHub"
                    >Star</a
                  >
                </li> --}}

                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="../assets/img/avatars/img.jpg" alt
                                            class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="../assets/img/avatars/img.jpg" alt
                                                            class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block">{{ Auth::user()->name }}</span>
                                                    <small class="text-muted">
                                                        @if (Auth::check())
                                                            {{ Auth::user()->user_type == '0' ? 'Welcome Admin' : Auth::user()->name }}
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">My Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('change_password') }}">
                                            <i class="bx bx-key me-2"></i>
                                            <span class="align-middle">Change Password</span>
                                        </a>
                                    </li>
                                    {{-- <li>
                      <a class="dropdown-item" href="#">
                        <i class="bx bx-cog me-2"></i>
                        <span class="align-middle">Settings</span>
                      </a>
                    </li> --}}
                                    {{-- <li>
                      <a class="dropdown-item" href="#">
                        <span class="d-flex align-items-center align-middle">
                          <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                          <span class="flex-grow-1 align-middle">Billing</span>
                          <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                        </span>
                      </a>
                    </li> --}}
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Log Out</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>

                @if (Auth::user()->user_type == '1')
                    @php
                        $notifications = notification(); // Retrieve notifications
                    @endphp

                    @if ($notifications && $notifications->count() > 0)
                        <br>
                        <div class="marquee-wrapper">
                            <div class="container">
                                <div class="d-flex align-items-center alert-primary py-2">
                                    <marquee behavior="scroll" direction="left">
                                        @foreach ($notifications as $notification)
                                            <!-- Use the variable instead of calling the function again -->
                                            {{ $notification->notification }} &nbsp; &nbsp;&nbsp; &nbsp; | &nbsp;&nbsp;
                                            &nbsp; &nbsp;
                                        @endforeach
                                    </marquee>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif


                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
