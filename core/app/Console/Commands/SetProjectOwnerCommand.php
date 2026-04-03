<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SetProjectOwnerCommand extends Command
{
    protected $signature = 'staylbd:set-owner
                            {email? : Owner email (default: from config or digitalzero.com@gmail.com)}
                            {password? : Owner password}';

    protected $description = 'Set or update the project Owner (single admin with full control). Creates admin if not found, demotes other owners to super_admin.';

    public function handle(): int
    {
        $email = $this->argument('email') ?: config('admin.owner_email', 'digitalzero.com@gmail.com');
        $password = $this->argument('password');

        if (empty($password)) {
            $this->warn('Run with password: php artisan staylbd:set-owner "' . $email . '" "YourPassword"');
            $password = $this->secret('Enter owner password');
            if (empty($password)) {
                $this->error('Password is required.');
                return 1;
            }
        }

        if (!Schema::hasColumn('admins', 'role')) {
            $this->error('Admins table has no role column. Run migrations first.');
            return 1;
        }

        // Demote any existing owners (except the one we are setting) to super_admin
        Admin::where('role', Admin::ROLE_OWNER)->where('email', '!=', $email)->update([
            'role' => Admin::ROLE_SUPER_ADMIN,
            'allowed_sections' => null,
        ]);

        $admin = Admin::where('email', $email)->first();

        if ($admin) {
            $admin->password = Hash::make($password);
            $admin->role = Admin::ROLE_OWNER;
            $admin->allowed_sections = null;
            $admin->force_password_change = false;
            $admin->save();
            $this->info('Project Owner updated: ' . $email);
        } else {
            $admin = new Admin();
            $admin->name = 'Project Owner';
            $admin->username = $email;
            $admin->email = $email;
            $admin->password = Hash::make($password);
            $admin->role = Admin::ROLE_OWNER;
            $admin->allowed_sections = null;
            $admin->force_password_change = false;
            if (Schema::hasColumn('admins', 'mobile')) {
                $admin->mobile = null;
            }
            $admin->save();
            $this->info('Project Owner created: ' . $email);
        }

        $this->line('Login: ' . config('admin.prefix', 'admin') . ' → ' . $email);
        $this->line('Only this Owner can access Admin Management and Security Dashboard.');
        return 0;
    }
}
