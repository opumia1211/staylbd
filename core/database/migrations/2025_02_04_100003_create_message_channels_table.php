<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('message_channels')) {
            return;
        }
        Schema::create('message_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('slug', 32)->unique()->comment('web, telegram, whatsapp, facebook, instagram, email');
            $table->json('config')->nullable()->comment('API keys, webhook URL, etc.');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_channels');
    }
};
