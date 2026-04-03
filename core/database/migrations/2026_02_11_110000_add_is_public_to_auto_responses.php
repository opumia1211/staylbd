<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('auto_responses')) {
            return;
        }
        Schema::table('auto_responses', function (Blueprint $table) {
            if (!Schema::hasColumn('auto_responses', 'is_public')) {
                $table->boolean('is_public')->default(true)->after('is_active')->comment('Public = sent to users on keyword match; Private = not sent');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('auto_responses') && Schema::hasColumn('auto_responses', 'is_public')) {
            Schema::table('auto_responses', function (Blueprint $table) {
                $table->dropColumn('is_public');
            });
        }
    }
};
