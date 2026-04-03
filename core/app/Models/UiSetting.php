<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class UiSetting extends Model
{
    protected $fillable = [
        'product_card_bg',
        'product_button_color',
        'product_buy_now_color',
        'product_buy_now_hover',
        'product_price_color',
        'header_bg',
        'footer_bg',
        'rating_color',
        'discount_badge_color',
        'stock_color',
        'shipping_badge_color',
        'theme_template',
    ];

    /**
     * Get the single UI settings row (id = 1). Cached for frontend.
     */
    public static function getSettings(): self
    {
        return Cache::remember('ui_settings', 300, function () {
            $row = static::find(1);
            if ($row) {
                return $row;
            }
            return static::firstOrCreate(['id' => 1], [
                'product_card_bg' => '#ffffff',
                'product_button_color' => '#1f2937',
                'product_buy_now_color' => '#0e9f90',
                'product_buy_now_hover' => '#0c8a7d',
                'product_price_color' => '#0e9f90',
                'rating_color' => '#f59e0b',
                'discount_badge_color' => '#dc2626',
                'stock_color' => '#16a34a',
                'shipping_badge_color' => '#2563eb',
                'theme_template' => 'default',
            ]);
        });
    }

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('ui_settings');
        });
    }
}
