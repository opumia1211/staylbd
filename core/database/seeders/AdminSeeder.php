<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Seeds default Super Admin. Idempotent.
     * - Production: uses random temp password; force_password_change blocks access until changed.
     * - Re-run: never overwrites password, email, username of existing admin (sensitive data preserved).
     */
    public function run(): void
    {
        if (!Schema::hasTable('admins')) {
            $this->auditLog('skip', 'admins table missing');
            return;
        }

        $existing = Admin::first();

        if ($existing) {
            // Re-run: only ensure role; do NOT overwrite password, email, username
            if (Schema::hasColumn('admins', 'role')) {
                $existing->update(['role' => Admin::ROLE_OWNER]);
            }
            $this->auditLog('skip', 'Admin exists; only role updated (no sensitive overwrite)');
            return;
        }

        $isProd = app()->environment('production');
        $email = env('ADMIN_EMAIL', 'admin@example.com');

        if ($isProd) {
            $password = Str::random(32); // Not usable; must change on first login
        } else {
            $password = env('ADMIN_PASSWORD', 'TempAdmin123!');
            if (empty($password) || strlen($password) < 8) {
                $password = 'TempAdmin123!';
            }
        }

        $attrs = [
            'name'     => 'Super Admin',
            'username' => $email,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => Schema::hasColumn('admins', 'role') ? Admin::ROLE_OWNER : null,
            'force_password_change' => Schema::hasColumn('admins', 'force_password_change'),
        ];
        Admin::create($attrs);
        $this->auditLog('run', $isProd ? 'Created with temp password (must change on first login)' : 'Created');
    }

    private function auditLog(string $action, string $message): void
    {
        try {
            if (Schema::hasTable('seeder_audit_logs')) {
                \DB::table('seeder_audit_logs')->insert([
                    'seeder_class' => self::class,
                    'action'       => $action,
                    'message'      => $message,
                    'environment'  => app()->environment(),
                    'run_at'       => now(),
                ]);
            }
        } catch (\Throwable $e) {
            \Log::info('Seeder audit: ' . self::class . ' ' . $action . ' – ' . $message);
        }
    }
}
