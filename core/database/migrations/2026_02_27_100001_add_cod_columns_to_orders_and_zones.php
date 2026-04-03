<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('shipping_zones') && !Schema::hasColumn('shipping_zones', 'cod_enabled')) {
            Schema::table('shipping_zones', function (Blueprint $table) {
                $table->boolean('cod_enabled')->default(true)->after('status');
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'cod_charge')) {
                    $table->decimal('cod_charge', 18, 2)->default(0)->after('shipping_charge');
                }
                if (!Schema::hasColumn('orders', 'cod_verified_at')) {
                    $table->timestamp('cod_verified_at')->nullable()->after('payment_type');
                }
            });
        }

        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'cod_disabled')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('cod_disabled')->default(false)->after('digital_item');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('shipping_zones') && Schema::hasColumn('shipping_zones', 'cod_enabled')) {
            Schema::table('shipping_zones', function (Blueprint $table) {
                $table->dropColumn('cod_enabled');
            });
        }
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'cod_charge')) $table->dropColumn('cod_charge');
                if (Schema::hasColumn('orders', 'cod_verified_at')) $table->dropColumn('cod_verified_at');
            });
        }
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'cod_disabled')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('cod_disabled');
            });
        }
    }
};
