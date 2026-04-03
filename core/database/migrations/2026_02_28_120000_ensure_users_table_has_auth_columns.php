<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure users table has all columns required for registration and login.
     * Adds missing columns only; safe to run on existing or fresh installs.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'firstname')) {
                $table->string('firstname', 100)->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'lastname')) {
                $table->string('lastname', 100)->nullable()->after('firstname');
            }
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username', 40)->nullable()->unique()->after('lastname');
            }
            if (!Schema::hasColumn('users', 'email')) {
                $table->string('email')->unique()->after('username');
            }
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'password')) {
                $table->string('password')->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'mobile')) {
                $table->string('mobile', 50)->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'ref_by')) {
                $table->unsignedBigInteger('ref_by')->default(0)->after('mobile');
            }
            if (!Schema::hasColumn('users', 'country_code')) {
                $table->string('country_code', 10)->nullable()->after('ref_by');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('country_code');
            }
            if (!Schema::hasColumn('users', 'kyc_data')) {
                $table->text('kyc_data')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'age')) {
                $table->unsignedTinyInteger('age')->nullable()->after('kyc_data');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender', 20)->nullable()->after('age');
            }
            if (!Schema::hasColumn('users', 'profile_complete')) {
                $table->unsignedTinyInteger('profile_complete')->default(0)->after('gender');
            }
            if (!Schema::hasColumn('users', 'kv')) {
                $table->unsignedTinyInteger('kv')->default(0)->after('profile_complete');
            }
            if (!Schema::hasColumn('users', 'ev')) {
                $table->unsignedTinyInteger('ev')->default(0)->after('kv');
            }
            if (!Schema::hasColumn('users', 'sv')) {
                $table->unsignedTinyInteger('sv')->default(0)->after('ev');
            }
            if (!Schema::hasColumn('users', 'ts')) {
                $table->unsignedTinyInteger('ts')->default(0)->after('sv');
            }
            if (!Schema::hasColumn('users', 'tv')) {
                $table->unsignedTinyInteger('tv')->default(1)->after('ts');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->unsignedTinyInteger('status')->default(1)->after('tv');
            }
            if (!Schema::hasColumn('users', 'balance')) {
                $table->decimal('balance', 28, 8)->default(0)->after('status');
            }
            if (!Schema::hasColumn('users', 'ver_code')) {
                $table->string('ver_code', 20)->nullable()->after('balance');
            }
            if (!Schema::hasColumn('users', 'ver_code_send_at')) {
                $table->timestamp('ver_code_send_at')->nullable()->after('ver_code');
            }
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken()->after('ver_code_send_at');
            }
            if (!Schema::hasColumn('users', 'created_at')) {
                $table->timestamps();
            }
        });

        // If table had only Laravel default "name" column, add "name" if missing (some installs use firstname+lastname only)
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('name')->nullable()->after('lastname');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Intentionally no down() – removing columns could break existing data.
    }
};
