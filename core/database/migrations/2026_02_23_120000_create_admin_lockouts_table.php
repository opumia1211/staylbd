<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admin_lockouts', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->index();
            $table->string('identifier', 191)->nullable()->index(); // username/email
            $table->unsignedInteger('failed_attempts')->default(0);
            $table->unsignedInteger('lock_count')->default(0); // consecutive locks
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('unlocked_at')->nullable();
            $table->unsignedBigInteger('unlocked_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_lockouts');
    }
};
