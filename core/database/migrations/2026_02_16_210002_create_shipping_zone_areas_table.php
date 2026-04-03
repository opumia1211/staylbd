<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('shipping_zone_areas')) {
            return;
        }
        Schema::create('shipping_zone_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained('shipping_zones')->cascadeOnDelete();
            $table->string('area_name', 100); // e.g. "Dhaka Metro"
            $table->json('district_names')->nullable(); // ["Dhaka", "Gazipur", "Narayanganj"]
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
        });
        Schema::table('shipping_zone_areas', function (Blueprint $table) {
            $table->index('shipping_zone_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_zone_areas');
    }
};
