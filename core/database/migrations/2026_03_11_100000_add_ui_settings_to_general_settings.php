<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Product card UI customization: frame color, button color, hover, rating star, discount badge.
     */
    public function up(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            $cols = [
                'product_card_color' => ['string', 30, '#ffffff'],
                'button_color' => ['string', 30, '#1f2937'],
                'button_hover_color' => ['string', 30, '#374151'],
                'rating_star_color' => ['string', 30, '#f59e0b'],
                'discount_badge_color' => ['string', 30, '#dc2626'],
            ];
            foreach ($cols as $col => $def) {
                if (!Schema::hasColumn('general_settings', $col)) {
                    $table->string($col, $def[1])->default($def[2])->nullable();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            $drop = ['product_card_color', 'button_color', 'button_hover_color', 'rating_star_color', 'discount_badge_color'];
            foreach ($drop as $col) {
                if (Schema::hasColumn('general_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
