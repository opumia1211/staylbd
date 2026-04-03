<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_events', function (Blueprint $table) {
            $table->id();
            $table->string('gateway', 64)->index();
            $table->string('trx', 100)->nullable()->index();
            $table->unsignedBigInteger('deposit_id')->nullable()->index();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->string('event_type', 64)->index(); // status_change, webhook_received, signature_verified, signature_failed
            $table->unsignedTinyInteger('old_status')->nullable();
            $table->unsignedTinyInteger('new_status')->nullable();
            $table->boolean('signature_valid')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('gateway_response')->nullable();
            $table->json('webhook_payload')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_events');
    }
};
