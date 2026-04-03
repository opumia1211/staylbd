<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * Fired when a product is restocked and a user (with WhatsApp/Telegram) should receive a message.
 * Listen to this event to send via Twilio/WhatsApp Business API or Telegram Bot API.
 * Payload: channel (whatsapp|telegram), to (number or username), message (string).
 */
class RestockNotifySocial
{
    use Dispatchable;

    /** @var array{channel: string, to: string, message: string} */
    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }
}
