<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('order_automation_settings')) {
            Schema::create('order_automation_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('is_enabled')->default(true);
                $table->boolean('auto_confirm_paid')->default(true);
                $table->boolean('auto_processing_after_confirm')->default(false);
                $table->unsignedSmallInteger('auto_cancel_unpaid_days')->default(7);
                $table->boolean('auto_cancel_unpaid_enabled')->default(false);
                $table->boolean('notify_customer_on_auto')->default(false);
                $table->boolean('notify_admin_new_order')->default(true);
                $table->boolean('channel_import_enabled')->default(true);
                $table->unsignedSmallInteger('run_interval_minutes')->default(15);
                $table->timestamp('last_run_at')->nullable();
                $table->timestamps();
            });

            \DB::table('order_automation_settings')->insert([
                'is_enabled' => true,
                'auto_confirm_paid' => true,
                'auto_processing_after_confirm' => false,
                'auto_cancel_unpaid_days' => 7,
                'auto_cancel_unpaid_enabled' => false,
                'notify_customer_on_auto' => false,
                'notify_admin_new_order' => true,
                'channel_import_enabled' => true,
                'run_interval_minutes' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!Schema::hasTable('order_automation_logs')) {
            Schema::create('order_automation_logs', function (Blueprint $table) {
                $table->id();
                $table->string('action', 80);
                $table->unsignedBigInteger('order_id')->nullable();
                $table->text('message');
                $table->json('meta')->nullable();
                $table->timestamps();
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_automation_logs');
        Schema::dropIfExists('order_automation_settings');
    }
};
