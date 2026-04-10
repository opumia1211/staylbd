<?php

namespace App\Observers;

use App\Models\Frontend;
use App\Services\HomepageDataService;
use App\Services\ProductCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * When any Frontend CMS row changes, public pages must drop cached sections quickly.
 * Controllers already clear some keys; this is a safety net for every save/delete path.
 */
class FrontendObserver
{
    public function saved(Frontend $frontend): void
    {
        $this->flushPublicCaches();
    }

    public function deleted(Frontend $frontend): void
    {
        $this->flushPublicCaches();
    }

    protected function flushPublicCaches(): void
    {
        Cache::forget('seo.data');
        Cache::forget('seo.sitemap.xml');
        Cache::forget('homepage.banner.guest');
        Cache::forget('homepage.banner.auth');
        HomepageDataService::clearCache();
        ProductCacheService::clearProductListings();
    }
}
