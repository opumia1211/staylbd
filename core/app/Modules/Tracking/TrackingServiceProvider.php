<?php

namespace App\Modules\Tracking;

use Illuminate\Support\ServiceProvider;

class TrackingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MetaConversionApiService::class, function () {
            return new MetaConversionApiService();
        });
        $this->app->singleton(TrackingScriptService::class, function () {
            return new TrackingScriptService();
        });
    }

    public function boot(): void
    {
        $this->registerViewComposer();
    }

    protected function registerViewComposer(): void
    {
        try {
            \View::composer('partials.tracking_scripts', function ($view) {
                $view->with('tracking', app(TrackingScriptService::class));
            });
        } catch (\Throwable $e) {
            \Log::warning('Tracking view composer failed: ' . $e->getMessage());
        }
    }
}
