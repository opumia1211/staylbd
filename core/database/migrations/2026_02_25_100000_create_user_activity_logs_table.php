<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_activity_logs')) {
            return;
        }
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('session_id', 100)->nullable()->index();
            $table->string('action_type', 60)->index();
            $table->string('description', 1000)->nullable();
            $table->string('model_type', 100)->nullable()->comment('e.g. product, order, deposit');
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('device', 50)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('os', 100)->nullable();
            $table->string('country', 100)->nullable()->index();
            $table->string('city', 100)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('url', 500)->nullable();
            $table->timestamps();
        });

        Schema::table('user_activity_logs', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('user_activity_logs', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_activity_logs');
    }
};
