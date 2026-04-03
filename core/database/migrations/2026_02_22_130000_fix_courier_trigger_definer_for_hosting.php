<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Recreate update_courier_statistics trigger without DEFINER
 * so it works with the hosting DB user (avoids DEFINER=root@localhost on import).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('courier_logs') || !Schema::hasTable('courier_statistics')) {
            return;
        }

        DB::statement('DROP TRIGGER IF EXISTS `update_courier_statistics`');

        DB::statement("
            CREATE TRIGGER `update_courier_statistics`
            AFTER INSERT ON `courier_logs`
            FOR EACH ROW
            INSERT INTO courier_statistics (courier_type, date, total_orders, successful_orders, failed_orders)
            VALUES (
                NEW.courier_type,
                DATE(NEW.created_at),
                1,
                CASE WHEN NEW.status = 'success' THEN 1 ELSE 0 END,
                CASE WHEN NEW.status = 'failed' THEN 1 ELSE 0 END
            )
            ON DUPLICATE KEY UPDATE
                total_orders = total_orders + 1,
                successful_orders = successful_orders + (CASE WHEN NEW.status = 'success' THEN 1 ELSE 0 END),
                failed_orders = failed_orders + (CASE WHEN NEW.status = 'failed' THEN 1 ELSE 0 END)
        ");
    }

    public function down(): void
    {
        if (!Schema::hasTable('courier_logs')) {
            return;
        }
        DB::statement('DROP TRIGGER IF EXISTS `update_courier_statistics`');
    }
};
