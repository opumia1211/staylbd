<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seed the application database. Run manually only: php artisan db:seed
 * Do NOT use in any automatic reset flow (no migrate:fresh / migrate:refresh with --seed in local dev).
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            PermissionSeeder::class,
            ShippingZonesSeeder::class,
            GatewaySeeder::class,
            ExtensionSeeder::class,
            ProductSizeAttributeSeeder::class,
        ]);
    }
}
