<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('abandoned_carts')) {
            return;
        }
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('session_id', 191)->nullable()->index();
            $table->string('cookie_id', 191)->nullable()->index();
            $table->string('local_storage_id', 191)->nullable()->index();
            $table->json('cart_snapshot')->nullable()->comment('Full cart items for recovery');
            $table->decimal('cart_value', 18, 2)->default(0);
            $table->timestamp('checkout_started_at')->nullable();
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('device_type', 50)->nullable();
            $table->string('email', 191)->nullable()->index();
            $table->string('mobile', 50)->nullable();
            $table->string('status', 20)->default('pending')->index()->comment('pending, abandoned, recovered');
            $table->timestamp('reminder_sent_at')->nullable();
            $table->string('recovery_token', 64)->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abandoned_carts');
    }
};
