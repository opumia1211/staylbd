<?php

namespace App\Console\Commands;

use App\Services\HomepageDataService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

/**
 * Professional optimization for Wishlist, Cart, Compare pages.
 * No feature changes: only cache clear, asset version bump, and asset checks
 * so pages load fast and CSS does not break on any device or browser.
 */
class StaylbdOptimizeListPagesCommand extends Command
{
    protected $signature = 'staylbd:optimize-list-pages
                            {--skip-clear : Do not run optimize:clear; only bump asset version and verify assets}
                            {--quiet : Minimal output}';

    protected $description = 'Optimize Wishlist/Cart/Compare: clear caches, bump asset version, verify CSS. Run after CSS/layout changes so all devices and browsers get fast, consistent pages.';

    /** CSS files required for /user/wishlist, /user/cart, /user/compare (dashboard layout). */
    private const LIST_PAGE_CSS = [
        'dashboard',
        'track-order',
        'product-list-screenshot',
        'cart-page',
        'user-pages',
        'dashboard-sidebar',
        'compare',
        'wishlist-responsive',
        'wishlist-buttons',
        'user-list-pages-layout',
        'user-list-pages-common',
    ];

    public function handle(): int
    {
        $quiet = $this->option('quiet');

        if (!$this->option('skip-clear')) {
            if (!$quiet) {
                $this->info('Clearing caches for a clean, fast load…');
            }
            try {
                Artisan::call('optimize:clear');
                if (!$quiet) {
                    $this->line('  - Config, route, view, and application cache cleared.');
                }
            } catch (\Throwable $e) {
                $this->error('optimize:clear failed: ' . $e->getMessage());
                return 1;
            }
            try {
                HomepageDataService::clearCache();
                if (!$quiet) {
                    $this->line('  - Homepage cache cleared.');
                }
            } catch (\Throwable $e) {
                // Non-fatal
            }
        }

        $version = (string) time();
        Cache::put('asset_version', $version);
        if (!$quiet) {
            $this->line('  - Asset version set to ' . $version . ' (browsers will request fresh CSS/JS).');
        }

        $basePath = public_path('assets/templates/basic/css');
        $missing = [];
        foreach (self::LIST_PAGE_CSS as $name) {
            $path = $basePath . DIRECTORY_SEPARATOR . $name . '.css';
            if (!is_file($path) || !is_readable($path)) {
                $missing[] = $name . '.css';
            }
        }
        if ($missing !== []) {
            $this->warn('Missing or unreadable CSS (layout may break on some devices):');
            foreach ($missing as $f) {
                $this->line('  - ' . $f);
            }
        } elseif (!$quiet) {
            $this->line('  - All list-page CSS files present and readable.');
        }

        if (!$quiet) {
            $this->newLine();
            $this->info('List pages (Wishlist, Cart, Compare) are optimized. Use this one-liner when you need it:');
            $this->line('  php artisan staylbd:optimize-list-pages');
            $this->newLine();
            $this->comment('Production tips: enable Gzip, keep Cache-Control (serve-css already sends max-age=2592000), test on multiple viewports and browsers.');
        }

        return 0;
    }
}
