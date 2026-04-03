<?php

namespace App\Console\Commands;

use App\Models\AbandonedCart;
use Illuminate\Console\Command;

class CleanupExpiredAbandonedCarts extends Command
{
    protected $signature = 'abandoned-cart:cleanup';
    protected $description = 'Remove abandoned cart records older than configured days';

    public function handle(): int
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('abandoned_carts')) {
            $this->warn('Abandoned carts table not found. Run migrations.');
            return Command::SUCCESS;
        }
        $general = gs();
        $days = (int) (isset($general->abandoned_cart_cleanup_days) ? $general->abandoned_cart_cleanup_days : 30);
        $cutoff = now()->subDays($days);

        $deleted = AbandonedCart::where('last_activity_at', '<', $cutoff)->delete();
        $this->info("Deleted {$deleted} expired abandoned cart record(s).");
        return Command::SUCCESS;
    }
}
