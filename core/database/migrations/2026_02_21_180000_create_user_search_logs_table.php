<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_search_logs')) {
            return;
        }
        Schema::create('user_search_logs', function (Blueprint $table) {
            $table->id();
            $table->string('query', 500)->index()->comment('User search keyword/phrase');
            $table->unsignedBigInteger('user_id')->nullable()->index()->comment('Logged-in user; null for guest');
            $table->string('ip', 45)->nullable()->index();
            $table->string('user_agent', 512)->nullable();
            $table->unsignedInteger('results_count')->default(0)->comment('Number of results returned');
            $table->string('source', 20)->default('universal')->comment('universal|voice|image');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('user_search_logs', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_search_logs');
    }
};
