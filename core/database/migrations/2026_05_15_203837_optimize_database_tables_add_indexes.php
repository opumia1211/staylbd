<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('frontends') && Schema::hasColumn('frontends', 'data_keys')) {
            if (!$this->indexExists('frontends', 'frontends_data_keys_index')) {
                Schema::table('frontends', function (Blueprint $table) {
                    $table->index('data_keys');
                });
            }
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'order_status') && !$this->indexExists('orders', 'orders_order_status_index')) {
                    $table->index('order_status');
                }
                if (Schema::hasColumn('orders', 'payment_status') && !$this->indexExists('orders', 'orders_payment_status_index')) {
                    $table->index('payment_status');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('frontends') && $this->indexExists('frontends', 'frontends_data_keys_index')) {
            Schema::table('frontends', function (Blueprint $table) {
                $table->dropIndex(['data_keys']);
            });
        }

        if (Schema::hasTable('orders')) {
            if ($this->indexExists('orders', 'orders_order_status_index')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->dropIndex(['order_status']);
                });
            }
            if ($this->indexExists('orders', 'orders_payment_status_index')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->dropIndex(['payment_status']);
                });
            }
        }
    }

    protected function indexExists(string $table, string $name): bool
    {
        try {
            $indexes = DB::select('SHOW INDEX FROM `' . $table . '` WHERE Key_name = ?', [$name]);

            return count($indexes) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
};
