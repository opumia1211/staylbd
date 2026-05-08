<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name');
            $table->string('owner_name');
            $table->string('email')->unique();
            $table->string('phone', 40)->nullable()->unique();
            $table->string('password');
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->decimal('balance', 28, 8)->default(0);
            $table->decimal('commission_percentage', 5, 2)->default(10.00);
            $table->tinyInteger('status')->default(0)->comment('0: Pending, 1: Active, 2: Banned');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sellers');
    }
};
