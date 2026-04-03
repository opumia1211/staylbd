<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('popup_ads', function (Blueprint $table) {
            $table->string('position', 20)->default('center')->after('height')
                ->comment('center, top-left, top-right, bottom-left, bottom-right');
        });
    }

    public function down(): void
    {
        Schema::table('popup_ads', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
