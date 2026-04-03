<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('orders')) {
            return;
        }
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivery_scan_token')) {
                $table->string('delivery_scan_token', 64)->nullable()->unique()->after('device_lng');
            }
            if (!Schema::hasColumn('orders', 'delivery_scanned_at')) {
                $table->timestamp('delivery_scanned_at')->nullable()->after('delivery_scan_token');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_scan_token', 'delivery_scanned_at']);
        });
    }
};
