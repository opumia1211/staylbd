<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds Public / Pending / Schedule system for categories.
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'publish_status')) {
                $table->string('publish_status', 20)->default('public');
            }
            if (!Schema::hasColumn('categories', 'scheduled_at')) {
                $table->dateTime('scheduled_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'publish_status')) {
                $table->dropColumn('publish_status');
            }
            if (Schema::hasColumn('categories', 'scheduled_at')) {
                $table->dropColumn('scheduled_at');
            }
        });
    }
};
