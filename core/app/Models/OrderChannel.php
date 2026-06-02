<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderChannel extends Model
{
    protected $fillable = [
        'name',
        'platform',
        'direction',
        'api_url',
        'api_key',
        'webhook_token',
        'settings',
        'is_active',
        'last_sync_at',
        'imported_count',
        'exported_count',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
    ];

    public static function platforms(): array
    {
        return [
            'woocommerce' => __('WooCommerce'),
            'shopify' => __('Shopify'),
            'facebook' => __('Facebook / Instagram Shop'),
            'daraz' => __('Daraz / Marketplace'),
            'custom' => __('Custom REST API'),
        ];
    }

    public static function directions(): array
    {
        return [
            'import' => __('Import only (receive orders)'),
            'export' => __('Export only (push orders)'),
            'both' => __('Import & Export'),
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (OrderChannel $channel) {
            if (empty($channel->webhook_token)) {
                $channel->webhook_token = Str::random(48);
            }
        });
    }

    public function webhookUrl(): string
    {
        return url('api/order-channel/' . $this->webhook_token . '/webhook');
    }
}
