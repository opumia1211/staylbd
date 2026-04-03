<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Illuminate\Database\Seeder;

/**
 * Seeds default shipping zones and methods:
 * - Inside Dhaka → ৳60 (2-3 Days)
 * - Outside Dhaka → ৳120 (3-5 Days)
 * - Remote Area → ৳150 (5-7 Days)
 * - International Standard → ৳1200 (7-15 Days)
 */
class ShippingZonesSeeder extends Seeder
{
    public function run(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('shipping_zones')) {
            return;
        }

        $zones = [
            ['name' => 'Inside Dhaka', 'type' => 'national', 'base_price' => 60, 'estimated_days' => '2-3 Days'],
            ['name' => 'Outside Dhaka', 'type' => 'national', 'base_price' => 120, 'estimated_days' => '3-5 Days'],
            ['name' => 'Remote Area', 'type' => 'national', 'base_price' => 150, 'estimated_days' => '5-7 Days'],
            ['name' => 'International Standard', 'type' => 'international', 'base_price' => 1200, 'estimated_days' => '7-15 Days'],
        ];

        foreach ($zones as $z) {
            $zone = ShippingZone::firstOrCreate(
                ['name' => $z['name']],
                [
                    'type' => $z['type'],
                    'status' => 1,
                    'base_price' => $z['base_price'],
                    'estimated_days' => $z['estimated_days'],
                ]
            );

            $methodName = $z['name'];
            $data = [
                'name' => $methodName,
                'price' => $z['base_price'],
                'status' => 1,
            ];
            if (\Illuminate\Support\Facades\Schema::hasColumn('shipping_methods', 'shipping_zone_id')) {
                $data['shipping_zone_id'] = $zone->id;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('shipping_methods', 'base_price')) {
                $data['base_price'] = $z['base_price'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('shipping_methods', 'estimated_days')) {
                $data['estimated_days'] = $z['estimated_days'];
            }
            ShippingMethod::updateOrCreate(['name' => $methodName], $data);
        }
    }
}
