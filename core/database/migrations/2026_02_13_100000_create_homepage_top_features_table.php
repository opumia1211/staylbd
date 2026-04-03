<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('homepage_top_features')) {
            return;
        }
        Schema::create('homepage_top_features', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('icon_image')->nullable();
            $table->string('background_style')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->decimal('offer_price', 18, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->dateTime('offer_start')->nullable();
            $table->dateTime('offer_end')->nullable();
            $table->string('redirect_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1=Active, 0=Hidden');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('homepage_top_features');
    }
};
