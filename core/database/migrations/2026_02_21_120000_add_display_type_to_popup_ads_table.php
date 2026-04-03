<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('popup_ads', function (Blueprint $table) {
            $table->string('display_type', 20)->default('popup')->after('position')->comment('popup=modal with close, inline=stays on page');
            $table->string('inline_placement', 50)->nullable()->after('display_type')->comment('sidebar_right, sidebar_left, content_top, content_bottom');
        });
    }

    public function down(): void
    {
        Schema::table('popup_ads', function (Blueprint $table) {
            $table->dropColumn(['display_type', 'inline_placement']);
        });
    }
};
