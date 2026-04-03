<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a user action should be logged.
 * Listener queues LogActivityJob so request is not slowed; worker does DB insert.
 */
class UserActivityOccurred
{
    use Dispatchable, SerializesModels;

    /** @var array<string, mixed> All scalar/null values for queue serialization */
    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }
}
