<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Immutable financial ledger – append-only, hash chained.
 * Dispute-grade integrity.
 */
class PaymentLedger extends Model
{
    protected $table = 'payment_ledger';

    public $timestamps = false;
    const UPDATED_AT = null;

    protected $fillable = [
        'order_id', 'transaction_id', 'deposit_id', 'gateway',
        'amount', 'currency', 'status', 'trx',
        'previous_hash', 'ledger_hash', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'created_at' => 'datetime',
    ];

    /** Prevent updates and deletes. */
    public static function boot()
    {
        parent::boot();
        static::updating(fn ($m) => abort(403, 'Ledger is immutable'));
        static::deleting(fn ($m) => abort(403, 'Ledger is immutable'));
    }

    public static function appendEntry(array $data): self
    {
        $last = self::orderBy('id', 'desc')->first();
        $previousHash = $last ? $last->ledger_hash : null;
        $createdAt = now();

        $payload = self::buildPayload(
            $data['order_id'] ?? null,
            $data['transaction_id'] ?? null,
            $data['deposit_id'] ?? null,
            $data['gateway'] ?? null,
            $data['amount'] ?? 0,
            $data['currency'] ?? 'USD',
            $data['status'] ?? null,
            $data['trx'] ?? null,
            $data['notes'] ?? null,
            $createdAt->toIso8601String()
        );

        $ledgerHash = hash('sha256', ($previousHash ?? '') . $payload);

        return self::create(array_merge($data, [
            'previous_hash' => $previousHash,
            'ledger_hash'   => $ledgerHash,
            'created_at'    => $createdAt,
        ]));
    }

    private static function buildPayload($orderId, $txId, $depositId, $gateway, $amount, $currency, $status, $trx, $notes, string $ts): string
    {
        return json_encode([
            'order_id' => $orderId,
            'transaction_id' => $txId,
            'deposit_id' => $depositId,
            'gateway' => $gateway,
            'amount' => (string) $amount,
            'currency' => $currency,
            'status' => $status,
            'trx' => $trx,
            'notes' => $notes,
            '_ts' => $ts,
        ], JSON_UNESCAPED_UNICODE);
    }

    public static function verifyIntegrity(): array
    {
        $rows = self::orderBy('id')->get();
        $errors = [];
        $prevHash = null;
        foreach ($rows as $row) {
            $payload = self::buildPayload(
                $row->order_id,
                $row->transaction_id,
                $row->deposit_id,
                $row->gateway,
                $row->amount,
                $row->currency,
                $row->status,
                $row->trx,
                $row->notes,
                $row->created_at->toIso8601String()
            );
            $expected = hash('sha256', ($prevHash ?? '') . $payload);
            if ($row->ledger_hash !== $expected || ($prevHash !== null && $row->previous_hash !== $prevHash)) {
                $errors[] = ['id' => $row->id, 'expected' => $expected, 'got' => $row->ledger_hash];
            }
            $prevHash = $row->ledger_hash;
        }
        return $errors;
    }
}
