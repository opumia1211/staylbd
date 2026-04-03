<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Test user login: find user by username (or email/email prefix/mobile) and verify password.
 * Use to verify why a user cannot log in after registration.
 *
 * Run: php artisan user:test-login opumiax 0987654321
 *      php artisan user:test-login bfeyfgy 1234567890
 */
class TestUserLoginCommand extends Command
{
    protected $signature = 'user:test-login
                            {username : Login name (username, email, or mobile)}
                            {password : Password to check}
                            {--show-hash : Print password hash prefix from DB}';
    protected $description = 'Test if a user can be found and password matches (diagnose login failures)';

    public function handle(): int
    {
        $login = trim((string) $this->argument('username'));
        $password = $this->argument('password');

        if ($login === '') {
            $this->error('Username/login cannot be empty.');
            return self::FAILURE;
        }

        if (!Schema::hasTable('users')) {
            $this->error('Table "users" does not exist.');
            return self::FAILURE;
        }

        $this->line('');
        $this->info('Looking up user with login: ' . $login);
        $this->line('');

        $loginLower = strtolower($login);
        $user = null;
        $by = '';

        if (Schema::hasColumn('users', 'username')) {
            $user = User::whereRaw('LOWER(username) = ?', [$loginLower])->first();
            if ($user) {
                $by = 'username';
            }
        }
        if (!$user) {
            $user = User::where('email', $login)->orWhere('email', $loginLower)->first();
            if ($user) {
                $by = 'email';
            }
        }
        if (!$user && strpos($loginLower, '@') === false) {
            $user = User::whereRaw('LOWER(SUBSTRING_INDEX(email, \'@\', 1)) = ?', [$loginLower])->first();
            if ($user) {
                $by = 'email prefix';
            }
        }
        if (!$user && preg_match('/^[0-9+\-\s]{8,20}$/', $login)) {
            $user = User::where('mobile', $login)->first();
            if ($user) {
                $by = 'mobile';
            }
        }

        if (!$user) {
            $this->error('User NOT FOUND. No row with this username, email, or mobile.');
            $this->line('Check:');
            $this->line('  1. Did registration complete? Check users table for this username/email.');
            $this->line('  2. Run: php artisan users:fix-login-credentials --show (to see users with empty username).');
            $this->line('');
            return self::FAILURE;
        }

        $this->info('User FOUND (by ' . $by . ')');
        $this->line('  id:       ' . $user->id);
        $this->line('  username: ' . ($user->username ?? '(empty)'));
        $this->line('  email:    ' . ($user->email ?? '(empty)'));
        $this->line('  mobile:   ' . ($user->mobile ?? '(empty)'));
        $this->line('  status:   ' . ($user->status ?? 1));
        if ($this->option('show-hash')) {
            $hash = $user->password ?? '';
            $this->line('  password_hash: ' . substr($hash, 0, 30) . '...');
        }
        $this->line('');

        $valid = Hash::check($password, $user->password);
        if ($valid) {
            $this->info('Password: MATCH – this user should be able to log in.');
            $this->line('If login still fails in browser, check: session, captcha, or login form field name (must be "username").');
        } else {
            $this->error('Password: NO MATCH – wrong password or password was not saved correctly at registration.');
            $this->line('Ask user to reset password or re-register.');
        }
        $this->line('');

        return $valid ? self::SUCCESS : self::FAILURE;
    }
}
