<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds indexes for 30k-50k product management - faster admin/frontend queries.
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $cols = ['category_id', 'brand_id', 'status', 'subcategory_id', 'created_at', 'quantity'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('products', $col) && !$this->indexExists('products', 'products_' . $col . '_index')) {
                    try {
                        $table->index($col);
                    } catch (\Throwable $e) {
                        // Skip if already exists or other error
                    }
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            foreach (['category_id', 'brand_id', 'status', 'subcategory_id', 'created_at', 'quantity'] as $col) {
                try {
                    $table->dropIndex([$col]);
                } catch (\Throwable $e) {
                    // Index may not exist
                }
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
