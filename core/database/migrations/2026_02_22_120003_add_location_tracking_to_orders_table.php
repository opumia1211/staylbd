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
            if (!Schema::hasColumn('orders', 'device_lat')) {
                $table->decimal('device_lat', 10, 7)->nullable()->after('address');
            }
            if (!Schema::hasColumn('orders', 'device_lng')) {
                $table->decimal('device_lng', 10, 7)->nullable()->after('device_lat');
            }
            if (!Schema::hasColumn('orders', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('device_lng');
            }
            if (!Schema::hasColumn('orders', 'location_risk_score')) {
                $table->unsignedTinyInteger('location_risk_score')->nullable()->after('ip_address');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('orders')) {
            return;
        }
        $cols = [];
        if (Schema::hasColumn('orders', 'device_lat')) $cols[] = 'device_lat';
        if (Schema::hasColumn('orders', 'device_lng')) $cols[] = 'device_lng';
        if (Schema::hasColumn('orders', 'ip_address')) $cols[] = 'ip_address';
        if (Schema::hasColumn('orders', 'location_risk_score')) $cols[] = 'location_risk_score';
        if (!empty($cols)) {
            Schema::table('orders', function (Blueprint $table) use ($cols) {
                $table->dropColumn($cols);
            });
        }
    }
};
