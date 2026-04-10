<?php

namespace App\Services;

use App\Constants\Status;
use App\Models\Deposit;

/**
 * Shared helpers for payment IPN/webhook handlers: idempotency, amount/currency checks, deposit state.
 * Gateways should still verify provider signatures (HMAC, Stripe constructEvent, PayPal VERIFIED, etc.).
 */
class PaymentIpnService
{
    /**
     * @return true if this delivery is a duplicate and should be ignored (replay).
     */
    public static function isReplay(string $gateway, string $idempotencyKey): bool
    {
        if (PaymentEventLogger::isDuplicate($gateway, $idempotencyKey)) {
            PaymentEventLogger::logReplayAttempt($gateway, $idempotencyKey);

            return true;
        }

        return false;
    }

    public static function depositAwaitingPayment(?Deposit $deposit): bool
    {
        return $deposit !== null && (int) $deposit->status === (int) Status::PAYMENT_INITIATE;
    }

    /**
     * Compare monetary amounts with a small tolerance for float rounding.
     */
    public static function amountsMatch(float $expected, float $actual, float $epsilon = 0.02): bool
    {
        return abs($expected - $actual) <= $epsilon;
    }

    public static function currencyMatches(string $expected, string $actual): bool
    {
        return strtoupper(trim($expected)) === strtoupper(trim($actual));
    }
}
