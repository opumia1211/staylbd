<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('fraud_blocks')) {
            Schema::create('fraud_blocks', function (Blueprint $table) {
                $table->id();
                $table->string('type', 20); // ip, phone
                $table->string('value', 100);
                $table->string('reason', 500)->nullable();
                $table->unsignedBigInteger('blocked_by_admin_id')->nullable();
                $table->timestamps();
                $table->unique(['type', 'value']);
            });
        }

        if (!Schema::hasTable('fraud_complaints')) {
            Schema::create('fraud_complaints', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('reported_by_admin_id')->nullable();
                $table->string('reason', 500)->nullable();
                $table->string('status', 30)->default('open'); // open, resolved, rejected
                $table->text('admin_note')->nullable();
                $table->timestamps();
                $table->index(['order_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_complaints');
        Schema::dropIfExists('fraud_blocks');
    }
};
