<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seeder_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('seeder_class', 255)->index();
            $table->string('action', 50)->default('run'); // run, skip, error
            $table->text('message')->nullable();
            $table->string('environment', 50)->nullable();
            $table->timestamp('run_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seeder_audit_logs');
    }
};
