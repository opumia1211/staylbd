<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('deposits')) {
            return;
        }
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('order_id')->default(0)->index();
            $table->unsignedInteger('method_code')->index();
            $table->string('method_currency', 20);
            $table->decimal('amount', 18, 8)->default(0);
            $table->decimal('charge', 18, 8)->default(0);
            $table->decimal('rate', 18, 8)->default(0);
            $table->decimal('final_amo', 18, 8)->default(0);
            $table->decimal('btc_amo', 18, 8)->default(0);
            $table->string('btc_wallet', 255)->nullable();
            $table->string('trx', 100)->unique();
            $table->unsignedTinyInteger('status')->default(0)->index(); // 0=initiate, 1=success, 2=pending, 3=reject
            $table->json('detail')->nullable();
            $table->text('admin_feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deposits');
    }
};
