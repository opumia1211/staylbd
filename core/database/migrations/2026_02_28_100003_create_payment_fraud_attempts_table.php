<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payment_fraud_attempts')) {
            return;
        }
        Schema::create('payment_fraud_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->string('gateway', 50)->nullable();
            $table->string('reason', 100)->nullable(); // failed_attempt, velocity, multiple_cards
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_fraud_attempts');
    }
};
