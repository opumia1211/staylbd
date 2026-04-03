<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('contact_channel_integrations')) {
            return;
        }
        Schema::create('contact_channel_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('channel', 32)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_primary')->default(false);
            $table->json('settings')->nullable();
            $table->json('auth_meta')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('last_error_at')->nullable();
            $table->string('last_error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_channel_integrations');
    }
};
