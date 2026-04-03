<?php

namespace App\Modules\FraudGuard;

use Illuminate\Support\ServiceProvider;

class FraudGuardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FraudGuardService::class, function () {
            return new FraudGuardService();
        });
    }

    public function boot(): void
    {
        try {
            $this->app['router']->aliasMiddleware('fraud.guard', FraudGuardMiddleware::class);
        } catch (\Throwable $e) {
            \Log::warning('FraudGuard: middleware registration failed: ' . $e->getMessage());
        }
    }
}
