<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'points',
        'type',
        'description',
        'balance_after'
    ];

    protected $casts = [
        'points' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    // Types
    const TYPE_EARNED = 'earned';
    const TYPE_REDEEMED = 'redeemed';
    const TYPE_EXPIRED = 'expired';
    const TYPE_ADJUSTED = 'adjusted';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope for earned points
     */
    public function scopeEarned($query)
    {
        return $query->where('type', self::TYPE_EARNED);
    }

    /**
     * Scope for redeemed points
     */
    public function scopeRedeemed($query)
    {
        return $query->where('type', self::TYPE_REDEEMED);
    }
}
