<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('shipping_methods', function (Blueprint $table) {
            if (!Schema::hasColumn('shipping_methods', 'shipping_zone_id')) {
                $table->foreignId('shipping_zone_id')->nullable()->after('id')->constrained('shipping_zones')->nullOnDelete();
            }
        });
        Schema::table('shipping_methods', function (Blueprint $table) {
            if (!Schema::hasColumn('shipping_methods', 'base_price')) {
                $table->decimal('base_price', 18, 2)->default(0);
            }
            if (!Schema::hasColumn('shipping_methods', 'price_per_kg')) {
                $table->decimal('price_per_kg', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('shipping_methods', 'estimated_days')) {
                $table->string('estimated_days', 50)->nullable();
            }
            if (!Schema::hasColumn('shipping_methods', 'courier_name')) {
                $table->string('courier_name', 100)->nullable();
            }
            if (!Schema::hasColumn('shipping_methods', 'is_express')) {
                $table->boolean('is_express')->default(false);
            }
            if (!Schema::hasColumn('shipping_methods', 'weight_limit_kg')) {
                $table->decimal('weight_limit_kg', 10, 2)->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('shipping_methods', function (Blueprint $table) {
            if (Schema::hasColumn('shipping_methods', 'weight_limit_kg')) $table->dropColumn('weight_limit_kg');
            if (Schema::hasColumn('shipping_methods', 'is_express')) $table->dropColumn('is_express');
            if (Schema::hasColumn('shipping_methods', 'courier_name')) $table->dropColumn('courier_name');
            if (Schema::hasColumn('shipping_methods', 'estimated_days')) $table->dropColumn('estimated_days');
            if (Schema::hasColumn('shipping_methods', 'price_per_kg')) $table->dropColumn('price_per_kg');
            if (Schema::hasColumn('shipping_methods', 'base_price')) $table->dropColumn('base_price');
            if (Schema::hasColumn('shipping_methods', 'shipping_zone_id')) $table->dropForeign(['shipping_zone_id']);
        });
    }
};
