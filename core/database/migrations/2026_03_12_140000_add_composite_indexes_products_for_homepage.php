<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Composite indexes for ultra-fast homepage section queries.
 * Covers: hot_deal, featured, new_arrivals (created_at), trending_now, best_selling (sale_count).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        $addIndex = function (array $cols, string $name) {
            try {
                Schema::table('products', function (Blueprint $table) use ($cols, $name) {
                    $table->index($cols, $name);
                });
            } catch (\Throwable $e) {
                // index may already exist
            }
        };

        if (Schema::hasColumn('products', 'status') && Schema::hasColumn('products', 'hot_deals')) {
            $addIndex(['status', 'hot_deals'], 'products_status_hot_deals_index');
        }
        if (Schema::hasColumn('products', 'status') && Schema::hasColumn('products', 'featured_product')) {
            $addIndex(['status', 'featured_product'], 'products_status_featured_index');
        }
        if (Schema::hasColumn('products', 'status') && Schema::hasColumn('products', 'created_at')) {
            $addIndex(['status', 'created_at'], 'products_status_created_at_index');
        }
        if (Schema::hasColumn('products', 'status') && Schema::hasColumn('products', 'sale_count')) {
            $addIndex(['status', 'sale_count'], 'products_status_sale_count_index');
        }
        if (Schema::hasColumn('products', 'status') && Schema::hasColumn('products', 'trending_now')) {
            $addIndex(['status', 'trending_now'], 'products_status_trending_now_index');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }
        Schema::table('products', function (Blueprint $table) {
            $drops = [
                'products_status_hot_deals_index',
                'products_status_featured_index',
                'products_status_created_at_index',
                'products_status_sale_count_index',
                'products_status_trending_now_index',
            ];
            foreach ($drops as $name) {
                try {
                    $table->dropIndex($name);
                } catch (\Throwable $e) {
                    // index may not exist
                }
            }
        });
    }
};
