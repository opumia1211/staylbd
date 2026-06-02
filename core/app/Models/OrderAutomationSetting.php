<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAutomationSetting extends Model
{
    protected $fillable = [
        'is_enabled',
        'auto_confirm_paid',
        'auto_processing_after_confirm',
        'auto_cancel_unpaid_days',
        'auto_cancel_unpaid_enabled',
        'notify_customer_on_auto',
        'notify_admin_new_order',
        'channel_import_enabled',
        'run_interval_minutes',
        'sla_pending_hours',
        'sla_fulfillment_hours',
        'sla_alerts_enabled',
        'last_run_at',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'auto_confirm_paid' => 'boolean',
        'auto_processing_after_confirm' => 'boolean',
        'auto_cancel_unpaid_enabled' => 'boolean',
        'notify_customer_on_auto' => 'boolean',
        'notify_admin_new_order' => 'boolean',
        'channel_import_enabled' => 'boolean',
        'sla_alerts_enabled' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    public static function current(): self
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('order_automation_settings')) {
            return new self([
                'is_enabled' => false,
                'auto_confirm_paid' => true,
                'auto_processing_after_confirm' => false,
                'auto_cancel_unpaid_days' => 7,
                'auto_cancel_unpaid_enabled' => false,
            ]);
        }

        return static::query()->first() ?? static::query()->create([]);
    }
}
