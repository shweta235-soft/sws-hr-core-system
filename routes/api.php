<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\PayrollController;

/*
|--------------------------------------------------------------------------
| 🟢 PUBLIC ROUTES (बिना टोकन के बाहर मेन स्क्रीन से चलेंगे)
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); 

/*
|--------------------------------------------------------------------------
| 🔒 PROTECTED ROUTES (इनके लिए लॉगिन टोकन ज़रूरी है)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) { return $request->user(); });
    
    // Standard Employee Routes
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::post('/attendance/log-break', [AttendanceController::class, 'logBreak']);
    Route::post('/tasks', [TaskController::class, 'createTask']);
    Route::post('/tasks/{id}/sync-timer', [TaskController::class, 'syncTimer']);
    Route::post('/tasks/{id}/complete', [TaskController::class, 'completeTask']);
    Route::post('/leaves', [PayrollController::class, 'applyLeave']);


      // Advanced Interval Clocking Channels
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart']);
    Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::get('/attendance/history', [AttendanceController::class, 'getHistory']);

    // Admin & HR Restricted Zone (Role Based Access Control)
    Route::middleware('role:Admin,HR')->group(function () {
        Route::post('/admin/employee', [PayrollController::class, 'createEmployee']);
        Route::post('/leaves/{id}/approve', [PayrollController::class, 'approveLeave']);
        Route::post('/payroll/generate', [PayrollController::class, 'generatePayroll']);
    });

    // Accounts & Admin Operations
    Route::middleware('role:Admin,Accounts')->group(function () {
        Route::post('/payroll/{id}/pay', [PayrollController::class, 'paySalary']);
    });

      

});
