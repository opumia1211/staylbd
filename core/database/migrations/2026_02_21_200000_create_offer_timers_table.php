<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Offer timers: discount/special offer countdown bars controllable from admin.
     * Show on: home, cart, checkout, product detail, category; by position and target products/categories.
     */
    public function up(): void
    {
        Schema::create('offer_timers', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('subtitle', 500)->nullable();
            $table->dateTime('end_at');
            $table->string('style', 30)->default('bar_large'); // bar_small, bar_large, full_width
            $table->string('position', 30)->default('cart_top'); // header, below_header, cart_top, checkout_top, product_detail, floating
            $table->json('show_on_pages')->nullable(); // ["home","cart","checkout","product_detail","category"] or ["all"]
            $table->json('product_ids')->nullable(); // [1,2,3] or null = all
            $table->json('category_ids')->nullable(); // [1,2] or null = all
            $table->string('link_url', 500)->nullable();
            $table->unsignedTinyInteger('is_active')->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_timers');
    }
};
