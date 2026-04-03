<?php

namespace App\Console\Commands;

use App\Models\GeneralSetting;
use App\Services\HomepageDataService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class StaylbdCacheClearCommand extends Command
{
    protected $signature = 'staylbd:cache-clear
                            {--browser-only : Only bump asset version (invalidate browser cache), do not run optimize:clear}';

    protected $description = 'Clear server cache and invalidate browser cache (professional e-commerce). Run after deploy or when visitors must get new CSS/JS.';

    public function handle(): int
    {
        if (!$this->option('browser-only')) {
            $this->info('Clearing server cache...');
            try {
                Artisan::call('optimize:clear');
                $this->line('  - Config, route, view, and application cache cleared.');
            } catch (\Throwable $e) {
                $this->error('optimize:clear failed: ' . $e->getMessage());
                return 1;
            }
            HomepageDataService::clearCache();
            $this->line('  - Homepage sections cache cleared.');
        }

        $version = (string) time();
        Cache::put('asset_version', $version);
        $this->line('  - Asset version set to ' . $version . ' (browser cache will be invalidated on next visit).');

        try {
            $general = GeneralSetting::first();
            if ($general) {
                Cache::put('GeneralSetting', $general);
                $this->line('  - GeneralSetting cache warmed.');
            }
        } catch (\Throwable $e) {
            // Non-fatal
        }

        $this->info('Cache clear completed. Old browser cache will be dropped automatically (new ?v= in URLs).');
        return 0;
    }
}
