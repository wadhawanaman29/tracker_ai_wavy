<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\ProjectController;

use Illuminate\Support\Facades\Password;

use App\Http\Controllers\Web\ForgotPasswordController;
use App\Http\Controllers\Web\AttendanceController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\LeaveRequestController;


Route::controller(UserController::class)->group(function() {
    Route::get('/', 'login')->name('login');
    Route::post('/doLogin', 'doLogin');
   
    //Route::get('admin', 'admin')->name('admin');
});

  
Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post'); 
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');


// Route::get('/forgot-password', function () {
//     return view('forgetPassword');
// })->middleware('guest')->name('password.request');

// Route::post('/forgot-password', 'App\Http\Controllers\Web\ForgotPasswordController@sendResetLink')
//     ->middleware('guest')
//     ->name('password.email');

// Route::get('/reset-password/{token}', 'App\Http\Controllers\Web\ForgotPasswordController@showResetForm')
//     ->middleware('guest')
//     ->name('password.reset');

// Route::post('/reset-password', 'App\Http\Controllers\Web\ForgotPasswordController@resetPassword')
//     ->middleware('guest')
//     ->name('password.update');


// Route::group(['middleware' => ['auth', 'role:admin']], function () {
//     // Admin routes here
//     Route::get('admin/dashboard', 'AdminController@dashboard')->name('admin.dashboard');
//     // Add more admin routes as needed
// });


// Route::group(['middleware' => ['auth', 'role:user']], function () {
//     // User routes here
//     Route::get('user/dashboard', 'UserController@dashboard')->name('user.dashboard');
//     // Add more user routes as needed
// });




