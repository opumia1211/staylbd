<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('payment_events')) {
            return;
        }
        Schema::table('payment_events', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_events', 'idempotency_key')) {
                $table->string('idempotency_key', 128)->nullable()->index()->after('gateway');
            }
        });
        if (Schema::hasColumn('payment_events', 'idempotency_key')) {
            try {
                Schema::table('payment_events', function (Blueprint $table) {
                    $table->unique(['gateway', 'idempotency_key'], 'payment_events_gateway_idempotency_unique');
                });
            } catch (\Throwable $e) {
                // Index may already exist
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('payment_events') && Schema::hasColumn('payment_events', 'idempotency_key')) {
            Schema::table('payment_events', function (Blueprint $table) {
                $table->dropUnique('payment_events_gateway_idempotency_unique');
                $table->dropColumn('idempotency_key');
            });
        }
    }
};
