<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'ad_source')) {
                $table->string('ad_source', 100)->nullable()->after('ip_address')->comment('facebook, google, tiktok');
            }
            if (!Schema::hasColumn('orders', 'utm_source')) {
                $table->string('utm_source', 200)->nullable()->after('ad_source');
            }
            if (!Schema::hasColumn('orders', 'utm_medium')) {
                $table->string('utm_medium', 200)->nullable()->after('utm_source');
            }
            if (!Schema::hasColumn('orders', 'utm_campaign')) {
                $table->string('utm_campaign', 200)->nullable()->after('utm_medium');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }
        Schema::table('orders', function (Blueprint $table) {
            $cols = ['ad_source', 'utm_source', 'utm_medium', 'utm_campaign'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
