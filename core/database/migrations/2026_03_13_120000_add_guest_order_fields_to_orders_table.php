<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds guest checkout fields: user_type, guest_name, guest_phone, guest_email, guest_address, guest_location.
     * Makes user_id nullable so guest orders can be stored without a user.
     */
    public function up()
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'user_type')) {
                $table->string('user_type', 20)->default('registered')->after('id')->comment('registered|guest');
            }
            if (!Schema::hasColumn('orders', 'guest_name')) {
                $table->string('guest_name', 200)->nullable()->after('user_type');
            }
            if (!Schema::hasColumn('orders', 'guest_phone')) {
                $table->string('guest_phone', 50)->nullable()->after('guest_name');
            }
            if (!Schema::hasColumn('orders', 'guest_email')) {
                $table->string('guest_email', 100)->nullable()->after('guest_phone');
            }
            if (!Schema::hasColumn('orders', 'guest_address')) {
                $table->text('guest_address')->nullable()->after('guest_email');
            }
            if (!Schema::hasColumn('orders', 'guest_location')) {
                $table->string('guest_location', 500)->nullable()->after('guest_address')->comment('District/City/Area text or JSON');
            }
            if (!Schema::hasColumn('orders', 'guest_delivery_note')) {
                $table->text('guest_delivery_note')->nullable()->after('guest_location');
            }
            if (!Schema::hasColumn('orders', 'guest_preferred_delivery_time')) {
                $table->string('guest_preferred_delivery_time', 200)->nullable()->after('guest_delivery_note');
            }
        });

        // Make user_id nullable for guest orders. Requires doctrine/dbal for ->change().
        // If you get an error, run: ALTER TABLE orders MODIFY user_id BIGINT UNSIGNED NULL;
        if (Schema::hasColumn('orders', 'user_id')) {
            try {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE orders MODIFY user_id BIGINT UNSIGNED NULL');
            } catch (\Throwable $e) {
                // Ignore if already nullable or DB doesn't support
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $cols = ['user_type', 'guest_name', 'guest_phone', 'guest_email', 'guest_address', 'guest_location', 'guest_delivery_note', 'guest_preferred_delivery_time'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
