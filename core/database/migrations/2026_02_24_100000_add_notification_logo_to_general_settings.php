<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'notification_logo')) {
                $table->string('notification_logo', 191)->nullable()->after('favicon');
            }
        });
    }

    public function down()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'notification_logo')) {
                $table->dropColumn('notification_logo');
            }
        });
    }
};
