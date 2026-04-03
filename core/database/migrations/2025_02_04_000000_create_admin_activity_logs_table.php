<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Admin Activity Log - কোন admin কী change করেছে track করা
     */
    public function up()
    {
        if (Schema::hasTable('admin_activity_logs')) {
            return;
        }
        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable()->index();
            $table->string('action', 50)->index(); // create, update, delete, status, etc.
            $table->string('model', 100)->nullable()->index(); // Product, Order, Category, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('old_values')->nullable(); // JSON
            $table->text('new_values')->nullable(); // JSON
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('admin_activity_logs');
    }
};
