<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Admin UI Theme Control: product card, button, header, footer, rating, discount colors + theme template.
     */
    public function up(): void
    {
        Schema::create('ui_settings', function (Blueprint $table) {
            $table->id();
            $table->string('product_card_bg', 30)->default('#ffffff');
            $table->string('product_button_color', 30)->default('#1f2937');
            $table->string('product_buy_now_color', 30)->default('#0e9f90');
            $table->string('product_buy_now_hover', 30)->default('#0c8a7d');
            $table->string('header_bg', 30)->nullable();
            $table->string('footer_bg', 30)->nullable();
            $table->string('rating_color', 30)->default('#f59e0b');
            $table->string('discount_badge_color', 30)->default('#dc2626');
            $table->string('theme_template', 50)->default('default');
            $table->timestamps();
        });
        \DB::table('ui_settings')->insert([
            'product_card_bg' => '#ffffff',
            'product_button_color' => '#1f2937',
            'product_buy_now_color' => '#0e9f90',
            'product_buy_now_hover' => '#0c8a7d',
            'header_bg' => null,
            'footer_bg' => null,
            'rating_color' => '#f59e0b',
            'discount_badge_color' => '#dc2626',
            'theme_template' => 'default',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ui_settings');
    }
};
