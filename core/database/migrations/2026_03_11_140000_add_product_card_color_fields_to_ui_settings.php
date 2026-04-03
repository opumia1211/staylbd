<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ui_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('ui_settings', 'product_price_color')) {
                $table->string('product_price_color', 30)->nullable()->after('product_buy_now_hover');
            }
            if (!Schema::hasColumn('ui_settings', 'stock_color')) {
                $table->string('stock_color', 30)->nullable()->after('discount_badge_color');
            }
            if (!Schema::hasColumn('ui_settings', 'shipping_badge_color')) {
                $table->string('shipping_badge_color', 30)->nullable()->after('stock_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ui_settings', function (Blueprint $table) {
            if (Schema::hasColumn('ui_settings', 'product_price_color')) {
                $table->dropColumn('product_price_color');
            }
            if (Schema::hasColumn('ui_settings', 'stock_color')) {
                $table->dropColumn('stock_color');
            }
            if (Schema::hasColumn('ui_settings', 'shipping_badge_color')) {
                $table->dropColumn('shipping_badge_color');
            }
        });
    }
};
