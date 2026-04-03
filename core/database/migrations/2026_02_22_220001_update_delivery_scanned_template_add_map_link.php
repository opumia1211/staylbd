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
        $template = DB::table('notification_templates')->where('act', 'DELIVERY_SCANNED_BY_DRIVER')->first();
        if ($template) {
            $shortcodes = ['order_no', 'method_name', 'map_link'];
            $emailBody = '<p>{{method_name}} Order <strong>{{order_no}}</strong>.</p><p>Your product is with the delivery person. Track delivery: <a href="{{map_link}}" target="_blank">Open Google Maps</a></p>';
            $smsBody = 'Product with delivery. Order {{order_no}}. Map: {{map_link}}';
            DB::table('notification_templates')->where('act', 'DELIVERY_SCANNED_BY_DRIVER')->update([
                'shortcodes' => json_encode($shortcodes),
                'email_body' => $emailBody,
                'sms_body' => $smsBody,
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('notification_templates')) {
            return;
        }
        $template = DB::table('notification_templates')->where('act', 'DELIVERY_SCANNED_BY_DRIVER')->first();
        if ($template) {
            DB::table('notification_templates')->where('act', 'DELIVERY_SCANNED_BY_DRIVER')->update([
                'shortcodes' => json_encode(['order_no', 'method_name']),
                'email_body' => '<p>Delivery personnel have scanned the delivery location for your order <strong>{{order_no}}</strong>. Your product has reached the delivery point.</p>',
                'sms_body' => 'Delivery scanned for order {{order_no}}. Your product has reached the delivery point.',
                'updated_at' => now(),
            ]);
        }
    }
};
