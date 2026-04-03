<?php

namespace App\Console\Commands;

use App\Services\ProductCacheService;
use Illuminate\Console\Command;

/**
 * Optimize application for fast admin & user pages.
 * Run: php artisan app:optimize
 * - Runs pending migrations (DB schema up to date)
 * - Clears application cache (fresh data on next request)
 * - Caches config, routes, views (faster page load)
 */
class OptimizeAppCommand extends Command
{
    protected $signature = 'app:optimize {--migrate : Run migrations first} {--no-cache : Skip config/route/view cache} {--clean : Also run project:clean}';
    protected $description = 'Optimize app: migrate, clear cache, cache config/routes/views for fast admin & user pages';

    public function handle(): int
    {
        if ($this->option('migrate')) {
            $this->info('Running migrations...');
            try {
                $this->call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {
                $this->warn('Migration error (tables may already exist): ' . $e->getMessage());
                $this->info('To run only product indexes: php artisan migrate --path=database/migrations/2026_02_18_100000_add_product_performance_indexes.php --force');
            }
        }

        $this->info('Clearing application cache...');
        $this->call('cache:clear');
        ProductCacheService::clearAll();

        if (!$this->option('no-cache')) {
            $this->info('Caching config...');
            $this->call('config:cache');
            $this->info('Caching routes...');
            try {
                $this->call('route:cache');
            } catch (\Throwable $e) {
                $this->warn('Route cache skipped: ' . $e->getMessage());
            }
            $this->info('Caching views...');
            $this->call('view:cache');
            $this->info('Running framework optimize...');
            $this->call('optimize');
        }

        if ($this->option('clean')) {
            $this->info('Running project cleanup...');
            $this->call('project:clean', ['--force' => true]);
        }

        $this->info('Optimization complete. Admin & user pages will load faster.');
        return self::SUCCESS;
    }
}
