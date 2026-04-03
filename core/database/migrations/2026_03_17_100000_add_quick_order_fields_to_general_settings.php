<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores which Quick Order form fields are shown on the public Quick Order page (cart?open_guest_checkout=1).
     * Value: JSON array of field keys, e.g. ["guest_phone","guest_name","guest_address","guest_area_city","guest_delivery_note"].
     */
    public function up(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        if (Schema::hasColumn('general_settings', 'quick_order_fields')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            $table->text('quick_order_fields')->nullable()->after('discount_type');
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
            if (Schema::hasColumn('general_settings', 'quick_order_fields')) {
                $table->dropColumn('quick_order_fields');
            }
        });
    }
};
