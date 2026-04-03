<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('admins')) {
            return;
        }
        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'two_factor_secret')) {
                $table->text('two_factor_secret')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('admins', 'two_factor_recovery_codes')) {
                $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            }
            if (!Schema::hasColumn('admins', 'two_factor_confirmed_at')) {
                $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
            }
        });
    }

    public function down()
    {
        if (Schema::hasTable('admins')) {
            Schema::table('admins', function (Blueprint $table) {
                $cols = ['two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('admins', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
