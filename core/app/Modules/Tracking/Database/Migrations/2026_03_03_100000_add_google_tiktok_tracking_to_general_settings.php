<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'google_ads_id')) {
                $table->string('google_ads_id', 100)->nullable()->after('facebook_access_token')->comment('Google Ads / gtag ID');
            }
            if (!Schema::hasColumn('general_settings', 'tiktok_pixel_id')) {
                $table->string('tiktok_pixel_id', 100)->nullable()->after('google_ads_id')->comment('TikTok Pixel ID');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'google_ads_id')) {
                $table->dropColumn('google_ads_id');
            }
            if (Schema::hasColumn('general_settings', 'tiktok_pixel_id')) {
                $table->dropColumn('tiktok_pixel_id');
            }
        });
    }
};
