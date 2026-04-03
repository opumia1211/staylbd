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
        if (Schema::hasColumn('notification_logs', 'read_at')) {
            return;
        }
        $afterClickUrl = Schema::hasColumn('notification_logs', 'click_url');
        Schema::table('notification_logs', function (Blueprint $table) use ($afterClickUrl) {
            if ($afterClickUrl) {
                $table->timestamp('read_at')->nullable()->after('click_url');
            } else {
                $table->timestamp('read_at')->nullable()->after('message');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('notification_logs') || !Schema::hasColumn('notification_logs', 'read_at')) {
            return;
        }
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->dropColumn('read_at');
        });
    }
};
