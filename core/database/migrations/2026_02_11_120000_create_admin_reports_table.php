<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('admin_reports')) {
            return;
        }
        Schema::create('admin_reports', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('bug'); // bug, feature
            $table->text('message');
            $table->string('status', 20)->default('pending'); // pending, read, resolved
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('admin_name', 100)->nullable();
            $table->string('page_url', 500)->nullable();
            $table->string('browser_info', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_reports');
    }
};
