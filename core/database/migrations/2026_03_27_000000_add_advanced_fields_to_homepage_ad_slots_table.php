<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Avoid doctrine/dbal dependency for column modification.
        DB::statement("ALTER TABLE `homepage_ad_slots` MODIFY `image` VARCHAR(512) NULL");

        Schema::table('homepage_ad_slots', function (Blueprint $table) {
            $table->text('external_url')->nullable()->after('image');
            $table->string('position', 32)->default('custom')->after('width_mode');
            $table->string('side', 32)->nullable()->after('position');
            $table->integer('top')->nullable()->after('side');
            $table->integer('bottom')->nullable()->after('top');
            $table->integer('left')->nullable()->after('bottom');
            $table->integer('right')->nullable()->after('left');
            $table->string('size_type', 16)->default('auto')->after('max_height_px');
            $table->string('custom_width', 16)->nullable()->after('size_type');
            $table->string('custom_height', 16)->nullable()->after('custom_width');
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `homepage_ad_slots` MODIFY `image` VARCHAR(512) NOT NULL");

        Schema::table('homepage_ad_slots', function (Blueprint $table) {
            $table->dropColumn([
                'external_url',
                'position',
                'side',
                'top',
                'bottom',
                'left',
                'right',
                'size_type',
                'custom_width',
                'custom_height'
            ]);
        });
    }
};
