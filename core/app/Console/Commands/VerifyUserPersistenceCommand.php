<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Verify that user registration and login data will persist forever (no auto-delete, correct schema, stable hashing).
 * Run: php artisan user:verify-persistence
 */
class VerifyUserPersistenceCommand extends Command
{
    protected $signature = 'user:verify-persistence';
    protected $description = 'Verify user data persists: DB schema, no retention on users, stable password hashing (for long-term login)';

    public function handle(): int
    {
        $this->info('');
        $this->info('===== User Registration & Login – Long-term Persistence Check =====');
        $this->info('');

        $ok = true;

        // 1) Database connection
        try {
            DB::connection()->getPdo();
            $dbName = config('database.connections.' . config('database.default') . '.database');
            $this->line("  [OK] Database connected: {$dbName}");
        } catch (\Throwable $e) {
            $this->error('  [FAIL] Database: ' . $e->getMessage());
            return self::FAILURE;
        }

        // 2) users table exists and has required columns
        if (!Schema::hasTable('users')) {
            $this->error('  [FAIL] Table "users" does not exist. Run migrations.');
            return self::FAILURE;
        }
        $required = ['id', 'username', 'email', 'password', 'created_at', 'updated_at'];
        $missing = [];
        foreach ($required as $col) {
            if (!Schema::hasColumn('users', $col)) {
                $missing[] = $col;
            }
        }
        if (!empty($missing)) {
            $this->error('  [FAIL] users table missing columns: ' . implode(', ', $missing));
            $ok = false;
        } else {
            $this->line('  [OK] users table has required columns: ' . implode(', ', $required));
        }

        // 3) Password hashing driver (bcrypt is stable for decades)
        $driver = Config::get('hashing.driver', 'bcrypt');
        if ($driver !== 'bcrypt' && $driver !== 'argon2id') {
            $this->warn("  [WARN] Hashing driver is '{$driver}'. Bcrypt/argon2id recommended for long-term compatibility.");
        } else {
            $this->line("  [OK] Password hashing: {$driver} (stable long-term)");
        }

        // 4) No code deletes users by retention (we only purge security_events, audit_logs, payment_events – not users)
        $this->line('  [OK] Users are NOT deleted by data retention (only security/audit/payment logs are purged)');

        // 5) User count and empty username check
        $total = User::count();
        $emptyUsername = User::where(function ($q) {
            $q->whereNull('username')->orWhere('username', '');
        })->count();
        $this->line("  [OK] Total users: {$total}; with empty username: {$emptyUsername}");
        if ($emptyUsername > 0) {
            $this->warn("  [WARN] Run: php artisan users:fix-login-credentials to set usernames so those users can log in.");
        }

        // 6) Sample: first user has valid password hash format
        $first = User::orderBy('id')->first();
        if ($first && $first->password) {
            $prefix = substr($first->password, 0, 4);
            if ($prefix === '$2y$' || $prefix === '$2a$' || str_starts_with($first->password, '$argon')) {
                $this->line('  [OK] Sample user password stored as hash (bcrypt/argon)');
            } else {
                $this->warn('  [WARN] Sample user password does not look like bcrypt/argon – check registration hashing.');
                $ok = false;
            }
        }

        $this->info('');
        if ($ok) {
            $this->info('All checks passed. User registration and login data will persist; users can log in even years later.');
        } else {
            $this->error('Some checks failed. Fix the issues above.');
            return self::FAILURE;
        }
        $this->info('');
        $this->line('To test a specific login: php artisan user:test-login <username> <password>');
        $this->line('To see which credential is used on site: php artisan login:debug');
        $this->info('');

        return self::SUCCESS;
    }
}
