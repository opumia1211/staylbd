<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MaintenanceRunFullCommand extends Command
{
    protected $signature = 'maintenance:run-full
                            {--skip-uploads : Skip unused uploads cleanup}
                            {--skip-media : Skip media optimization}
                            {--skip-db : Skip database optimize}';

    protected $description = 'Run full maintenance: temp/cache → logs → (optional) DB optimize. Safe for high-traffic.';

    public function handle(): int
    {
        $this->info('=== Full maintenance started ===');

        // 1. Temp & cache (always – safe for concurrency)
        $this->info('Step 1/3: Temp & cache cleanup');
        Artisan::call('maintenance:clean-temp-cache');
        $this->line(Artisan::output());

        // 2. Logs
        $this->info('Step 2/3: Log cleanup');
        Artisan::call('maintenance:clean-logs', ['--keep-days' => 7]);
        $this->line(Artisan::output());

        // 3. Database optimize (optional, run during low traffic)
        if (!$this->option('skip-db') && $this->confirm('Run database optimization? (OPTIMIZE TABLE – can lock briefly)', false)) {
            $this->info('Step 3/3: Database optimization');
            if (class_exists(\App\Console\Commands\MaintenanceOptimizeDatabaseCommand::class)) {
                Artisan::call('maintenance:optimize-database');
                $this->line(Artisan::output());
            } else {
                $this->warn('  maintenance:optimize-database not registered. Skip.');
            }
        } else {
            $this->line('Step 3/3: Database optimization skipped.');
        }

        $this->info('=== Full maintenance completed ===');
        return 0;
    }
}
