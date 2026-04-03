<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 64)->index(); // failed_admin_login, permission_denied, webhook_signature_failure, rate_limit_exceeded
            $table->string('severity', 16)->default('medium'); // low, medium, critical
            $table->string('ip_address', 45)->nullable()->index();
            $table->unsignedBigInteger('admin_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('route', 255)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->json('payload')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('security_events');
    }
};
