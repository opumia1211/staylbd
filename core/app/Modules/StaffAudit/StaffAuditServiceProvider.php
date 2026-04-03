<?php

namespace App\Modules\StaffAudit;

use Illuminate\Support\ServiceProvider;

class StaffAuditServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            if (class_exists(\App\Models\Order::class) && class_exists(\App\Models\AdminActivityLog::class)) {
                \App\Models\Order::observe(OrderAuditObserver::class);
            }
        } catch (\Throwable $e) {
            \Log::warning('StaffAudit: Order observer registration failed: ' . $e->getMessage());
        }
    }
}
