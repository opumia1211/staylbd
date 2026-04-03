<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notification_logs')) {
            return;
        }
        Schema::table('notification_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('notification_logs', 'click_url')) {
                $table->string('click_url', 500)->nullable()->after('message');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('notification_logs') && Schema::hasColumn('notification_logs', 'click_url')) {
            Schema::table('notification_logs', function (Blueprint $table) {
                $table->dropColumn('click_url');
            });
        }
    }
};
