<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stock-out and restock notification messages + controls (editable from admin).
     */
    public function up(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'stock_out_user_message')) {
                $table->text('stock_out_user_message')->nullable()->after('id')->comment('Message shown to user when order fails due to stock out');
            }
            if (!Schema::hasColumn('general_settings', 'stock_out_admin_message')) {
                $table->string('stock_out_admin_message', 500)->nullable()->after('stock_out_user_message')->comment('Admin notification title when customers try to order out-of-stock product');
            }
            if (!Schema::hasColumn('general_settings', 'restock_notify_enable')) {
                $table->tinyInteger('restock_notify_enable')->default(1)->after('stock_out_admin_message')->comment('1=notify users (cart/wishlist/compare) when product back in stock');
            }
            if (!Schema::hasColumn('general_settings', 'restock_message_template')) {
                $table->text('restock_message_template')->nullable()->after('restock_notify_enable')->comment('In-app message for restock notification. Use {product_name}, {product_url}');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            $cols = ['stock_out_user_message', 'stock_out_admin_message', 'restock_notify_enable', 'restock_message_template'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('general_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
