<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add is_approved (and related columns) to reviews if missing (e.g. after restore from old SQL dump).
     */
    public function up(): void
    {
        if (!Schema::hasTable('reviews')) {
            return;
        }

        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'is_approved')) {
                $table->boolean('is_approved')->default(true)->after('review_comment');
            }
            if (!Schema::hasColumn('reviews', 'title')) {
                $table->string('title', 255)->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('reviews', 'is_verified_purchase')) {
                $table->boolean('is_verified_purchase')->default(false)->after('review_comment');
            }
            if (!Schema::hasColumn('reviews', 'helpful_count')) {
                $table->unsignedInteger('helpful_count')->default(0)->after('is_approved');
            }
            if (!Schema::hasColumn('reviews', 'images')) {
                $table->json('images')->nullable()->after('helpful_count');
            }
            if (!Schema::hasColumn('reviews', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('images');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('reviews')) {
            return;
        }
        Schema::table('reviews', function (Blueprint $table) {
            $columns = ['is_approved', 'title', 'is_verified_purchase', 'helpful_count', 'images', 'is_featured'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('reviews', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
