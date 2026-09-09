<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\HolidayApiController ;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeaveRequestController;


/* ------- User Routes ---------*/

Route::controller(UserController::class)->group(function () {
    Route::post('/login', 'login')->name('user.login');
});

/* ------- Protected Routes (Sanctum) ---------*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [UserController::class, 'logout'])->name('user.logout');
    Route::get('/projects', [ProjectController::class, 'projects']);
    Route::post('/daily-status', [ProjectController::class, 'storeAssignment']);
    Route::get('/assignment-list', [ProjectController::class, 'assignmentList']);
    Route::delete('/assignment/{id}', [ProjectController::class, 'deleteAssignment']);
    Route::get('/assignment/{id}', [ProjectController::class, 'getAssignmentById']);
    Route::put('/assignment-update/{id}', [ProjectController::class, 'updateAssignment']);
    Route::get('/notifications', [NotificationController::class, 'getNotifications']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
    Route::post('/notifications/mark-read/{id}', [NotificationController::class, 'markOneRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
     Route::get('/ping', function () { return response()->json(['success' => true]);});

    Route::get('/holidays', [HolidayApiController::class, 'index']);
    
    Route::get('/leave-requests', [LeaveRequestController::class, 'index']);
    Route::post('/leave-requests-add', [LeaveRequestController::class, 'store']);
    // Route::get('/leave-requests',[LeaveRequestController::class, 'index']);
    Route::delete('/leave-request-delete/{id}', [LeaveRequestController::class, 'delete']);

    Route::controller(AttendanceController::class)->group(function () {

        Route::post('/officeIn', 'officeIn');
        Route::post('/officeOut', 'officeOut');
        Route::post('/lunchIn', 'lunchIn');
        Route::post('/lunchOut', 'lunchOut');
        Route::post('/startTracker', 'startTracker');
        Route::post('/start', 'startTrackerSession');
        Route::post('/stop', 'stopTrackerSession');
        Route::post('/userTrackerStatus', 'userTrackerStatus');
        Route::get('/history', 'getUserHistory');
        Route::get('/test', 'test');
         Route::post('/syncOfflineData', 'syncOfflineData');
        Route::get('/attendance-graph-report', 'attendenceGraphReportApi');
    });
});

/* ------- Test Route ---------*/

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to Tracker API',
    ]);
});
