<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('message_status_logs')) {
            return;
        }
        Schema::create('message_status_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->string('status', 32)->comment('sent, delivered, read');
            $table->timestamp('created_at');
            $table->foreign('message_id')->references('id')->on('omnichannel_messages')->onDelete('cascade');
            $table->index(['message_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_status_logs');
    }
};
