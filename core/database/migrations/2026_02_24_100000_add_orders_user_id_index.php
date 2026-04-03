<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add index on orders.user_id for faster "my orders" and reporting queries.
 * Safe: only adds index if column exists and index does not already exist.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders') || !Schema::hasColumn('orders', 'user_id')) {
            return;
        }

        $indexName = 'orders_user_id_index';
        if ($this->indexExists('orders', $indexName)) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }
        if (!$this->indexExists('orders', 'orders_user_id_index')) {
            return;
        }
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
    }

    protected function indexExists(string $table, string $name): bool
    {
        try {
            $indexes = \DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$name]);
            return count($indexes) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
};
