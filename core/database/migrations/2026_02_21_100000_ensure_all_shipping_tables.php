<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ensures all shipping-related tables exist so Zones, Methods, and Rules work.
 * Run: php artisan migrate
 */
return new class extends Migration
{
    public function up()
    {
        // 1. Shipping zones (required for zones, countries, areas, and methods FK)
        if (!Schema::hasTable('shipping_zones')) {
            Schema::create('shipping_zones', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('type', 20)->default('national');
                $table->unsignedTinyInteger('status')->default(1);
                $table->decimal('base_price', 18, 2)->default(0);
                $table->string('estimated_days', 50)->nullable();
                $table->timestamps();
            });
            Schema::table('shipping_zones', function (Blueprint $table) {
                $table->index(['status', 'type']);
            });
        }

        // 2. Shipping zone countries
        if (!Schema::hasTable('shipping_zone_countries')) {
            Schema::create('shipping_zone_countries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shipping_zone_id')->constrained('shipping_zones')->cascadeOnDelete();
                $table->string('country_iso', 5)->index();
                $table->string('country_name', 100)->nullable();
                $table->unsignedTinyInteger('status')->default(1);
                $table->timestamps();
            });
            Schema::table('shipping_zone_countries', function (Blueprint $table) {
                $table->unique(['shipping_zone_id', 'country_iso']);
            });
        }

        // 3. Shipping zone areas
        if (!Schema::hasTable('shipping_zone_areas')) {
            Schema::create('shipping_zone_areas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shipping_zone_id')->constrained('shipping_zones')->cascadeOnDelete();
                $table->string('area_name', 100);
                $table->json('district_names')->nullable();
                $table->unsignedTinyInteger('status')->default(1);
                $table->timestamps();
            });
            Schema::table('shipping_zone_areas', function (Blueprint $table) {
                $table->index('shipping_zone_id');
            });
        }

        // 4. Shipping rules
        if (!Schema::hasTable('shipping_rules')) {
            Schema::create('shipping_rules', function (Blueprint $table) {
                $table->id();
                $table->decimal('free_shipping_min_amount', 18, 2)->nullable();
                $table->decimal('cod_extra_charge', 18, 2)->default(0);
                $table->decimal('express_extra_charge', 18, 2)->default(0);
                $table->boolean('international_enabled')->default(true);
                $table->timestamps();
            });
        }

        // 5. Optional columns on shipping_methods (if table exists)
        if (Schema::hasTable('shipping_methods')) {
            Schema::table('shipping_methods', function (Blueprint $table) {
                if (!Schema::hasColumn('shipping_methods', 'shipping_zone_id')) {
                    $table->unsignedBigInteger('shipping_zone_id')->nullable()->after('id');
                    $table->foreign('shipping_zone_id')->references('id')->on('shipping_zones')->nullOnDelete();
                }
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
    }

    public function down()
    {
        if (Schema::hasTable('shipping_methods')) {
            Schema::table('shipping_methods', function (Blueprint $table) {
                if (Schema::hasColumn('shipping_methods', 'weight_limit_kg')) {
                    $table->dropColumn('weight_limit_kg');
                }
                if (Schema::hasColumn('shipping_methods', 'is_express')) {
                    $table->dropColumn('is_express');
                }
                if (Schema::hasColumn('shipping_methods', 'courier_name')) {
                    $table->dropColumn('courier_name');
                }
                if (Schema::hasColumn('shipping_methods', 'estimated_days')) {
                    $table->dropColumn('estimated_days');
                }
                if (Schema::hasColumn('shipping_methods', 'price_per_kg')) {
                    $table->dropColumn('price_per_kg');
                }
                if (Schema::hasColumn('shipping_methods', 'base_price')) {
                    $table->dropColumn('base_price');
                }
                if (Schema::hasColumn('shipping_methods', 'shipping_zone_id')) {
                    $table->dropForeign(['shipping_zone_id']);
                }
            });
        }
        Schema::dropIfExists('shipping_rules');
        Schema::dropIfExists('shipping_zone_areas');
        Schema::dropIfExists('shipping_zone_countries');
        Schema::dropIfExists('shipping_zones');
    }
};