Route::group(['middleware'=>'DisbleBackBtn'], function () {
    Route::group(['middleware'=>'AuthLogin'], function () {

        Route::group(['middleware' => ['auth', 'userType:0']], function () {

            Route::controller(UserController::class)->group(function() {
                Route::get('dashboard', 'dashboard')->name('dashboard');
                Route::get('users', 'users')->name('users');
                Route::get('addUser', 'addUser')->name('addUser');
                Route::post('storeUser', 'storeUser')->name('storeUser');
                Route::get('/editUser/{id}', 'editUser')->name('editUser');
                Route::post('/updateUser/{id}', 'updateUser')->name('updateUser');
                Route::get('/deleteUser/{id}', 'deleteUser')->name('deleteUser');
                Route::get('logout', 'logout')->name('logout');                        
                Route::get('change_password', 'changePassword')->name('change_password');
                Route::post('storeChangePassword', 'storeChangePassword')->name('storeChangePassword');
                Route::get('add_email', 'addEmail')->name('add_email');
                Route::post('store-email','store')->name('store-email');
            });

            Route::controller(ProjectController::class)->group(function() {
                Route::get('project', 'project')->name('project');
                Route::get('addProject', 'addProject')->name('addProject');
                Route::post('storeProject', 'storeProject')->name('storeProject');
                Route::get('/editProject/{id}', 'editProject')->name('editProject');
                Route::post('/updateProject{id}', 'updateProject')->name('updateProject');
                Route::get('/deleteProject/{id}', 'deleteProject')->name('deleteProject');

                Route::get('assignment_list', 'assignmentList')->name('assignment_list');
                Route::get('new_assignment', 'addAssignment')->name('new_assignment');
                Route::post('storeAssignment', 'storeAssignment')->name('storeAssignment');
                Route::get('/editAssignment/{id}', 'editAssignment')->name('editAssignment');
                Route::post('/updateAssignment{id}', 'updateAssignment')->name('updateAssignment');
                Route::post('/deleteAssignment/{id}', 'deleteAssignment')->name('deleteAssignment');

                Route::get('view_report', 'reportData')->name('view_report');
                Route::get('view_user_report', 'view_user_report')->name('view_user_report');
                Route::get('view_project_report', 'view_project_report')->name('view_project_report');
                Route::get('viewuser/{id}', 'viewuser')->name('viewuser');
                Route::get('view_project_detail/{id}', 'viewProjectDetails')->name('view_project_detail');

                Route::get('list_report', 'listReport')->name('list_report');
                Route::get('report', 'generate')->name('generate');

                Route::get('view_employee_full_report/{id}', 'viewEmployeeFullReport')->name('view_employee_full_report');

            });

            Route::controller(AttendanceController::class)->group(function() {
                Route::post('/attendance/breaktime',  'breaktime')->name('breaktime');
                Route::get('/today_attendence_report', 'todayAttendanceReport')->name('today_attendence_report');
                Route::get('/daily_attendence_report', 'dailyAttendanceReport')->name('daily_attendence_report');               
                Route::get('edit_user_attendance', 'edit_user_attendance')->name('edit_user_attendance');

            });

            Route::controller(DepositController::class)->group(function() {
                Route::post('/deposit', 'deposit')->name('deposit');
                Route::get('/mark-as-read', 'markAsRead')->name('mark-as-read');
                Route::get('/notification', 'notification')->name('notification');
                Route::get('/deletenotification/{id}', 'deletenotification')->name('deletenotification');

            });

            // Route for adding holiday
            Route::get('/add-holiday', [HolidayController::class, 'create'])->name('add-holiday');
            Route::post('/add-holiday', [HolidayController::class, 'store'])->name('store-holiday');
            // Route for viewing holidays
            Route::get('/view-holiday', [HolidayController::class, 'index'])->name('view-holiday');
            Route::get('/deleteholiday/{id}', [HolidayController::class, 'deleteholiday'])->name('deleteholiday');


            // Route::post('/deposit', [DepositController::class,'deposit'])->name('deposit');
            // Route::get('/mark-as-read', [DepositController::class,'markAsRead'])->name('mark-as-read');
         

            Route::get('leave-requests', [LeaveRequestController::class,'index'])->name('leave-requests');
            Route::get('leave-requests/create', [LeaveRequestController::class,'create'])->name('leave-requests-create');
            Route::post('leave-requests', [LeaveRequestController::class,'store'])->name('leave-requests-add');
            Route::put('leave-requests/{id}/approve', [LeaveRequestController::class,'approve'])->name('leave-requests.approve');
            Route::put('leave-requests/{id}/reject', [LeaveRequestController::class,'reject'])->name('leave-requests.reject');

        });
   

            Route::controller(UserController::class)->group(function() {
                Route::get('dashboard', 'dashboard')->name('dashboard');    
                Route::get('logout', 'logout')->name('logout');                        
            });

            Route::controller(AttendanceController::class)->group(function() {
                // Route::get('user/self_attendence', 'addSelfAttendance')->name('self_attendence');
                
                Route::post('storeAttendance', 'storeAttendance')->name('storeAttendance');

                Route::post('/attendance/office',  'office')->name('officeAttendance');
                Route::post('/attendance/lunch',  'lunch')->name('lunchAttendance');
                Route::post('/attendance/breaktime',  'breaktime')->name('breaktime');
                Route::get('/self_attendence', 'showAttendanceForm')->name('self_attendence');
                Route::get('/attendence_report', 'attendenceReport')->name('attendence_report');
            });

            Route::controller(ProjectController::class)->group(function() {
        
                Route::get('assignment_list', 'assignmentList')->name('assignment_list');
                Route::get('new_assignment', 'addAssignment')->name('new_assignment');
                Route::post('storeAssignment', 'storeAssignment')->name('storeAssignment');
                Route::get('/editAssignment/{id}', 'editAssignment')->name('editAssignment');
                Route::post('/updateAssignment{id}', 'updateAssignment')->name('updateAssignment');
                Route::post('/deleteAssignment/{id}', 'deleteAssignment')->name('deleteAssignment');

            });
        
            Route::get('/view-holiday', [HolidayController::class, 'index'])->name('view-holiday');
            Route::get('/deleteholiday/{id}', [HolidayController::class, 'deleteholiday'])->name('deleteholiday');

            Route::post('/deposit', [DepositController::class,'deposit'])->name('deposit');

            Route::get('leave-requests', [LeaveRequestController::class,'index'])->name('leave-requests');
            Route::get('leave-requests/create', [LeaveRequestController::class,'create'])->name('leave-requests-create');
            Route::post('leave-requests', [LeaveRequestController::class,'store'])->name('leave-requests-add');
            Route::put('leave-requests/{id}/approve', [LeaveRequestController::class,'approve'])->name('leave-requests.approve');
            Route::put('leave-requests/{id}/reject', [LeaveRequestController::class,'reject'])->name('leave-requests.reject');

    });
});