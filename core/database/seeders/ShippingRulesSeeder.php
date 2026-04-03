<?php

namespace Database\Seeders;

use App\Models\ShippingRule;
use Illuminate\Database\Seeder;

class ShippingRulesSeeder extends Seeder
{
    public function run()
    {
        if (ShippingRule::exists()) {
            return;
        }
        ShippingRule::create([
            'free_shipping_min_amount' => 5000,
            'cod_extra_charge' => 0,
            'express_extra_charge' => 50,
            'international_enabled' => true,
        ]);
    }
}
