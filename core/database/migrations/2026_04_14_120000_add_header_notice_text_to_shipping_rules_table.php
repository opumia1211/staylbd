<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('shipping_rules')) {
            return;
        }

        Schema::table('shipping_rules', function (Blueprint $table) {
            if (!Schema::hasColumn('shipping_rules', 'header_notice_text')) {
                $table->string('header_notice_text', 255)
                    ->default('Cash on Delivery available nationwide')
                    ->after('international_enabled');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('shipping_rules')) {
            return;
        }

        Schema::table('shipping_rules', function (Blueprint $table) {
            if (Schema::hasColumn('shipping_rules', 'header_notice_text')) {
                $table->dropColumn('header_notice_text');
            }
        });
    }
};
