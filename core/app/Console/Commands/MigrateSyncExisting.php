<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * One-time fix: mark all existing migration files as already run
 * so "php artisan migrate" does not try to re-run them (e.g. when
 * the DB was created from a backup or migrations table was lost).
 */
class MigrateSyncExisting extends Command
{
    protected $signature = 'migrate:sync-existing';

    protected $description = 'Mark all migration files as already run (fix "Table already exists" when running migrate)';

    public function handle()
    {
        if (!Schema::hasTable('migrations')) {
            $this->error('Migrations table does not exist. Run: php artisan migrate:install');
            return 1;
        }

        $migrationsPath = database_path('migrations');
        $files = glob($migrationsPath . '/*.php');
        $existing = DB::table('migrations')->pluck('migration')->flip()->all();
        $batch = (int) DB::table('migrations')->max('batch') + 1;
        $inserted = 0;

        foreach ($files as $file) {
            $name = basename($file, '.php');
            if (!isset($existing[$name])) {
                DB::table('migrations')->insert([
                    'migration' => $name,
                    'batch'     => $batch,
                ]);
                $inserted++;
                $this->line("  Marked as run: {$name}");
            }
        }

        if ($inserted === 0) {
            $this->info('No missing migrations to sync. Your migrations table is already up to date.');
        } else {
            $this->info("Done. Marked {$inserted} migration(s) as already run. You can now use 'php artisan migrate' for new migrations only.");
        }

        return 0;
    }
}
