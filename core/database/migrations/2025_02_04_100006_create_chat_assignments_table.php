<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chat_assignments')) {
            return;
        }
        Schema::create('chat_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('admin_id');
            $table->timestamp('assigned_at');
            $table->timestamps();
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->index('admin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_assignments');
    }
};
