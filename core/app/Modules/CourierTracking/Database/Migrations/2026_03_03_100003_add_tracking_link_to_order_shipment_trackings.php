<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('order_shipment_trackings')) {
            return;
        }
        if (!Schema::hasColumn('order_shipment_trackings', 'tracking_link')) {
            Schema::table('order_shipment_trackings', function (Blueprint $table) {
                $table->string('tracking_link', 500)->nullable()->after('courier_name')->comment('URL to track on courier website');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('order_shipment_trackings') && Schema::hasColumn('order_shipment_trackings', 'tracking_link')) {
            Schema::table('order_shipment_trackings', function (Blueprint $table) {
                $table->dropColumn('tracking_link');
            });
        }
    }
};
