<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payment_transactions')) {
            return;
        }
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->unsignedBigInteger('deposit_id')->nullable()->index();
            $table->string('payment_method', 50)->nullable()->index();
            $table->string('transaction_id', 255)->nullable()->index();
            $table->json('gateway_response')->nullable();
            $table->decimal('amount', 18, 2)->default(0);
            $table->string('currency', 10)->default('BDT');
            $table->string('status', 30)->default('pending')->index(); // pending, success, failed, refunded
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
