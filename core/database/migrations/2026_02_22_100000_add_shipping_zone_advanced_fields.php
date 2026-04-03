<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('shipping_zones') && !Schema::hasColumn('shipping_zones', 'free_shipping')) {
            Schema::table('shipping_zones', function (Blueprint $table) {
                $table->boolean('free_shipping')->default(false)->after('estimated_days');
            });
        }

        if (Schema::hasTable('shipping_zone_countries') && !Schema::hasColumn('shipping_zone_countries', 'shipping_price')) {
            Schema::table('shipping_zone_countries', function (Blueprint $table) {
                $table->decimal('shipping_price', 18, 2)->nullable()->after('country_name');
            });
        }

        if (Schema::hasTable('shipping_zone_areas')) {
            if (!Schema::hasColumn('shipping_zone_areas', 'shipping_price')) {
                Schema::table('shipping_zone_areas', function (Blueprint $table) {
                    $table->decimal('shipping_price', 18, 2)->nullable()->after('district_names');
                });
            }
            if (!Schema::hasColumn('shipping_zone_areas', 'free_shipping')) {
                Schema::table('shipping_zone_areas', function (Blueprint $table) {
                    $table->boolean('free_shipping')->default(false)->after('shipping_price');
                });
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('shipping_zones') && Schema::hasColumn('shipping_zones', 'free_shipping')) {
            Schema::table('shipping_zones', fn (Blueprint $t) => $t->dropColumn('free_shipping'));
        }
        if (Schema::hasTable('shipping_zone_countries') && Schema::hasColumn('shipping_zone_countries', 'shipping_price')) {
            Schema::table('shipping_zone_countries', fn (Blueprint $t) => $t->dropColumn('shipping_price'));
        }
        if (Schema::hasTable('shipping_zone_areas')) {
            if (Schema::hasColumn('shipping_zone_areas', 'free_shipping')) {
                Schema::table('shipping_zone_areas', fn (Blueprint $t) => $t->dropColumn('free_shipping'));
            }
            if (Schema::hasColumn('shipping_zone_areas', 'shipping_price')) {
                Schema::table('shipping_zone_areas', fn (Blueprint $t) => $t->dropColumn('shipping_price'));
            }
        }
    }
};
