<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reviews')) {
            return;
        }
        if (Schema::hasColumn('reviews', 'is_private')) {
            return;
        }
        Schema::table('reviews', function (Blueprint $table) {
            $table->boolean('is_private')->default(false)->after('is_featured');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('reviews') || !Schema::hasColumn('reviews', 'is_private')) {
            return;
        }
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('is_private');
        });
    }
};
