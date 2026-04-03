<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class UserResetPassword extends Command
{
    protected $signature = 'users:reset-password
                            {username : Username or email prefix (e.g. opumiax)}
                            {password : New password}';
    protected $description = 'Set a new password for a user by username or email prefix. Use after users:fix-login-credentials if login fails.';

    public function handle(): int
    {
        $login = trim((string) $this->argument('username'));
        $password = $this->argument('password');

        if ($login === '' || $password === '') {
            $this->error('Username and password are required.');
            return self::FAILURE;
        }

        $loginLower = strtolower($login);

        $user = User::whereRaw('LOWER(username) = ?', [$loginLower])->first();
        if (!$user) {
            $user = User::where('email', $login)->orWhere('email', $loginLower)->first();
        }
        if (!$user && strpos($loginLower, '@') === false) {
            $user = User::whereRaw('LOWER(SUBSTRING_INDEX(email, \'@\', 1)) = ?', [$loginLower])->first();
        }

        if (!$user) {
            $this->error("User not found: {$login}");
            return self::FAILURE;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("Password updated for user id={$user->id} username={$user->username} email={$user->email}");
        $this->info('They can now log in with the new password.');
        return self::SUCCESS;
    }
}
