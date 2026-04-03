<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixUserLoginCredentials extends Command
{
    protected $signature = 'users:fix-login-credentials
                            {--dry-run : Show what would be updated without changing database}
                            {--show : Show user count and how many have empty username}';
    protected $description = 'Set username from email prefix for users with empty username so they can log in. Run after migrate.';

    public function handle(): int
    {
        $this->info('Checking users table...');

        if (!Schema::hasTable('users')) {
            $this->error('Table "users" does not exist. Run: php artisan migrate --force');
            return self::FAILURE;
        }

        if (!Schema::hasColumn('users', 'username')) {
            $this->error('Column "username" missing on users table. Run: php artisan migrate --force');
            return self::FAILURE;
        }

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('DRY RUN – no changes will be saved.');
        }

        if ($this->option('show')) {
            $total = User::count();
            $empty = User::where(function ($q) {
                $q->whereNull('username')->orWhere('username', '');
            })->count();
            $this->info("Total users: {$total}. Users with empty username: {$empty}");
            if ($total > 0 && $empty > 0) {
                $this->info('Run without --show to fix them.');
            }
            return self::SUCCESS;
        }
        $query = User::where(function ($q) {
            $q->whereNull('username')->orWhere('username', '');
        });

        $count = $query->count();
        if ($count === 0) {
            $this->info('No users with empty username. Nothing to fix.');
            return self::SUCCESS;
        }

        $this->info("Found {$count} user(s) with empty username. Fixing...");

        $fixed = 0;
        $used = [];
        $query->orderBy('id')->chunk(100, function ($users) use (&$fixed, &$used, $dryRun) {
            foreach ($users as $user) {
                $prefix = strtolower((string) trim(explode('@', $user->email ?? '')[0] ?? ''));
                if ($prefix === '') {
                    $prefix = 'user' . $user->id;
                }
                $prefix = preg_replace('/[^a-z0-9_]/', '', $prefix) ?: ('user' . $user->id);
                $base = $prefix;
                $n = 0;
                while (isset($used[$prefix]) || User::where('username', $prefix)->where('id', '!=', $user->id)->exists()) {
                    $n++;
                    $prefix = $base . '_' . $n;
                }
                $used[$prefix] = true;

                if (!$dryRun) {
                    DB::table('users')->where('id', $user->id)->update(['username' => $prefix]);
                }
                $this->line("  id={$user->id} email={$user->email} -> username={$prefix}");
                $fixed++;
            }
        });

        if ($dryRun) {
            $this->info("Would update {$fixed} user(s). Run without --dry-run to apply.");
        } else {
            $this->info("Updated {$fixed} user(s). They can now log in with username or email prefix.");
        }

        return self::SUCCESS;
    }
}
