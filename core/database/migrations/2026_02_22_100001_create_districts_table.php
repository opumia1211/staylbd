<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('districts')) {
            return;
        }
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained('divisions')->onDelete('cascade');
            $table->string('name_en', 100);
            $table->string('name_bn', 100);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::table('districts', function (Blueprint $table) {
            $table->index('division_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('districts');
    }
};
