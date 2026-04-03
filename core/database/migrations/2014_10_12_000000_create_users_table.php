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
        if (Schema::hasTable('users')) {
            return;
        }
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname', 100)->nullable();
            $table->string('lastname', 100)->nullable();
            $table->string('name', 191)->nullable();
            $table->string('username', 40)->nullable()->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('mobile', 50)->nullable();
            $table->unsignedBigInteger('ref_by')->default(0);
            $table->string('country_code', 10)->nullable();
            $table->text('address')->nullable();
            $table->text('kyc_data')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('gender', 20)->nullable();
            $table->unsignedTinyInteger('profile_complete')->default(0);
            $table->unsignedTinyInteger('kv')->default(0);
            $table->unsignedTinyInteger('ev')->default(0);
            $table->unsignedTinyInteger('sv')->default(0);
            $table->unsignedTinyInteger('ts')->default(0);
            $table->unsignedTinyInteger('tv')->default(1);
            $table->unsignedTinyInteger('status')->default(1);
            $table->decimal('balance', 28, 8)->default(0);
            $table->string('ver_code', 20)->nullable();
            $table->timestamp('ver_code_send_at')->nullable();
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
        Schema::dropIfExists('users');
    }
};
