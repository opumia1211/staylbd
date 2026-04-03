<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('autopay_messages')) {
            return;
        }
        Schema::create('autopay_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('method_code')->index();
            $table->unsignedBigInteger('deposit_id')->nullable()->index();
            $table->string('sender', 100)->nullable();
            $table->text('raw_message')->nullable();
            $table->decimal('amount', 18, 8)->nullable();
            $table->string('trx_id', 100)->nullable();
            $table->boolean('matched')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autopay_messages');
    }
};
