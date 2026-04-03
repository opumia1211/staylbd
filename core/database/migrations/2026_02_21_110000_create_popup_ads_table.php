<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('popup_ads', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->unsignedSmallInteger('delay_seconds')->default(3)->comment('Show after N seconds');
            $table->string('image', 500)->nullable();
            $table->string('link_url', 500)->nullable();
            $table->string('width', 100)->nullable()->comment('e.g. 90%, 800px, auto');
            $table->string('height', 100)->nullable()->comment('e.g. 80vh, 600px, auto');
            $table->json('show_on_pages')->nullable()->comment('all, home, cart, etc.');
            $table->unsignedTinyInteger('is_active')->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('popup_ads');
    }
};
