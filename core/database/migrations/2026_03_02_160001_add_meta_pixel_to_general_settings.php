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
            if (!Schema::hasColumn('general_settings', 'meta_pixel_id')) {
                $table->string('meta_pixel_id', 100)->nullable()->comment('Meta/Facebook Pixel ID');
            }
            if (!Schema::hasColumn('general_settings', 'facebook_access_token')) {
                $table->string('facebook_access_token', 500)->nullable()->comment('Facebook Access Token');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'meta_pixel_id')) {
                $table->dropColumn('meta_pixel_id');
            }
            if (Schema::hasColumn('general_settings', 'facebook_access_token')) {
                $table->dropColumn('facebook_access_token');
            }
        });
    }
};
