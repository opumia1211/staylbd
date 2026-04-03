<?php

namespace App\Console\Commands;

use App\Models\AbandonedCart;
use App\Jobs\SendAbandonedCartReminderJob;
use Illuminate\Console\Command;

class DetectAbandonedCarts extends Command
{
    protected $signature = 'abandoned-cart:detect';
    protected $description = 'Mark inactive carts as abandoned and queue reminder notifications';

    public function handle(): int
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('abandoned_carts')) {
            $this->warn('Abandoned carts table not found. Run migrations.');
            return Command::SUCCESS;
        }
        $general = gs();
        $minutes = (int) (isset($general->abandoned_cart_inactivity_minutes) ? $general->abandoned_cart_inactivity_minutes : 60);
        $cutoff = now()->subMinutes($minutes);

        $updated = AbandonedCart::where('status', AbandonedCart::STATUS_PENDING)
            ->where('last_activity_at', '<', $cutoff)
            ->update(['status' => AbandonedCart::STATUS_ABANDONED]);

        $abandoned = AbandonedCart::where('status', AbandonedCart::STATUS_ABANDONED)
            ->where(function ($q) {
                $q->whereNull('reminder_sent_at')->orWhere('reminder_sent_at', '<', now()->subDays(1));
            })
            ->get();

        $sent = 0;
        foreach ($abandoned as $ac) {
            $hasContact = $ac->email || $ac->mobile || ($ac->user_id && $ac->user && ($ac->user->email || $ac->user->mobile));
            if ($hasContact) {
                SendAbandonedCartReminderJob::dispatch($ac);
                $ac->update(['reminder_sent_at' => now()]);
                $sent++;
            }
        }

        $this->info("Marked {$updated} cart(s) as abandoned. Queued {$sent} reminder(s).");
        return Command::SUCCESS;
    }
}
