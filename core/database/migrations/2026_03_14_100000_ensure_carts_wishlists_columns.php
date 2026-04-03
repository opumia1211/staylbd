<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ensure carts and wishlists tables have all required columns for persistent storage.
 * Fixes: cart/wishlist disappearing on refresh when data was only in session or DB structure was incomplete.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('carts')) {
            Schema::table('carts', function (Blueprint $table) {
                if (!Schema::hasColumn('carts', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id')->index();
                }
                if (!Schema::hasColumn('carts', 'product_id')) {
                    $table->unsignedBigInteger('product_id')->nullable()->after('user_id')->index();
                }
                if (!Schema::hasColumn('carts', 'quantity')) {
                    $table->unsignedInteger('quantity')->default(1);
                }
            });
        }

        if (Schema::hasTable('wishlists')) {
            Schema::table('wishlists', function (Blueprint $table) {
                if (!Schema::hasColumn('wishlists', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id')->index();
                }
                if (!Schema::hasColumn('wishlists', 'product_id')) {
                    $table->unsignedBigInteger('product_id')->nullable()->after('user_id')->index();
                }
            });
        }
    }

    public function down(): void
    {
        // Optional: only drop columns we added; leave existing structure intact
    }
};
