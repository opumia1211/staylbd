<?php

use Illuminate\Support\Facades\Route;

Route::get('report/revenue-profit', [\App\Modules\RevenueProfitReport\Http\Controllers\RevenueProfitReportController::class, 'index'])
    ->name('report.revenue_profit');
