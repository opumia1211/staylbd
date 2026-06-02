<?php

namespace App\Console\Commands;

use App\Models\OrderAutomationSetting;
use App\Services\OrderAutomationService;
use Illuminate\Console\Command;

class OrderAutomationRunCommand extends Command
{
    protected $signature = 'orders:automation-run';

    protected $description = 'Run order automation rules (confirm paid, cancel stale, etc.)';

    public function handle(OrderAutomationService $automation): int
    {
        if (!$automation->isAvailable()) {
            $this->warn('order_automation_settings table missing.');
            return self::SUCCESS;
        }

        $settings = OrderAutomationSetting::current();
        if (!$settings->is_enabled) {
            $this->info('Order automation is disabled.');
            return self::SUCCESS;
        }

        $result = $automation->run($settings);
        $this->info(sprintf(
            'Done: %d confirmed, %d processing, %d cancelled.',
            $result['confirmed'],
            $result['processing'],
            $result['cancelled']
        ));

        return self::SUCCESS;
    }
}
