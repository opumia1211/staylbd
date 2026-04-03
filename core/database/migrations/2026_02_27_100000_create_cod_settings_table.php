<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cod_settings')) {
            return;
        }
        Schema::create('cod_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('cod_enabled')->default(true);
            // Min/Max order value for COD (0 = no limit)
            $table->decimal('cod_min_order', 18, 2)->default(0);
            $table->decimal('cod_max_order', 18, 2)->default(0)->comment('0 = no max limit');
            // Charge: flat (1) or percent (2)
            $table->tinyInteger('cod_charge_type')->default(1)->comment('1=flat, 2=percent');
            $table->decimal('cod_charge_value', 18, 2)->default(0);
            $table->decimal('cod_free_above', 18, 2)->default(0)->comment('Free COD above this order amount; 0=disabled');
            // OTP & fraud
            $table->boolean('cod_otp_required')->default(false);
            $table->unsignedSmallInteger('cod_otp_expire_minutes')->default(10);
            $table->unsignedSmallInteger('cod_auto_cancel_hours')->default(24)->comment('Cancel unverified COD order after N hours');
            // Smart restriction
            $table->unsignedTinyInteger('cod_failed_disable_count')->default(2)->comment('Disable COD after N failed deliveries');
            $table->decimal('cod_new_customer_max', 18, 2)->default(0)->comment('Max order for new customer COD; 0=use cod_max_order');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cod_settings');
    }
};
