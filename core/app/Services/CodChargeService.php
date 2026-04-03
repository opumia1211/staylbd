<?php

namespace App\Services;

use App\Models\CodSettings;
use App\Models\ShippingRule;
use App\Constants\Status;
use Illuminate\Support\Facades\Schema;

class CodChargeService
{
    /**
     * Calculate COD charge for given order subtotal (after discount, before shipping).
     * Returns ['charge' => float, 'free_applied' => bool].
     */
    public function calculate(float $orderSubtotal): array
    {
        $cod = CodSettings::getCached();
        if (!$cod || !$cod->cod_enabled) {
            return ['charge' => 0, 'free_applied' => false];
        }
        $freeAbove = (float) ($cod->cod_free_above ?? 0);
        if ($freeAbove > 0 && $orderSubtotal >= $freeAbove) {
            return ['charge' => 0, 'free_applied' => true];
        }
        $chargeValue = (float) ($cod->cod_charge_value ?? 0);
        if ($chargeValue <= 0) {
            return ['charge' => 0, 'free_applied' => false];
        }
        if ($cod->isChargeFlat()) {
            return ['charge' => round($chargeValue, 2), 'free_applied' => false];
        }
        $charge = round($orderSubtotal * ($chargeValue / 100), 2);
        return ['charge' => $charge, 'free_applied' => false];
    }

    /**
     * Legacy: COD extra from shipping_rules (added on top of shipping cost in ShippingService).
     * This method is for displaying/using standalone COD fee in checkout (separate line).
     */
    public function getLegacyCodExtraFromShippingRules(): float
    {
        if (!Schema::hasTable('shipping_rules')) {
            return 0;
        }
        $rules = ShippingRule::getCached();
        return $rules ? (float) ($rules->cod_extra_charge ?? 0) : 0;
    }
}
