<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('delivery_zones')) {
            return;
        }
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thana_id')->constrained('thanas')->onDelete('cascade');
            $table->decimal('delivery_charge', 14, 2)->default(0);
            $table->string('estimated_days', 50)->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
        });
        Schema::table('delivery_zones', function (Blueprint $table) {
            $table->index('thana_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_zones');
    }
};
