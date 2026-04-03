<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (Schema::hasTable('courierapis')) {
            return;
        }
        Schema::create('courierapis', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // 'steadfast' or 'pathao'
            $table->string('api_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->string('url')->nullable();
            $table->string('token')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });

        // Insert default records
        DB::table('courierapis')->insert([
            [
                'type' => 'steadfast',
                'api_key' => '',
                'secret_key' => '',
                'url' => '',
                'token' => '',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'pathao',
                'api_key' => '',
                'secret_key' => '',
                'url' => 'https://api-hermes.pathao.com',
                'token' => '',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courierapis');
    }
};
