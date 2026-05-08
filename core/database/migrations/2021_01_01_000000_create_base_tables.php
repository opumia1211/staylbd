<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('name', 40)->nullable();
                $table->string('email', 40)->unique();
                $table->string('username', 40)->unique();
                $table->string('password');
                $table->string('image', 100)->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('general_settings')) {
            Schema::create('general_settings', function (Blueprint $table) {
                $table->id();
                $table->string('site_name', 100)->nullable();
                $table->string('cur_text', 20)->default('USD');
                $table->string('cur_sym', 20)->default('$');
                $table->boolean('multi_language')->default(true);
                $table->text('mail_config')->nullable();
                $table->text('sms_config')->nullable();
                $table->text('global_shortcodes')->nullable();
                $table->string('favicon', 100)->nullable();
                $table->string('logo', 100)->nullable();
                $table->boolean('ev')->default(false);
                $table->boolean('sv')->default(false);
                $table->boolean('en')->default(false);
                $table->boolean('sn')->default(false);
                $table->boolean('display_stock')->default(true);
                $table->decimal('discount', 28, 8)->default(0);
                $table->tinyInteger('discount_type')->default(0);
                $table->boolean('force_ssl')->default(false);
                $table->string('active_template', 50)->default('basic');
                $table->timestamps();
            });
            \DB::table('general_settings')->insert(['site_name' => 'StayLBD', 'created_at' => now(), 'updated_at' => now()]);
        }

        if (!Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->default(0);
                $table->string('name', 40)->nullable();
                $table->string('email', 40)->nullable();
                $table->string('channel', 40)->nullable();
                $table->string('ticket', 10)->nullable();
                $table->string('subject', 255)->nullable();
                $table->tinyInteger('status')->default(0);
                $table->tinyInteger('priority')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('frontends')) {
            Schema::create('frontends', function (Blueprint $table) {
                $table->id();
                $table->string('data_keys', 40)->nullable();
                $table->longText('data_values')->nullable();
                $table->timestamps();
            });
        }
        
        if (!Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->id();
                $table->string('name', 40)->nullable();
                $table->string('code', 40)->nullable();
                $table->tinyInteger('is_default')->default(0);
                $table->timestamps();
            });
            \DB::table('languages')->insert(['name' => 'English', 'code' => 'en', 'is_default' => 1, 'created_at' => now(), 'updated_at' => now()]);
        }

        if (!Schema::hasTable('gateways')) {
            Schema::create('gateways', function (Blueprint $table) {
                $table->id();
                $table->string('code', 40)->nullable();
                $table->unsignedBigInteger('form_id')->default(0);
                $table->string('name', 40)->nullable();
                $table->string('alias', 40)->nullable();
                $table->text('gateway_parameters')->nullable();
                $table->text('supported_currencies')->nullable();
                $table->string('description', 255)->nullable();
                $table->tinyInteger('status')->default(1);
                $table->tinyInteger('crypto')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('gateway_currencies')) {
            Schema::create('gateway_currencies', function (Blueprint $table) {
                $table->id();
                $table->string('name', 40)->nullable();
                $table->string('currency', 40)->nullable();
                $table->string('symbol', 40)->nullable();
                $table->string('gateway_alias', 40)->nullable();
                $table->integer('method_code')->default(0);
                $table->decimal('min_amount', 28, 8)->default(0);
                $table->decimal('max_amount', 28, 8)->default(0);
                $table->decimal('fixed_charge', 28, 8)->default(0);
                $table->decimal('percent_charge', 28, 8)->default(0);
                $table->decimal('rate', 28, 8)->default(0);
                $table->text('gateway_parameter')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('extensions')) {
            Schema::create('extensions', function (Blueprint $table) {
                $table->id();
                $table->string('act', 40)->nullable();
                $table->string('name', 40)->nullable();
                $table->string('alias', 40)->nullable();
                $table->string('image', 255)->nullable();
                $table->text('description')->nullable();
                $table->text('script')->nullable();
                $table->text('shortcode')->nullable();
                $table->string('support', 255)->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
        Schema::dropIfExists('general_settings');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('frontends');
        Schema::dropIfExists('languages');
    }
};
