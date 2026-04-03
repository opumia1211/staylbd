<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('auto_responses')) {
            return;
        }
        Schema::create('auto_responses', function (Blueprint $table) {
            $table->id();
            $table->string('trigger_type', 32)->comment('welcome, offline, keyword, scheduled');
            $table->string('keyword', 255)->nullable()->comment('For keyword trigger');
            $table->text('message');
            $table->string('channel', 32)->nullable()->comment('Null = all channels');
            $table->json('config')->nullable()->comment('Business hours, schedule, etc.');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['trigger_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_responses');
    }
};
