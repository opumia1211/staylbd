<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('homepage_ad_slots')) {
            return;
        }

        Schema::table('homepage_ad_slots', function (Blueprint $table) {
            if (!Schema::hasColumn('homepage_ad_slots', 'source_type')) {
                $table->string('source_type', 24)->default('upload')->after('image');
            }
            if (!Schema::hasColumn('homepage_ad_slots', 'display_pages')) {
                $table->string('display_pages', 24)->default('all')->after('position');
            }
            if (!Schema::hasColumn('homepage_ad_slots', 'custom_path')) {
                $table->string('custom_path', 255)->nullable()->after('display_pages');
            }
            if (!Schema::hasColumn('homepage_ad_slots', 'z_index')) {
                $table->integer('z_index')->default(1100)->after('custom_height');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('homepage_ad_slots')) {
            return;
        }

        Schema::table('homepage_ad_slots', function (Blueprint $table) {
            $drops = [];
            foreach (['source_type', 'display_pages', 'custom_path', 'z_index'] as $col) {
                if (Schema::hasColumn('homepage_ad_slots', $col)) {
                    $drops[] = $col;
                }
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
