<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class CodSettings extends Model
{
    protected $table = 'cod_settings';

    protected $fillable = [
        'cod_enabled',
        'cod_min_order',
        'cod_max_order',
        'cod_charge_type',
        'cod_charge_value',
        'cod_free_above',
        'cod_otp_required',
        'cod_otp_expire_minutes',
        'cod_auto_cancel_hours',
        'cod_failed_disable_count',
        'cod_new_customer_max',
    ];

    protected $casts = [
        'cod_enabled' => 'boolean',
        'cod_min_order' => 'decimal:2',
        'cod_max_order' => 'decimal:2',
        'cod_charge_value' => 'decimal:2',
        'cod_free_above' => 'decimal:2',
        'cod_otp_required' => 'boolean',
        'cod_otp_expire_minutes' => 'integer',
        'cod_auto_cancel_hours' => 'integer',
        'cod_failed_disable_count' => 'integer',
        'cod_new_customer_max' => 'decimal:2',
    ];

    public static function getCached(): ?self
    {
        if (!Schema::hasTable('cod_settings')) {
            return null;
        }
        try {
            return Cache::remember('cod_settings', 3600, function () {
                return self::first();
            });
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function clearCache(): void
    {
        if (Schema::hasTable('cod_settings')) {
            Cache::forget('cod_settings');
        }
    }

    public function isChargeFlat(): bool
    {
        return (int) ($this->cod_charge_type ?? Status::COD_CHARGE_FLAT) === Status::COD_CHARGE_FLAT;
    }
}
