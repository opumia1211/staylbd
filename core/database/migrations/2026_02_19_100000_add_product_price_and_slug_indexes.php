<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * High-performance indexes for 100k+ products: price (filter/sort), slug (lookup if column exists).
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'price') && !$this->indexExists('products', 'products_price_index')) {
                $table->index('price');
            }
            if (Schema::hasColumn('products', 'slug') && !$this->indexExists('products', 'products_slug_index')) {
                $table->index('slug');
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'price')) {
                $table->dropIndex(['price']);
            }
            if (Schema::hasColumn('products', 'slug')) {
                $table->dropIndex(['slug']);
            }
        });
    }

    protected function indexExists($table, $name)
    {
        try {
            $indexes = \DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$name]);
            return count($indexes) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
};
