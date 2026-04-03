<?php

namespace App\Services;

use App\Models\PaymentEvent;
use App\Models\SecurityEvent;

/**
 * Log payment events for audit and dispute handling.
 * Idempotency: pass idempotency_key to prevent duplicate webhook processing.
 */
class PaymentEventLogger
{
    /**
     * Check if webhook is duplicate (replay). Returns true if already processed.
     */
    public static function isDuplicate(string $gateway, string $idempotencyKey): bool
    {
        return PaymentEvent::isDuplicateIdempotency($gateway, $idempotencyKey);
    }

    /**
     * Log replay attack attempt.
     */
    public static function logReplayAttempt(string $gateway, string $idempotencyKey, array $context = []): void
    {
        SecurityEvent::log('payment_replay_attempt', 'critical', array_merge([
            'payload' => ['gateway' => $gateway, 'idempotency_key' => $idempotencyKey],
        ], $context));
    }

    public static function logWebhook(string $gateway, array $data = []): ?PaymentEvent
    {
        return PaymentEvent::log($gateway, 'webhook_received', $data);
    }

    public static function logSignatureVerified(string $gateway, array $data = []): PaymentEvent
    {
        return PaymentEvent::log($gateway, 'signature_verified', array_merge($data, ['signature_valid' => true]));
    }

    public static function logSignatureFailed(string $gateway, array $data = []): PaymentEvent
    {
        return PaymentEvent::log($gateway, 'signature_failed', array_merge($data, ['signature_valid' => false]));
    }

    public static function logStatusChange(string $gateway, ?int $oldStatus, int $newStatus, array $data = []): ?PaymentEvent
    {
        return PaymentEvent::log($gateway, 'status_change', array_merge($data, [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]));
    }
}
