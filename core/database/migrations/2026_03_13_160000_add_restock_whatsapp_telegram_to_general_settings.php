<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Restock notification via WhatsApp & Telegram (enable + message template).
     * Actual sending requires API setup (Twilio/WhatsApp Business, Telegram Bot).
     */
    public function up(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'restock_whatsapp_enable')) {
                $table->tinyInteger('restock_whatsapp_enable')->default(0)->after('restock_message_template')->comment('1=send restock alert via WhatsApp when configured');
            }
            if (!Schema::hasColumn('general_settings', 'restock_whatsapp_message')) {
                $table->text('restock_whatsapp_message')->nullable()->after('restock_whatsapp_enable')->comment('Template for WhatsApp. Use {product_name}, {product_url}');
            }
            if (!Schema::hasColumn('general_settings', 'restock_telegram_enable')) {
                $table->tinyInteger('restock_telegram_enable')->default(0)->after('restock_whatsapp_message')->comment('1=send restock alert via Telegram when configured');
            }
            if (!Schema::hasColumn('general_settings', 'restock_telegram_message')) {
                $table->text('restock_telegram_message')->nullable()->after('restock_telegram_enable')->comment('Template for Telegram. Use {product_name}, {product_url}');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            $cols = ['restock_whatsapp_enable', 'restock_whatsapp_message', 'restock_telegram_enable', 'restock_telegram_message'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('general_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
