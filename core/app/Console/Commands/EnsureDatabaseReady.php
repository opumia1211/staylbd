<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnsureDatabaseReady extends Command
{
    protected $signature = 'db:ensure-ready {--migrate : Run migrations first}';
    protected $description = 'Verify database connection and critical tables exist. Run migrations with --migrate.';

    public function handle(): int
    {
        $this->info('Checking database...');

        try {
            DB::connection()->getPdo();
            $this->info('Database connection: OK');
        } catch (\Throwable $e) {
            $this->error('Database connection failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        if ($this->option('migrate')) {
            $this->info('Running migrations...');
            Artisan::call('migrate', ['--force' => true], $this->getOutput());
        }

        $tables = ['users', 'orders', 'general_settings', 'courierapis', 'courier_logs', 'deposits', 'shipping_methods'];
        $missing = [];
        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                $missing[] = $table;
            }
        }

        if (!empty($missing)) {
            $msg = 'Missing tables: ' . implode(', ', $missing);
            if (config('app.env') === 'production') {
                $msg .= '. In production, import the master SQL file (database/staylbd_wintersm.sql) in cPanel. Do not run migrations.';
            } else {
                $msg .= '. Run: php artisan migrate --force';
            }
            $this->warn($msg);
            return self::FAILURE;
        }

        $this->info('Critical tables: OK');
        $this->info('Database is ready.');
        return self::SUCCESS;
    }
}
