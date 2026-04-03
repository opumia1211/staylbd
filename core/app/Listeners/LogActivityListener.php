<?php

namespace App\Listeners;

use App\Events\UserActivityOccurred;
use App\Jobs\LogActivityJob;

/**
 * Listens to UserActivityOccurred and dispatches job to queue.
 * Runs synchronously (fast); worker performs DB insert so request is not slowed.
 */
class LogActivityListener
{
    public function handle(UserActivityOccurred $event): void
    {
        LogActivityJob::dispatch($event->payload);
    }
}
