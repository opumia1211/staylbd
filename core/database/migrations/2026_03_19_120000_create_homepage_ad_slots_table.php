<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_ad_slots', function (Blueprint $table) {
            $table->id();
            $table->string('admin_title', 191);
            $table->string('advertiser_name', 191)->nullable();
            $table->string('image', 512);
            $table->string('link_url', 512)->nullable();
            $table->boolean('open_new_tab')->default(true);
            $table->string('frame_style', 32)->default('thin'); // thin, card, minimal, bordered
            $table->string('width_mode', 16)->default('full'); // full, wide, half, third, quarter
            $table->unsignedSmallInteger('max_height_px')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_ad_slots');
    }
};
