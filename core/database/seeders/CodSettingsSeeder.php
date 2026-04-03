<?php

namespace Database\Seeders;

use App\Models\CodSettings;
use App\Constants\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CodSettingsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('cod_settings') || CodSettings::exists()) {
            return;
        }
        CodSettings::create([
            'cod_enabled' => true,
            'cod_min_order' => 500,
            'cod_max_order' => 20000,
            'cod_charge_type' => Status::COD_CHARGE_FLAT,
            'cod_charge_value' => 50,
            'cod_free_above' => 2000,
            'cod_otp_required' => false,
            'cod_otp_expire_minutes' => 10,
            'cod_auto_cancel_hours' => 24,
            'cod_failed_disable_count' => 2,
            'cod_new_customer_max' => 5000,
        ]);
    }
}
