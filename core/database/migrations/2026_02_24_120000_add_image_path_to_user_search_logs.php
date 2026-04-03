<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_search_logs')) {
            return;
        }
        if (!Schema::hasColumn('user_search_logs', 'image_path')) {
            Schema::table('user_search_logs', function (Blueprint $table) {
                $table->string('image_path', 500)->nullable()->after('source')->comment('Image search uploaded file path (WebP)');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_search_logs') && Schema::hasColumn('user_search_logs', 'image_path')) {
            Schema::table('user_search_logs', function (Blueprint $table) {
                $table->dropColumn('image_path');
            });
        }
    }
};
