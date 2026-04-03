<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class PaymentTransaction extends Model
{
    protected $table = 'payment_transactions';

    protected $fillable = [
        'order_id', 'deposit_id', 'payment_method', 'transaction_id',
        'gateway_response', 'amount', 'currency', 'status', 'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'gateway_response' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }

    public static function recordFromDeposit(Deposit $deposit): void
    {
        if (!Schema::hasTable('payment_transactions')) {
            return;
        }
        self::create([
            'order_id' => $deposit->order_id ?: null,
            'deposit_id' => $deposit->id,
            'payment_method' => $deposit->method_code,
            'transaction_id' => $deposit->trx,
            'gateway_response' => $deposit->detail,
            'amount' => $deposit->final_amo,
            'currency' => $deposit->method_currency ?? 'BDT',
            'status' => 'success',
            'paid_at' => now(),
        ]);
    }
}
