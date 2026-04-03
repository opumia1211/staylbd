<?php

namespace App\Console\Commands;

use App\Models\AdminLockout;
use Illuminate\Console\Command;

class ClearAdminLockoutCommand extends Command
{
    protected $signature = 'admin:clear-lockouts {--ip= : Clear lockout for specific IP only}';

    protected $description = 'Clear admin login lockouts (useful after credential reset)';

    public function handle(): int
    {
        $ip = $this->option('ip');

        $query = AdminLockout::query();
        if ($ip) {
            $query->where('ip_address', $ip);
        }
        $count = $query->whereNotNull('locked_at')->update(['locked_at' => null, 'unlocked_at' => now(), 'failed_attempts' => 0]);

        $this->info("Cleared {$count} admin lockout(s).");
        return 0;
    }
}
