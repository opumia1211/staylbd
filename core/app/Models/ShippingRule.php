<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class ShippingRule extends Model
{
    protected $fillable = [
        'free_shipping_min_amount', 'cod_extra_charge', 'express_extra_charge', 'international_enabled',
    ];

    protected $casts = [
        'free_shipping_min_amount' => 'decimal:2',
        'cod_extra_charge' => 'decimal:2',
        'express_extra_charge' => 'decimal:2',
        'international_enabled' => 'boolean',
    ];

    /**
     * Get shipping rules (cached). Returns null if table does not exist to avoid errors.
     */
    public static function getCached(): ?self
    {
        if (!Schema::hasTable('shipping_rules')) {
            return null;
        }
        try {
            return Cache::remember('shipping_rules', 3600, function () {
                return self::first();
            });
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function clearCache(): void
    {
        if (Schema::hasTable('shipping_rules')) {
            Cache::forget('shipping_rules');
        }
    }
}
