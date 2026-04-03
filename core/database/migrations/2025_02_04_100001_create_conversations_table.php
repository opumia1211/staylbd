<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('conversations')) {
            return;
        }
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('Site user if logged in');
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('channel', 32)->default('web')->comment('web, telegram, whatsapp, facebook, instagram, email, other');
            $table->string('external_id', 255)->nullable()->comment('Telegram chat_id, WhatsApp phone, etc.');
            $table->tinyInteger('status')->default(0)->comment('0=open, 1=answered, 2=replied, 3=closed');
            $table->unsignedBigInteger('assigned_to')->nullable()->comment('Admin/agent id');
            $table->tinyInteger('priority')->default(2)->comment('1=low, 2=medium, 3=high');
            $table->string('subject', 255)->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            $table->index(['channel', 'status']);
            $table->index('assigned_to');
            $table->index('last_message_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
