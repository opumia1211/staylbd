<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use Searchable, GlobalStatus;

    protected $fillable = [
        'name', 'price', 'status', 'shipping_zone_id', 'base_price', 'price_per_kg',
        'estimated_days', 'courier_name', 'is_express', 'weight_limit_kg',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'base_price' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'weight_limit_kg' => 'decimal:2',
        'is_express' => 'boolean',
    ];

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function getEffectivePrice(): float
    {
        if ($this->base_price !== null && (float) $this->base_price > 0) {
            return (float) $this->base_price;
        }
        return (float) ($this->price ?? 0);
    }
}
