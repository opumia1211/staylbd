<?php

use Illuminate\Support\Facades\Route;

Route::get('report/employee-performance', [\App\Modules\EmployeePerformanceReport\Http\Controllers\EmployeePerformanceReportController::class, 'index'])
    ->name('report.employee_performance');
