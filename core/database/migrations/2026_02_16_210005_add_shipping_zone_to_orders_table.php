<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_zone_id')) {
                $table->unsignedBigInteger('shipping_zone_id')->nullable()->after('shipping_method_id');
                $table->foreign('shipping_zone_id')->references('id')->on('shipping_zones')->nullOnDelete();
            }
        });
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivery_estimate')) {
                $table->string('delivery_estimate', 100)->nullable();
            }
            if (!Schema::hasColumn('orders', 'courier_name')) {
                $table->string('courier_name', 100)->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'courier_name')) $table->dropColumn('courier_name');
            if (Schema::hasColumn('orders', 'delivery_estimate')) $table->dropColumn('delivery_estimate');
            if (Schema::hasColumn('orders', 'shipping_zone_id')) $table->dropForeign(['shipping_zone_id']);
        });
    }
};
