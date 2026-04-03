<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admin_ip_whitelist', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->index();
            $table->string('label', 100)->nullable();
            $table->boolean('enabled')->default(true);
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();

            $table->unique(['ip_address']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_ip_whitelist');
    }
};
