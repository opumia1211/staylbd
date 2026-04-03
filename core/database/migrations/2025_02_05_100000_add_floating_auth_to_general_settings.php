<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'floating_login')) {
                $table->tinyInteger('floating_login')->default(1)->after('multi_language');
            }
            if (!Schema::hasColumn('general_settings', 'floating_register')) {
                $table->tinyInteger('floating_register')->default(1)->after('floating_login');
            }
        });
    }

    public function down()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'floating_login')) {
                $table->dropColumn('floating_login');
            }
            if (Schema::hasColumn('general_settings', 'floating_register')) {
                $table->dropColumn('floating_register');
            }
        });
    }
};
