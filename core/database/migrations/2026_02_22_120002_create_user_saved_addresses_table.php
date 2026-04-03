<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('user_saved_addresses')) {
            return;
        }
        Schema::create('user_saved_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('country', 100);
            $table->unsignedBigInteger('division_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('thana_id')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('address_line', 500);
            $table->string('address_line_2', 500)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->decimal('device_lat', 10, 7)->nullable();
            $table->decimal('device_lng', 10, 7)->nullable();
            $table->unsignedTinyInteger('verified_status')->default(0);
            $table->unsignedTinyInteger('is_default')->default(0);
            $table->string('label', 50)->nullable();
            $table->timestamps();
        });
        Schema::table('user_saved_addresses', function (Blueprint $table) {
            $table->index('user_id');
            $table->index(['user_id', 'is_default']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_saved_addresses');
    }
};
