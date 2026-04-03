<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure each product is in at most one spotlight (today_deals > hot_deals > featured_product).
     * Run once to fix existing data; admin toggles already enforce this for new changes.
     */
    public function up(): void
    {
        $table = 'products';
        if (!Schema::hasTable($table) || !Schema::hasColumns($table, ['featured_product', 'hot_deals', 'today_deals'])) {
            return;
        }

        // Products with today_deals = 1: keep only today_deals, clear the other two
        DB::table($table)
            ->where('today_deals', 1)
            ->update(['featured_product' => 0, 'hot_deals' => 0]);

        // Products with hot_deals = 1 (and not today deal): clear featured
        DB::table($table)
            ->where('hot_deals', 1)
            ->update(['featured_product' => 0]);

        // Products with featured_product = 1 and (hot_deals or today_deals): already cleared above
    }

    public function down(): void
    {
        // No reversible change; data was normalized.
    }
};
