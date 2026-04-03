<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentFraudAttempt extends Model
{
    protected $table = 'payment_fraud_attempts';

    protected $fillable = ['ip_address', 'user_id', 'order_id', 'gateway', 'reason'];

    public static function record(string $ipAddress, ?int $userId = null, ?int $orderId = null, ?string $gateway = null, string $reason = 'failed_attempt'): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('payment_fraud_attempts')) {
            return;
        }
        self::create([
            'ip_address' => $ipAddress,
            'user_id' => $userId,
            'order_id' => $orderId,
            'gateway' => $gateway,
            'reason' => $reason,
        ]);
    }
}
