<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('tickets:cleanup --days=30')->daily()->at('01:00');
        $schedule->command('data:retention:enforce')->daily()->at('03:00');
        $schedule->command('maintenance:clean-temp-cache')->daily()->at('04:00');

        // Auto delete trashed uploads + temp cache (delayed-delete)
        $schedule->command('staylbd:cleanup-trashed-files --temp')->daily()->at('04:05');

        $schedule->command('search:cleanup')->daily()->at('04:15');
        $schedule->command('maintenance:clean-logs', ['--keep-days' => config('maintenance.log_keep_days', 7)])->daily()->at('04:30');
        $schedule->command('maintenance:run-full', ['--skip-db' => true])->weeklyOn(0, '05:00');
        $schedule->command('activity:archive')->daily()->at('02:00');

        // Abandoned cart: mark inactive carts as abandoned and queue reminders
        $schedule->command('abandoned-cart:detect')->everyThirtyMinutes();
        $schedule->command('abandoned-cart:cleanup')->daily()->at('02:30');

        // Auto optimize (optional): caches config/routes/views for max speed.
        if (env('AUTO_OPTIMIZE', false)) {
            $schedule->command('app:optimize')->weeklyOn(0, '03:30');
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
