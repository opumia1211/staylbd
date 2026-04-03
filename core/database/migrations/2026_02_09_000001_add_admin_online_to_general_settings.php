<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'admin_online_status')) {
                $table->tinyInteger('admin_online_status')->default(0)->comment('1=Online/Green, 0=Offline/Red');
            }
        });
    }

    public function down()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'admin_online_status')) {
                $table->dropColumn('admin_online_status');
            }
        });
    }
};
