<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add indexes on reviews for faster product/user lookups.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reviews')) {
            return;
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('reviews')) {
            return;
        }
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
        });
    }
};
