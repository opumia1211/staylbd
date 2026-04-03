<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add lightweight indexes to speed up homepage product sections.
     */
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            // created_at index – new arrivals / global sorts
            if (!self::hasIndex('products', 'products_created_at_index')) {
                $table->index('created_at', 'products_created_at_index');
            }
            // category filter
            if (!self::hasIndex('products', 'products_category_id_index')) {
                $table->index('category_id', 'products_category_id_index');
            }
            // featured / hot / today_deals scopes
            if (Schema::hasColumn('products', 'featured_product') && !self::hasIndex('products', 'products_featured_product_index')) {
                $table->index('featured_product', 'products_featured_product_index');
            }
            if (Schema::hasColumn('products', 'hot_deals') && !self::hasIndex('products', 'products_hot_deals_index')) {
                $table->index('hot_deals', 'products_hot_deals_index');
            }
            if (Schema::hasColumn('products', 'today_deals') && !self::hasIndex('products', 'products_today_deals_index')) {
                $table->index('today_deals', 'products_today_deals_index');
            }
            if (Schema::hasColumn('products', 'trending_now') && !self::hasIndex('products', 'products_trending_now_index')) {
                $table->index('trending_now', 'products_trending_now_index');
            }
            // sale_count – best selling, recommended
            if (Schema::hasColumn('products', 'sale_count') && !self::hasIndex('products', 'products_sale_count_index')) {
                $table->index('sale_count', 'products_sale_count_index');
            }
            // status – active/available scope
            if (Schema::hasColumn('products', 'status') && !self::hasIndex('products', 'products_status_index')) {
                $table->index('status', 'products_status_index');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $indexes = [
                'products_created_at_index',
                'products_category_id_index',
                'products_featured_product_index',
                'products_hot_deals_index',
                'products_today_deals_index',
                'products_trending_now_index',
                'products_sale_count_index',
                'products_status_index',
            ];

            foreach ($indexes as $index) {
                if (self::hasIndex('products', $index)) {
                    $table->dropIndex($index);
                }
            }
        });
    }

    private static function hasIndex(string $table, string $indexName): bool
    {
        try {
            $dbName = DB::getDatabaseName();
            if (!$dbName) {
                return false;
            }
            $rows = DB::select(
                'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
                [$dbName, $table, $indexName]
            );
            return !empty($rows);
        } catch (\Throwable $e) {
            return false;
        }
    }
};

