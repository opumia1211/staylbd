<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds fields for: general product upload, clothing-specific, SEO, shipping, import source.
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'slug')) {
                $table->string('slug', 255)->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('products', 'original_price')) {
                $table->decimal('original_price', 28, 8)->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'profit_margin')) {
                $table->decimal('profit_margin', 10, 2)->nullable()->after('discount_type')->comment('Percentage or fixed margin');
            }
            if (!Schema::hasColumn('products', 'low_stock_alert')) {
                $table->unsignedInteger('low_stock_alert')->nullable()->after('quantity')->comment('Alert when stock falls below this');
            }
            if (!Schema::hasColumn('products', 'warehouse_location')) {
                $table->string('warehouse_location', 255)->nullable()->after('low_stock_alert');
            }
            if (!Schema::hasColumn('products', 'shipping_weight')) {
                $table->decimal('shipping_weight', 10, 2)->nullable()->after('warehouse_location')->comment('kg');
            }
            if (!Schema::hasColumn('products', 'shipping_class')) {
                $table->string('shipping_class', 100)->nullable()->after('shipping_weight');
            }
            if (!Schema::hasColumn('products', 'delivery_time')) {
                $table->string('delivery_time', 100)->nullable()->after('shipping_class')->comment('e.g. 2-5 days');
            }
            if (!Schema::hasColumn('products', 'meta_title')) {
                $table->string('meta_title', 255)->nullable()->after('description');
            }
            if (!Schema::hasColumn('products', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('products', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description')->comment('JSON array or comma-separated');
            }
            if (!Schema::hasColumn('products', 'product_type')) {
                $table->string('product_type', 30)->nullable()->default('general')->after('status')->comment('clothing, general');
            }
            if (!Schema::hasColumn('products', 'fabric_type')) {
                $table->string('fabric_type', 100)->nullable()->after('product_type');
            }
            if (!Schema::hasColumn('products', 'material')) {
                $table->string('material', 255)->nullable()->after('fabric_type');
            }
            if (!Schema::hasColumn('products', 'season')) {
                $table->string('season', 50)->nullable()->after('material')->comment('spring, summer, fall, winter, all');
            }
            if (!Schema::hasColumn('products', 'color_variants')) {
                $table->text('color_variants')->nullable()->after('season')->comment('JSON array of color names');
            }
            if (!Schema::hasColumn('products', 'source_url')) {
                $table->string('source_url', 500)->nullable()->after('color_variants')->comment('Import source URL');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $cols = [
                'slug', 'original_price', 'profit_margin', 'low_stock_alert', 'warehouse_location',
                'shipping_weight', 'shipping_class', 'delivery_time', 'meta_title', 'meta_description', 'meta_keywords',
                'product_type', 'fabric_type', 'material', 'season', 'color_variants', 'source_url'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('products', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
