<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRefund extends Model
{
    protected $table = 'payment_refunds';

    protected $fillable = [
        'deposit_id', 'order_id', 'amount', 'type', 'status',
        'gateway_refund_id', 'processed_at', 'admin_id', 'reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public const TYPE_FULL = 'full';
    public const TYPE_PARTIAL = 'partial';
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_FAILED = 'failed';

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
