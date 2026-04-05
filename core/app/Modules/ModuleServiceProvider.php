<?php

namespace App\Modules;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

/**
 * Central provider for E-commerce Tracking & Automation modules.
 * Each module is booted in isolation; failure in one module does not break others.
 */
class ModuleServiceProvider extends ServiceProvider
{
    /** @var array<string> Module directory names under app/Modules */
    protected $modules = [
        'Banner',
        'Tracking',
        'OrderEnhancements',
        'StaffAudit',
        'FraudGuard',
        'CourierTracking',
        'RevenueProfitReport',
        'EmployeePerformanceReport',
    ];

    public function register(): void
    {
        foreach ($this->modules as $name) {
            $providerClass = "App\\Modules\\{$name}\\{$name}ServiceProvider";
            if (class_exists($providerClass)) {
                $this->app->register($providerClass);
            }
        }
    }

    public function boot(): void
    {
        foreach ($this->modules as $name) {
            $this->bootModule($name);
        }
    }

    protected function bootModule(string $name): void
    {
        try {
            $base = app_path("Modules/{$name}");
            if (!is_dir($base)) {
                return;
            }

            $this->loadMigrationsFrom("{$base}/Database/Migrations");
            $viewsPath = "{$base}/Resources/views";
            if (is_dir($viewsPath)) {
                $this->loadViewsFrom($viewsPath, "modules.{$name}");
            }

            $routesFile = "{$base}/Routes/web.php";
            if (file_exists($routesFile)) {
                Route::middleware('web')->group($routesFile);
            }

            $adminRoutes = "{$base}/Routes/admin.php";
            if (file_exists($adminRoutes)) {
                Route::middleware(['web', 'admin'])->prefix(config('admin.prefix', 'admin'))->group($adminRoutes);
            }
        } catch (\Throwable $e) {
            report($e);
            \Log::warning("Module [{$name}] boot failed: " . $e->getMessage());
        }
    }
}
