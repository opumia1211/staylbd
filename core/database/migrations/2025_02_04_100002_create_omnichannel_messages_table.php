<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('omnichannel_messages')) {
            return;
        }
        Schema::create('omnichannel_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->string('sender_type', 32)->comment('user, admin, system');
            $table->unsignedBigInteger('sender_id')->nullable()->comment('user_id or admin_id');
            $table->longText('message')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omnichannel_messages');
    }
};
