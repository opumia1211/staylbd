<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('order_channels')) {
            Schema::create('order_channels', function (Blueprint $table) {
                $table->id();
                $table->string('name', 120);
                $table->string('platform', 40)->default('custom');
                $table->string('direction', 20)->default('both');
                $table->string('api_url', 500)->nullable();
                $table->text('api_key')->nullable();
                $table->string('webhook_token', 64)->nullable()->unique();
                $table->json('settings')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_sync_at')->nullable();
                $table->unsignedInteger('imported_count')->default(0);
                $table->unsignedInteger('exported_count')->default(0);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'external_order_ref')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('external_order_ref', 120)->nullable()->after('order_no');
                $table->index(['order_source', 'external_order_ref'], 'orders_external_ref_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'external_order_ref')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex('orders_external_ref_idx');
                $table->dropColumn('external_order_ref');
            });
        }
        Schema::dropIfExists('order_channels');
    }
};
