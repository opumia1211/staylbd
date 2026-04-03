<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('general_settings')) {
            Schema::table('general_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('general_settings', 'invoice_qr_caption_en')) {
                    $table->text('invoice_qr_caption_en')->nullable()->after('invoice_authorized_name');
                }
                if (!Schema::hasColumn('general_settings', 'invoice_qr_caption_bn')) {
                    $table->text('invoice_qr_caption_bn')->nullable()->after('invoice_qr_caption_en');
                }
            });
        }
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'delivery_driver_scan_token')) {
                    $table->string('delivery_driver_scan_token', 64)->nullable()->unique()->after('delivery_scanned_at');
                }
                if (!Schema::hasColumn('orders', 'delivery_driver_scanned_at')) {
                    $table->timestamp('delivery_driver_scanned_at')->nullable()->after('delivery_driver_scan_token');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('general_settings')) {
            Schema::table('general_settings', function (Blueprint $table) {
                if (Schema::hasColumn('general_settings', 'invoice_qr_caption_en')) {
                    $table->dropColumn('invoice_qr_caption_en');
                }
                if (Schema::hasColumn('general_settings', 'invoice_qr_caption_bn')) {
                    $table->dropColumn('invoice_qr_caption_bn');
                }
            });
        }
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn(['delivery_driver_scan_token', 'delivery_driver_scanned_at']);
            });
        }
    }
};
