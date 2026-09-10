<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\ProjectController;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Web\ForgotPasswordController;
use App\Http\Controllers\Web\AttendanceController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\Web\AssignedTaskController;
use App\Http\Controllers\web\CompanyLeaveController;

Route::controller(UserController::class)->group(function () {
    Route::get('/', 'login')->name('login');
    Route::post('/doLogin', 'doLogin');
});

Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');

Route::group(['middleware' => 'DisbleBackBtn'], function () {
    Route::group(['middleware' => 'AuthLogin'], function () {

        Route::get('dashboard', [UserController::class, 'dashboard'])->name('dashboard');
        Route::post('dashboarddata', [UserController::class, 'dashboarddata'])->name('dashboarddata');
        Route::get('users', [UserController::class, 'users'])->name('users')->middleware('CheckRole:0');
        Route::get('addUser', [UserController::class, 'addUser'])->name('addUser')->middleware('CheckRole:0');
        Route::post('storeUser', [UserController::class, 'storeUser'])->name('storeUser')->middleware('CheckRole:0');
        Route::get('/editUser/{id}', [UserController::class, 'editUser'])->name('editUser')->middleware('CheckRole:0');
        Route::post('/updateUser/{id}', [UserController::class, 'updateUser'])->name('updateUser')->middleware('CheckRole:0');
        Route::get('/deleteUser/{id}', [UserController::class, 'deleteUser'])->name('deleteUser')->middleware('CheckRole:0');
        Route::get('logout', [UserController::class, 'logout'])->name('logout');
        Route::get('change_password', [UserController::class, 'changePassword'])->name('change_password');
        Route::post('storeChangePassword', [UserController::class, 'storeChangePassword'])->name('storeChangePassword');
        Route::get('add_email', [UserController::class, 'addEmail'])->name('add_email')->middleware('CheckRole:0');
        Route::post('store-email', [UserController::class, 'store'])->name('store-email')->middleware('CheckRole:0');

        Route::get('project', [ProjectController::class, 'project'])->name('project')->middleware('CheckRole:0');;
        Route::get('addProject', [ProjectController::class, 'addProject'])->name('addProject')->middleware('CheckRole:0');
        Route::post('storeProject', [ProjectController::class, 'storeProject'])->name('storeProject')->middleware('CheckRole:0');
        Route::get('/editProject/{id}', [ProjectController::class, 'editProject'])->name('editProject')->middleware('CheckRole:0');
        Route::post('/updateProject{id}', [ProjectController::class, 'updateProject'])->name('updateProject')->middleware('CheckRole:0');
        Route::get('/deleteProject/{id}', [ProjectController::class, 'deleteProject'])->name('deleteProject')->middleware('CheckRole:0');

        Route::get('assignment_list', [ProjectController::class, 'assignmentList'])->name('assignment_list')->middleware('CheckRole:1');
        Route::get('new_assignment', [ProjectController::class, 'addAssignment'])->name('new_assignment')->middleware('CheckRole:1');
        Route::post('storeAssignment', [ProjectController::class, 'storeAssignment'])->name('storeAssignment')->middleware('CheckRole:1');
        Route::get('/editAssignment/{id}', [ProjectController::class, 'editAssignment'])->name('editAssignment')->middleware('CheckRole:1');
        Route::post('/updateAssignment{id}', [ProjectController::class, 'updateAssignment'])->name('updateAssignment')->middleware('CheckRole:1');
        Route::post('/deleteAssignment/{id}', [ProjectController::class, 'deleteAssignment'])->name('deleteAssignment')->middleware('CheckRole:1');
        Route::get('/get-project-tasks', [ProjectController::class, 'getProjectTasks']);
        Route::get('/get-project-tasks', [ProjectController::class, 'getProjectTasks']);
        Route::get('view_report', [ProjectController::class, 'reportData'])->name('view_report')->middleware('CheckRole:0');
        Route::get('view_user_report', [ProjectController::class, 'view_user_report'])->name('view_user_report')->middleware('CheckRole:0');
        Route::get('view_project_report', [ProjectController::class, 'view_project_report'])->name('view_project_report')->middleware('CheckRole:0');
        Route::get('viewuser/{id}', [ProjectController::class, 'viewuser'])->name('viewuser')->middleware('CheckRole:0');
        Route::get('view_project_detail/{id}', [ProjectController::class, 'viewProjectDetails'])->name('view_project_detail');
        Route::get('view_employee_full_report/{id}', [ProjectController::class, 'viewEmployeeFullReport'])->name('view_employee_full_report')->middleware('CheckRole:0');
        Route::get('list_report', [ProjectController::class, 'listReport'])->name('list_report')->middleware('CheckRole:0');
        Route::get('report', [ProjectController::class, 'generate'])->name('generate')->middleware('CheckRole:0');
        Route::get('/report/{user_id}', [ProjectController::class, 'report'])->name('report')->middleware('CheckRole:0');
        Route::post('/employee', [ProjectController::class, 'employee'])->name('employee');
        Route::post('/employeedata', [ProjectController::class, 'employeedata'])->name('employeedata');

        Route::post('storeAttendance', [AttendanceController::class, 'storeAttendance'])->name('storeAttendance');
        Route::post('/attendance/office', [AttendanceController::class, 'office'])->name('officeAttendance');
        Route::post('/attendance/lunch', [AttendanceController::class, 'lunch'])->name('lunchAttendance');
        Route::post('/attendance/breaktime', [AttendanceController::class, 'breaktime'])->name('breaktime');
        Route::get('/self_attendence', [AttendanceController::class, 'showAttendanceForm'])->name('self_attendence')->middleware('CheckRole:1');
        Route::get('/attendence_report', [AttendanceController::class, 'attendenceReport'])->name('attendence_report')->middleware('CheckRole:1');
        Route::get('/today_attendence_report', [AttendanceController::class, 'todayAttendanceReport'])->name('today_attendence_report')->middleware('CheckRole:0');
        Route::get('/employee_attendence_report', [AttendanceController::class, 'employee_attendence_report'])->name('employee_attendence_report')->middleware('CheckRole:0');
        Route::get('/daily_attendence_report', [AttendanceController::class, 'dailyAttendanceReport'])->name('daily_attendence_report')->middleware('CheckRole:0');
        // Route::post('edit_user_attendance', [AttendanceController::class, 'edit_user_attendance'])->name('edit_user_attendance')->middleware('CheckRole:0');
        Route::get('/tracker-history/{user}', [AttendanceController::class, 'todayTrackerHistory'])->middleware('CheckRole:0');;
        Route::get('/daily-tracker-history/{userId}', [AttendanceController::class, 'dailyTrackerHistory']);
        Route::get('/attendence_graph_report', [AttendanceController::class, 'attendenceGraphReport'])->name('attendence_graph_report')->middleware('CheckRole:0,1');
        Route::get('/attendance-settings', [AttendanceController::class, 'attendancesSettings'])->name('attendance.settings');
        Route::post('/attendance-settings-update', [AttendanceController::class, 'attendanceSettingsUpdate'])->name('attendance.settings.update');
        Route::post('/edit_user_attendance', [AttendanceController::class, 'edit_user_attendance'])->name('edit_user_attendance');
        // Route::get('/daily-attendance-report', [AttendanceController::class, 'dailyAttendanceReport'])->name('daily_attendance_report');

        Route::get('/add-holiday', [HolidayController::class, 'create'])->name('add-holiday')->middleware('CheckRole:0');
        Route::post('/add-holiday', [HolidayController::class, 'store'])->name('store-holiday')->middleware('CheckRole:0');
        Route::get('/view-holiday', [HolidayController::class, 'index'])->name('view-holiday')->middleware('CheckRole:0');
        Route::get('/deleteholiday/{id}', [HolidayController::class, 'deleteholiday'])->name('deleteholiday')->middleware('CheckRole:0');


        // Route::post('/add-leave', [CompanyLeaveController::class, 'store'])->name('add-leave')->middleware('CheckRole:0');

        Route::get('/add-leave', [CompanyLeaveController::class, 'create'])->name('add-leave')->middleware('CheckRole:0');
        Route::post('/add-leave', [CompanyLeaveController::class, 'store'])->name('add-leave.store')->middleware('CheckRole:0');
        Route::get('/view-leave', [CompanyLeaveController::class, 'index'])->name('view-leave')->middleware('CheckRole:0');
        Route::delete('/destroy/{id}', [CompanyLeaveController::class, 'destroy'])->name('destroy')->middleware('CheckRole:0');
        Route::get('/leave/edit/{id}', [CompanyLeaveController::class, 'edit'])->name('leave.edit');
        Route::put('/leave/update/{id}', [CompanyLeaveController::class, 'update'])->name('leave.update');


        Route::post('/deposit', [DepositController::class, 'deposit'])->name('deposit')->middleware('CheckRole:0');
        Route::get('/mark-as-read', [DepositController::class, 'markAsRead'])->name('mark-as-read')->middleware('CheckRole:0');
        Route::get('/notification', [DepositController::class, 'notification'])->name('notification')->middleware('CheckRole:0');
        Route::get('/deletenotification/{id}', [DepositController::class, 'deletenotification'])->name('deletenotification')->middleware('CheckRole:0');


        Route::get('leave-requests', [LeaveRequestController::class, 'index'])->name('leave-requests');
        Route::get('leave-requests/create', [LeaveRequestController::class, 'create'])->name('leave-requests-create')->middleware('CheckRole:1');
        Route::post('leave-requests', [LeaveRequestController::class, 'store'])->name('leave-requests-add');
        Route::put('leave-requests/{id}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
        Route::put('leave-requests/{id}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
        Route::get('leave-requests/{id}/delete', [LeaveRequestController::class, 'delete'])->name('leave-requests.delete');




        Route::get('assigned_task', [AssignedTaskController::class, 'assignedTask'])->name('assigned_task')->middleware('CheckRole:0');
        Route::get('assigned_task_list', [AssignedTaskController::class, 'assignedTaskList'])->name('assigned_task_list')->middleware('CheckRole:0,1');
        Route::get('assigned_task_view/{id}', [AssignedTaskController::class, 'show'])->name('assigned.view')->middleware('CheckRole:0,1');
        Route::post('assigned_task', [AssignedTaskController::class, 'store'])->name('assigned_task.store')->middleware('CheckRole:0');
        Route::get('assigned_task_edit/{id}', [AssignedTaskController::class, 'edit'])->name('assigned.edit')->middleware('CheckRole:0');
        Route::put('assigned_task/{id}', [AssignedTaskController::class, 'update'])->name('assigned_task.update')->middleware('CheckRole:0');
        Route::get('progress_report', [AssignedTaskController::class, 'progress_report'])->name('progress_report')->middleware('CheckRole:0');
        Route::get('employee_report', [AssignedTaskController::class, 'employeeReport'])->name('employee_report')->middleware('CheckRole:0');
        Route::get('employee_report/{user}', [AssignedTaskController::class, 'employeeReportShow'])->name('employee_report.show')->middleware('CheckRole:0');
        Route::get('my_report', [AssignedTaskController::class, 'myReport'])->name('my_report')->middleware('CheckRole:1');
        Route::put('assigned_task/{id}/status', [AssignedTaskController::class, 'updateStatus'])->name('assigned.status.update')->middleware('CheckRole:0,1');
        Route::delete('assigned_task/delete/{id}', [AssignedTaskController::class, 'destroy'])->name('assigned_task_delete')->middleware('CheckRole:0');
        Route::get('/progress-report/projects', [AssignedTaskController::class, 'progressReportProjectsAjax'])
            ->name('progress_report.projects');
        Route::get('/progress-report/tasks', [AssignedTaskController::class, 'progressReportTasksAjax'])
            ->name('progress_report.tasks');
        Route::get('progress_report/tasks_ajax', [AssignedTaskController::class, 'progressReportTasksAjax'])
            ->name('progress_report.tasks_ajax')
            ->middleware('CheckRole:0');
            Route::get('/my-report/tasks',    [AssignedTaskController::class, 'myReportTasksAjax'])->name('my_report.tasks');
Route::get('/my-report/projects', [AssignedTaskController::class, 'myReportProjectsAjax'])->name('my_report.projects');
    });
});
