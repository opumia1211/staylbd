<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ui_settings')) {
            return;
        }

        Schema::table('ui_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('ui_settings', 'header_top_bg')) {
                $table->string('header_top_bg', 30)->nullable()->after('shipping_badge_color');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('ui_settings')) {
            return;
        }

        Schema::table('ui_settings', function (Blueprint $table) {
            if (Schema::hasColumn('ui_settings', 'header_top_bg')) {
                $table->dropColumn('header_top_bg');
            }
        });
    }
};
