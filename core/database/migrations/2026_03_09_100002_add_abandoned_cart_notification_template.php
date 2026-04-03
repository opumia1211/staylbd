<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('notification_templates')) {
            return;
        }
        $exists = DB::table('notification_templates')->where('act', 'ABANDONED_CART')->exists();
        if (!$exists) {
            DB::table('notification_templates')->insert([
                'act' => 'ABANDONED_CART',
                'name' => 'Abandoned Cart Reminder',
                'subj' => 'You left items in your cart – complete your order',
                'email_body' => '<p>Hi {{user_name}},</p><p>You left items in your cart. Complete your order now before stock runs out!</p><p><strong>Cart value:</strong> {{cart_value}}</p><p><a href="{{recovery_link}}" style="display:inline-block;padding:10px 20px;background:#007bff;color:#fff;text-decoration:none;border-radius:5px;">Complete my order</a></p><p>If you have any questions, reply to this email.</p>',
                'sms_body' => 'You left items in your cart. Complete your order: {{recovery_link}}',
                'shortcodes' => json_encode(['user_name', 'recovery_link', 'cart_value', 'product_list']),
                'email_status' => 1,
                'sms_status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('notification_templates')) {
            DB::table('notification_templates')->where('act', 'ABANDONED_CART')->delete();
        }
    }
};
