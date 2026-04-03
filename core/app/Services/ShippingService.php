<?php

namespace App\Services;

use App\Models\ShippingMethod;
use App\Models\ShippingRule;
use App\Models\ShippingZone;
use App\Models\ShippingZoneArea;
use App\Models\ShippingZoneCountry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ShippingService
{
    protected ?ShippingRule $rules = null;

    public function __construct()
    {
        $this->rules = ShippingRule::getCached();
    }

    /** Check if zone-based shipping tables exist (safe for missing migrations). */
    protected function zonesTableExists(): bool
    {
        return Schema::hasTable('shipping_zones');
    }

    /** Check if shipping_methods has zone column. */
    protected function methodsHaveZoneColumn(): bool
    {
        return Schema::hasTable('shipping_methods') && Schema::hasColumn('shipping_methods', 'shipping_zone_id');
    }

    /**
     * Detect zone by country and city. Strict logic:
     * - Bangladesh + city = "Dhaka" → Inside Dhaka
     * - Bangladesh + city != "Dhaka" → Outside Dhaka
     * - Non-Bangladesh → International Standard (flat ৳1200)
     */
    public function detectZone(string $countryIso, string $city = '', string $state = ''): ?ShippingZone
    {
        return $this->resolveZone($countryIso, $city, $state);
    }

    /**
     * Resolve shipping zone by country (ISO) and optional city/state (for Bangladesh).
     */
    public function resolveZone(string $countryIso, string $city = '', string $state = ''): ?ShippingZone
    {
        if (!$this->zonesTableExists()) {
            return null;
        }
        try {
            $countryIso = strtoupper(trim($countryIso));
            if ($countryIso === 'BD') {
                return $this->resolveBangladeshZone($city, $state);
            }
            // International: return first active international zone (e.g. International Standard — ৳1200)
            $zone = ShippingZone::where('type', 'international')->where('status', 1)->orderBy('id')->first();
            if ($zone) {
                return $zone;
            }
            if (Schema::hasTable('shipping_zone_countries')) {
                $zoneCountry = ShippingZoneCountry::where('country_iso', $countryIso)
                    ->where('status', 1)
                    ->whereHas('zone', fn ($q) => $q->where('status', 1))
                    ->with('zone')
                    ->first();
                return $zoneCountry?->zone;
            }
            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Bangladesh: Match zone by district (ShippingZoneArea.district_names) for location-based charge;
     * fallback: Dhaka → Inside Dhaka, else → Outside Dhaka / Remote Area.
     */
    protected function resolveBangladeshZone(string $city, string $state): ?ShippingZone
    {
        $district = trim($city ?: $state);
        try {
            // First: match by zone areas (admin-configured district_names) for location-based delivery charge
            if ($district && Schema::hasTable('shipping_zone_areas')) {
                $area = ShippingZoneArea::where('status', 1)
                    ->with('zone')
                    ->whereHas('zone', fn ($q) => $q->where('status', 1)->where('type', 'national'))
                    ->get()
                    ->first(function ($a) use ($district) {
                        $names = $a->district_names ?? [];
                        foreach ($names as $d) {
                            if (strcasecmp(trim((string) $d), $district) === 0) {
                                return true;
                            }
                        }
                        return false;
                    });
                if ($area && $area->relationLoaded('zone') && $area->zone) {
                    return $area->zone;
                }
            }
            // Fallback: Dhaka → Inside Dhaka, else → Outside Dhaka
            $isDhaka = (strtolower($district) === 'dhaka');
            if ($isDhaka) {
                $zone = ShippingZone::where('type', 'national')->where('status', 1)
                    ->where('name', 'Inside Dhaka')->first();
                if ($zone) {
                    return $zone;
                }
            }
            $zone = ShippingZone::where('type', 'national')->where('status', 1)
                ->where('name', 'Outside Dhaka')->first();
            if ($zone) {
                return $zone;
            }
            $zone = ShippingZone::where('type', 'national')->where('status', 1)
                ->where('name', 'Remote Area')->first();
            return $zone ?? ShippingZone::national()->where('status', 1)->orderBy('base_price')->first();
        } catch (\Throwable $e) {
            return ShippingZone::national()->where('status', 1)->orderBy('id')->first();
        }
    }

    /**
     * Get available shipping methods for a zone (and optional cart total, payment type for rules).
     */
    public function getMethodsForZone(?ShippingZone $zone, float $cartSubtotal = 0, int $paymentType = 0): Collection
    {
        if (!$zone || !$this->methodsHaveZoneColumn()) {
            return $this->getLegacyMethods();
        }
        try {
            $methods = ShippingMethod::where('shipping_zone_id', $zone->id)
                ->where('status', 1)
                ->orderByRaw('COALESCE(is_express, 0)')
                ->orderByRaw('COALESCE(base_price, price, 0)')
                ->get();
            if ($methods->isEmpty()) {
                return $this->getLegacyMethods();
            }
            return $methods;
        } catch (\Throwable $e) {
            return $this->getLegacyMethods();
        }
    }

    /**
     * Legacy: methods without zone (current dropdown behavior). Safe when shipping_zone_id column missing.
     */
    public function getLegacyMethods(): Collection
    {
        try {
            if ($this->methodsHaveZoneColumn()) {
                return ShippingMethod::whereNull('shipping_zone_id')
                    ->where('status', 1)
                    ->orderBy('price')
                    ->get();
            }
            return ShippingMethod::where('status', 1)->orderBy('price')->get();
        } catch (\Throwable $e) {
            return ShippingMethod::where('status', 1)->orderBy('price')->get();
        }
    }

    /**
     * Calculate shipping cost for a method (with rules: free over X, COD extra, express extra).
     */
    public function calculateCost(ShippingMethod $method, float $cartSubtotal, int $paymentType, float $weightKg = 0): array
    {
        $base = $method->getEffectivePrice();
        $extra = 0;

        if ($method->price_per_kg && $weightKg > 0) {
            $extra += (float) $method->price_per_kg * $weightKg;
        }

        $shippingCost = $base + $extra;

        if ($this->rules && $this->rules->free_shipping_min_amount !== null && $cartSubtotal >= (float) $this->rules->free_shipping_min_amount) {
            $shippingCost = 0;
        } else {
            if ($this->rules && $paymentType == 2) { // COD
                $shippingCost += (float) ($this->rules->cod_extra_charge ?? 0);
            }
            if ($method->is_express && $this->rules) {
                $shippingCost += (float) ($this->rules->express_extra_charge ?? 0);
            }
        }

        return [
            'cost' => round($shippingCost, 2),
            'free_applied' => $shippingCost == 0 && $this->rules && $this->rules->free_shipping_min_amount !== null && $cartSubtotal >= (float) $this->rules->free_shipping_min_amount,
            'estimated_days' => $method->estimated_days ?? $method->zone?->estimated_days,
            'courier_name' => $method->courier_name,
        ];
    }

    /**
     * Get methods for checkout: by country + city/state, with calculated cost.
     */
    public function getMethodsForCheckout(string $countryIso, string $city, string $state, float $cartSubtotal, int $paymentType, float $weightKg = 0): array
    {
        $zone = $this->resolveZone($countryIso, $city, $state);
        $methods = $this->getMethodsForZone($zone, $cartSubtotal, $paymentType);

        $list = [];
        foreach ($methods as $method) {
            $calc = $this->calculateCost($method, $cartSubtotal, $paymentType, $weightKg);
            $list[] = [
                'id' => $method->id,
                'name' => $method->name,
                'price' => $calc['cost'],
                'estimated_days' => $calc['estimated_days'],
                'courier_name' => $calc['courier_name'],
                'is_express' => (bool) $method->is_express,
                'free_applied' => $calc['free_applied'],
            ];
        }

        return $list;
    }

    /**
     * Get shipping options (methods) for a zone by zone id. Used for API/admin.
     */
    public function getShippingOptions(?int $zoneId): Collection
    {
        if (!$zoneId || !$this->methodsHaveZoneColumn()) {
            return $this->getLegacyMethods();
        }
        $zone = ShippingZone::where('id', $zoneId)->where('status', 1)->first();
        return $this->getMethodsForZone($zone, 0, 0);
    }

    public function getRules(): ?ShippingRule
    {
        return $this->rules;
    }
}
