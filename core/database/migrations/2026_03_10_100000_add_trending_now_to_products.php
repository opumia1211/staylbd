<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds trending_now flag so admin can manually mark products for "Trending Now" section.
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'trending_now')) {
                $table->unsignedTinyInteger('trending_now')->default(0)->comment('1 = show in Trending Now section');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'trending_now')) {
                $table->dropColumn('trending_now');
            }
        });
    }
};
