<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetAdminCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:set-credentials 
                            {email : Admin login email} 
                            {password : Admin login password}
                            {--id=1 : Admin user ID to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update admin panel login email and password (e.g. php artisan admin:set-credentials your@email.com "yourPassword")';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $id = (int) $this->option('id');

        $admin = Admin::find($id);
        if (!$admin) {
            $this->error("Admin with ID {$id} not found.");
            return 1;
        }

        $admin->email = $email;
        $admin->username = $email; // keep username same as email so both work
        $admin->password = Hash::make($password);
        $admin->save();

        $this->info('Admin credentials updated successfully.');
        $this->line("Email: {$email}");
        $this->line('Password: (updated)');
        return 0;
    }
}
