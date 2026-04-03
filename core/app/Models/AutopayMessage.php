<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutopayMessage extends Model
{
    protected $fillable = [
        'method_code', 'deposit_id', 'sender', 'raw_message', 'amount', 'trx_id', 'matched',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'matched' => 'boolean',
    ];

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }
}
