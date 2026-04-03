<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure E-commerce & Ads Tracking columns exist on general_settings
     * so General Settings page can show Meta/Google/TikTok fields.
     */
    public function up(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'meta_pixel_id')) {
                $table->string('meta_pixel_id', 100)->nullable()->comment('Meta/Facebook Pixel ID');
            }
            if (!Schema::hasColumn('general_settings', 'facebook_access_token')) {
                $table->string('facebook_access_token', 500)->nullable()->comment('Facebook Access Token for CAPI');
            }
            if (!Schema::hasColumn('general_settings', 'google_ads_id')) {
                $table->string('google_ads_id', 100)->nullable()->comment('Google Ads / gtag ID');
            }
            if (!Schema::hasColumn('general_settings', 'tiktok_pixel_id')) {
                $table->string('tiktok_pixel_id', 100)->nullable()->comment('TikTok Pixel ID');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            $cols = ['meta_pixel_id', 'facebook_access_token', 'google_ads_id', 'tiktok_pixel_id'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('general_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
