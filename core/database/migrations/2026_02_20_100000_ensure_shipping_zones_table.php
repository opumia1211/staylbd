<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fallback: create shipping_zones table if missing (fixes "Shipping zones table is missing" error).
 * Run: php artisan migrate
 */
return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('shipping_zones')) {
            return;
        }

        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('type', 20)->default('national');
            $table->unsignedTinyInteger('status')->default(1);
            $table->decimal('base_price', 18, 2)->default(0);
            $table->string('estimated_days', 50)->nullable();
            $table->timestamps();
        });
        Schema::table('shipping_zones', function (Blueprint $table) {
            $table->index(['status', 'type']);
        });
    }

    public function down()
    {
        if (Schema::hasTable('shipping_zones')) {
            Schema::dropIfExists('shipping_zones');
        }
    }
};
