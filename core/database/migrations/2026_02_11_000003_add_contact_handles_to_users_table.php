<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'whatsapp_identity')) {
                $table->string('whatsapp_identity')->nullable()->after('mobile')->index();
            }
            if (!Schema::hasColumn('users', 'telegram_username')) {
                $table->string('telegram_username')->nullable()->after('whatsapp_identity')->index();
            }
            if (!Schema::hasColumn('users', 'contact_channel_opt_in')) {
                $table->boolean('contact_channel_opt_in')->default(true)->after('telegram_username');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'contact_channel_opt_in')) {
                $table->dropColumn('contact_channel_opt_in');
            }
            if (Schema::hasColumn('users', 'telegram_username')) {
                $table->dropColumn('telegram_username');
            }
            if (Schema::hasColumn('users', 'whatsapp_identity')) {
                $table->dropColumn('whatsapp_identity');
            }
        });
    }
};
