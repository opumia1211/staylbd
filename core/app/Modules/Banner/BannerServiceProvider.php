<?php

namespace App\Modules\Banner;

use Illuminate\Support\ServiceProvider;

/**
 * Banner Module – স্বাধীন মডিউল; হোমপেজ ব্যানার সেকশন।
 */
class BannerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BannerModuleService::class, function () {
            return new BannerModuleService();
        });
    }

    public function boot(): void
    {
        // Views loaded from Resources/views (namespace: modules.Banner)
    }
}
