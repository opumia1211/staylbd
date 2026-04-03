<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('divisions')) {
            return;
        }
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 100);
            $table->string('name_bn', 100);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('divisions');
    }
};
