<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            // Logo Effects Settings
            if (!Schema::hasColumn('general_settings', 'logo_effects_enabled')) {
                $table->tinyInteger('logo_effects_enabled')->default(0)->after('favicon');
            }
            if (!Schema::hasColumn('general_settings', 'logo_hover_effect')) {
                $table->string('logo_hover_effect', 50)->default('none')->after('logo_effects_enabled');
            }
            if (!Schema::hasColumn('general_settings', 'logo_animation')) {
                $table->string('logo_animation', 50)->default('none')->after('logo_hover_effect');
            }
            if (!Schema::hasColumn('general_settings', 'logo_animation_speed')) {
                $table->string('logo_animation_speed', 20)->default('normal')->after('logo_animation');
            }
            if (!Schema::hasColumn('general_settings', 'logo_opacity')) {
                $table->decimal('logo_opacity', 3, 2)->default(1.00)->after('logo_animation_speed');
            }
            
            // Logo Display Settings
            if (!Schema::hasColumn('general_settings', 'logo_max_width')) {
                $table->integer('logo_max_width')->default(200)->after('logo_opacity');
            }
            if (!Schema::hasColumn('general_settings', 'logo_max_height')) {
                $table->integer('logo_max_height')->default(60)->after('logo_max_width');
            }
            if (!Schema::hasColumn('general_settings', 'footer_logo_height')) {
                $table->integer('footer_logo_height')->default(35)->after('logo_max_height');
            }
        });
    }

    public function down()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $columns = [
                'logo_effects_enabled', 
                'logo_hover_effect', 
                'logo_animation', 
                'logo_animation_speed',
                'logo_opacity',
                'logo_max_width',
                'logo_max_height',
                'footer_logo_height'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('general_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
