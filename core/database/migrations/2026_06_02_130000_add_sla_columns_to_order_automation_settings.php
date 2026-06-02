<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('order_automation_settings')) {
            return;
        }

        Schema::table('order_automation_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('order_automation_settings', 'sla_pending_hours')) {
                $table->unsignedSmallInteger('sla_pending_hours')->default(24)->after('run_interval_minutes');
            }
            if (!Schema::hasColumn('order_automation_settings', 'sla_fulfillment_hours')) {
                $table->unsignedSmallInteger('sla_fulfillment_hours')->default(48)->after('sla_pending_hours');
            }
            if (!Schema::hasColumn('order_automation_settings', 'sla_alerts_enabled')) {
                $table->boolean('sla_alerts_enabled')->default(true)->after('sla_fulfillment_hours');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('order_automation_settings')) {
            return;
        }

        Schema::table('order_automation_settings', function (Blueprint $table) {
            foreach (['sla_pending_hours', 'sla_fulfillment_hours', 'sla_alerts_enabled'] as $col) {
                if (Schema::hasColumn('order_automation_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
