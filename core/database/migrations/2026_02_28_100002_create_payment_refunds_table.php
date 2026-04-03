<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payment_refunds')) {
            return;
        }
        Schema::create('payment_refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deposit_id')->nullable()->index();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->decimal('amount', 18, 2)->default(0);
            $table->string('type', 20)->default('full'); // full, partial
            $table->string('status', 30)->default('pending')->index(); // pending, processed, failed
            $table->string('gateway_refund_id', 255)->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_refunds');
    }
};
