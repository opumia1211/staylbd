<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add width/height control for offer timer bars (e.g. 100%, 80%, 200px, auto).
     */
    public function up(): void
    {
        Schema::table('offer_timers', function (Blueprint $table) {
            $table->string('bar_width', 50)->nullable()->after('style');   // e.g. 100%, 80%, 500px, auto
            $table->string('bar_height', 50)->nullable()->after('bar_width'); // e.g. auto, 60px, 80px
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_timers', function (Blueprint $table) {
            $table->dropColumn(['bar_width', 'bar_height']);
        });
    }
};
