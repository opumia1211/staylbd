<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('shipping_rules')) {
            return;
        }
        Schema::create('shipping_rules', function (Blueprint $table) {
            $table->id();
            $table->decimal('free_shipping_min_amount', 18, 2)->nullable();
            $table->decimal('cod_extra_charge', 18, 2)->default(0);
            $table->decimal('express_extra_charge', 18, 2)->default(0);
            $table->boolean('international_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_rules');
    }
};
