<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'abandoned_cart_inactivity_minutes')) {
                $table->unsignedSmallInteger('abandoned_cart_inactivity_minutes')->default(60)->after('id');
            }
            if (!Schema::hasColumn('general_settings', 'abandoned_cart_reminder_email')) {
                $table->boolean('abandoned_cart_reminder_email')->default(true)->after('abandoned_cart_inactivity_minutes');
            }
            if (!Schema::hasColumn('general_settings', 'abandoned_cart_reminder_sms')) {
                $table->boolean('abandoned_cart_reminder_sms')->default(false)->after('abandoned_cart_reminder_email');
            }
            if (!Schema::hasColumn('general_settings', 'abandoned_cart_cleanup_days')) {
                $table->unsignedSmallInteger('abandoned_cart_cleanup_days')->default(30)->after('abandoned_cart_reminder_sms');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            $cols = ['abandoned_cart_inactivity_minutes', 'abandoned_cart_reminder_email', 'abandoned_cart_reminder_sms', 'abandoned_cart_cleanup_days'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('general_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
