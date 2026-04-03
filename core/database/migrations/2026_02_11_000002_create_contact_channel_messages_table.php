<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('contact_channel_messages')) {
            return;
        }
        Schema::create('contact_channel_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_channel_integration_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('support_ticket_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel', 32)->index();
            $table->string('direction', 16)->default('inbound')->index();
            $table->string('remote_chat_id')->nullable()->index();
            $table->string('remote_message_id')->nullable()->index();
            $table->string('sender_name')->nullable();
            $table->string('sender_handle')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->json('attachments')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 32)->default('queued');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['channel', 'remote_message_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_channel_messages');
    }
};
