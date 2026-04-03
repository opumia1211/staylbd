<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('shipping_zone_countries')) {
            return;
        }
        Schema::create('shipping_zone_countries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained('shipping_zones')->cascadeOnDelete();
            $table->string('country_iso', 5)->index(); // BD, IN, US, etc.
            $table->string('country_name', 100)->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
        });
        Schema::table('shipping_zone_countries', function (Blueprint $table) {
            $table->unique(['shipping_zone_id', 'country_iso']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_zone_countries');
    }
};
