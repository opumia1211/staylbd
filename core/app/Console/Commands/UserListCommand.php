<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UserListCommand extends Command
{
    protected $signature = 'users:list
                            {--limit=20 : Max users to show}
                            {--search= : Filter by username or email}';
    protected $description = 'List users (id, username, email) to verify who is in the database.';

    public function handle(): int
    {
        $query = User::query()->orderBy('id');
        if ($search = trim((string) $this->option('search'))) {
            $q = '%' . $search . '%';
            $query->where(function ($qry) use ($q) {
                $qry->where('username', 'like', $q)->orWhere('email', 'like', $q);
            });
        }
        $users = $query->limit((int) $this->option('limit'))->get(['id', 'username', 'email', 'created_at']);
        if ($users->isEmpty()) {
            $this->warn('No users found.');
            return self::SUCCESS;
        }
        $this->info('Users in database (current connection):');
        $this->info('DB: ' . config('database.connections.' . config('database.default') . '.database'));
        $this->table(['ID', 'Username', 'Email', 'Created'], $users->map(fn ($u) => [$u->id, $u->username ?? '-', $u->email ?? '-', $u->created_at?->format('Y-m-d H:i') ?? '-'])->toArray());
        $this->info('To reset password: php artisan users:reset-password <username> <new-password>');
        return self::SUCCESS;
    }
}
