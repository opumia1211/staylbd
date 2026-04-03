<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add index for COD filtering (payment_type = 2)
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'payment_type')) {
            try {
                Schema::table('orders', function (Blueprint $table) {
                    $table->index('payment_type');
                });
            } catch (\Throwable $e) {
                // Index may already exist
            }
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'cod_failed_count')) {
                    $table->unsignedSmallInteger('cod_failed_count')->default(0)->after('status');
                }
                if (!Schema::hasColumn('users', 'cod_disabled_until')) {
                    $table->timestamp('cod_disabled_until')->nullable()->after('cod_failed_count');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex(['payment_type']);
            });
        }
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'cod_failed_count')) $table->dropColumn('cod_failed_count');
                if (Schema::hasColumn('users', 'cod_disabled_until')) $table->dropColumn('cod_disabled_until');
            });
        }
    }
};
