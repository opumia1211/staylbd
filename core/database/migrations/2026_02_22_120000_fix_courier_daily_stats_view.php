<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix courier_daily_stats view: drop and recreate without DEFINER
 * so it works with the app's DB user (avoids DEFINER=root`@`localhost syntax/privilege issues).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('courier_logs')) {
            return;
        }

        DB::statement('DROP VIEW IF EXISTS `courier_daily_stats`');

        DB::statement("
            CREATE VIEW courier_daily_stats AS
            SELECT
                courier_logs.courier_type AS courier_type,
                CAST(courier_logs.created_at AS DATE) AS date,
                COUNT(0) AS total_orders,
                SUM(CASE WHEN courier_logs.status = 'success' THEN 1 ELSE 0 END) AS successful_orders,
                SUM(CASE WHEN courier_logs.status = 'failed' THEN 1 ELSE 0 END) AS failed_orders
            FROM courier_logs
            GROUP BY courier_logs.courier_type, CAST(courier_logs.created_at AS DATE)
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS `courier_daily_stats`');
    }
};
