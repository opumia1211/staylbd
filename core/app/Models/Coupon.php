<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use Searchable, GlobalStatus;

    protected $fillable = [
        'name',
        'discount',
        'discount_type',
        'min_order',
        'start_date',
        'end_date',
        'status',
        'usage_limit',
        'max_discount',
        'per_user_limit',
        'description',
        'type',
        'is_first_order_only',
    ];

    protected $casts = [
        'discount'     => 'decimal:2',
        'min_order'    => 'decimal:2',
        'max_discount' => 'decimal:2',
        'start_date'   => 'date',
        'end_date'     => 'date',
        'is_first_order_only' => 'boolean',
    ];

    /** Times this coupon has been used (from orders). */
    public function ordersUsed()
    {
        return $this->hasMany(Order::class);
    }

    /** Count of orders using this coupon. Uses orders_used_count when loaded with withCount('ordersUsed'). */
    public function getUsedCountAttribute()
    {
        if (array_key_exists('orders_used_count', $this->attributes)) {
            return (int) $this->attributes['orders_used_count'];
        }
        return \App\Models\Order::where('coupon_id', $this->id)->count();
    }

    /** Check if coupon is active (within date, enabled, usage limit not exceeded). */
    public function isCurrentlyActive(): bool
    {
        if ($this->status != 1) return false;
        if (now()->format('Y-m-d') < $this->start_date) return false;
        if (now()->format('Y-m-d') > $this->end_date) return false;
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) return false;
        return true;
    }
}
