<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add indexes on products for faster homepage and listing queries.
 * category_id: category/listing filters; created_at: latest() / new arrivals.
 * slug is typically already unique/indexed via earlier migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }
        if (Schema::hasColumn('products', 'category_id')) {
            try {
                Schema::table('products', fn (Blueprint $t) => $t->index('category_id'));
            } catch (\Throwable $e) {
                // index may already exist
            }
        }
        try {
            Schema::table('products', fn (Blueprint $t) => $t->index('created_at'));
        } catch (\Throwable $e) {
            // index may already exist
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }
        try {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex(['category_id']);
            });
        } catch (\Throwable $e) {
            // index may not exist
        }
        try {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex(['created_at']);
            });
        } catch (\Throwable $e) {
            // index may not exist
        }
    }
};
