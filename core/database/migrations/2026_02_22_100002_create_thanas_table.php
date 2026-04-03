<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('thanas')) {
            return;
        }
        Schema::create('thanas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');
            $table->string('name_en', 150);
            $table->string('name_bn', 150);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::table('thanas', function (Blueprint $table) {
            $table->index('district_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('thanas');
    }
};
