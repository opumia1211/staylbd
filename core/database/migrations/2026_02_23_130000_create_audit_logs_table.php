<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('previous_log_id')->nullable();
            $table->string('previous_hash', 64)->nullable();
            $table->string('current_hash', 64)->index();
            $table->string('event_type', 64)->index();
            $table->string('actor_type', 64)->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('target_type', 64)->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('payload')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};
