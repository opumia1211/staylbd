<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Immutable financial ledger – append-only, hash chained.
     */
    public function up()
    {
        Schema::create('payment_ledger', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->unsignedBigInteger('transaction_id')->nullable()->index();
            $table->unsignedBigInteger('deposit_id')->nullable()->index();
            $table->string('gateway', 64)->nullable();
            $table->decimal('amount', 18, 8)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->string('status', 32)->index();
            $table->string('trx', 100)->nullable()->index();
            $table->string('previous_hash', 64)->nullable();
            $table->string('ledger_hash', 64)->index();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('created_at');
        });

        // Append-only enforced via PaymentLedger model (no update/delete)
    }

    public function down()
    {
        Schema::dropIfExists('payment_ledger');
    }
};
