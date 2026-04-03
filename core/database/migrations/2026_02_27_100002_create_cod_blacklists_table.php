<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cod_blacklists')) {
            return;
        }
        Schema::create('cod_blacklists', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->comment('mobile, address, ip');
            $table->string('value', 255)->comment('phone, address hash, or IP');
            $table->string('reason', 500)->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();
            $table->index(['type', 'value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cod_blacklists');
    }
};
