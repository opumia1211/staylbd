<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('reviews', 'product_id')) {
                $table->unsignedBigInteger('product_id')->after('id');
            }
            if (!Schema::hasColumn('reviews', 'stars')) {
                $table->unsignedInteger('stars')->default(5)->after('product_id');
            }
            if (!Schema::hasColumn('reviews', 'review_comment')) {
                $table->text('review_comment')->nullable()->after('stars');
            }

            // Ensure foreign keys if they don't exist
            // Note: In some environments, foreign keys might fail if types don't match exactly.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'product_id', 'stars', 'review_comment']);
        });
    }
};
