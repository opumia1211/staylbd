<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('order_shipment_trackings')) {
            return;
        }
        Schema::create('order_shipment_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('status', 50)->default('processing'); // processing, picked, in_transit, out_for_delivery, delivered
            $table->string('location_name', 200)->nullable();
            $table->string('location_address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('notes', 500)->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->string('courier_name', 100)->nullable();
            $table->timestamps();
        });
        Schema::table('order_shipment_trackings', function (Blueprint $table) {
            $table->index(['order_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_shipment_trackings');
    }
};
