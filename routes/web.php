<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Task;
use App\Models\Payroll;
use App\Http\Controllers\PayrollController;

// 🖥️ Clean View Routing: Loading Decoupled Blade Layout
Route::get('/', function () {
    return view('dashboard', [
        'employees' => User::all(),
        'attendances' => Attendance::all(),
        'tasks' => Task::all(),
        'payrolls' => Payroll::all()
    ]);
});

// PDF Downloader Hook Channel
Route::get('/download-payslip/{id}', [PayrollController::class, 'downloadPayslip']);