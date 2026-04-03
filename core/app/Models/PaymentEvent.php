<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentEvent extends Model
{
    protected $fillable = [
        'gateway', 'idempotency_key', 'trx', 'deposit_id', 'order_id', 'event_type',
        'old_status', 'new_status', 'signature_valid', 'ip_address',
        'gateway_response', 'webhook_payload', 'notes',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'webhook_payload'  => 'array',
        'signature_valid'  => 'boolean',
    ];

    public function deposit(): BelongsTo
    {
        return $this->belongsTo(Deposit::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function log(string $gateway, string $eventType, array $data = []): self
    {
        return self::create(array_merge([
            'gateway'    => $gateway,
            'event_type' => $eventType,
            'ip_address' => request()->ip(),
        ], $data));
    }

    public static function isDuplicateIdempotency(string $gateway, string $idempotencyKey): bool
    {
        return self::where('gateway', $gateway)->where('idempotency_key', $idempotencyKey)->exists();
    }
}
