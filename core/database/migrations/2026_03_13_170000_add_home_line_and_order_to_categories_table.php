<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'home_line')) {
                $table->unsignedTinyInteger('home_line')->default(1)->after('featured');
            }
            if (!Schema::hasColumn('categories', 'home_order')) {
                $table->unsignedSmallInteger('home_order')->default(0)->after('home_line');
            }
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'home_line')) {
                $table->dropColumn('home_line');
            }
            if (Schema::hasColumn('categories', 'home_order')) {
                $table->dropColumn('home_order');
            }
        });
    }
};

