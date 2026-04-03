<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('banner_analytics')) {
            return;
        }
        Schema::create('banner_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('frontend_id')->index()->comment('banner element id (frontends.id)');
            $table->string('event', 20)->index()->comment('impression|click');
            $table->string('device', 50)->nullable()->comment('desktop|mobile|tablet');
            $table->string('campaign_source', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banner_analytics');
    }
};
