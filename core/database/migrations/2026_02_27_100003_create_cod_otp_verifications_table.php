<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cod_otp_verifications')) {
            return;
        }
        Schema::create('cod_otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('mobile', 50);
            $table->string('otp', 10);
            $table->string('session_id', 100)->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->index(['mobile', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cod_otp_verifications');
    }
};
