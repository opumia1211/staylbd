<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('notification_templates')) {
            return;
        }
        $exists = DB::table('notification_templates')->where('act', 'DELIVERY_SCANNED_BY_DRIVER')->exists();
        if (!$exists) {
            DB::table('notification_templates')->insert([
                'act' => 'DELIVERY_SCANNED_BY_DRIVER',
                'name' => 'Delivery location scanned',
                'subj' => 'Delivery scanned for order {{order_no}}',
                'email_body' => '<p>Delivery personnel have scanned the delivery location for your order <strong>{{order_no}}</strong>. Your product has reached the delivery point.</p>',
                'sms_body' => 'Delivery scanned for order {{order_no}}. Your product has reached the delivery point.',
                'shortcodes' => json_encode(['order_no', 'method_name']),
                'email_status' => 1,
                'sms_status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('notification_templates')) {
            DB::table('notification_templates')->where('act', 'DELIVERY_SCANNED_BY_DRIVER')->delete();
        }
    }
};
