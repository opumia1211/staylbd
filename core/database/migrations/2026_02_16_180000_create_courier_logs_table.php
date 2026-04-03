<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('courier_logs')) {
            return;
        }
        Schema::create('courier_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('courier_type', 50); // steadfast, pathao
            $table->string('courier_order_id')->nullable();
            $table->string('status', 20)->default('pending'); // success, failed, pending
            $table->text('request_data')->nullable();
            $table->text('response_data')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('courier_type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courier_logs');
    }
};
