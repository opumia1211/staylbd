<?php

namespace App\Modules\RevenueProfitReport;

use Illuminate\Support\ServiceProvider;

class RevenueProfitReportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'modules.RevenueProfitReport');
    }
}
