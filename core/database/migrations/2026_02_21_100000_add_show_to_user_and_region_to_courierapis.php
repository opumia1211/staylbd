<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('courierapis')) {
            return;
        }
        Schema::table('courierapis', function (Blueprint $table) {
            if (!Schema::hasColumn('courierapis', 'show_to_user')) {
                $table->boolean('show_to_user')->default(false)->after('status');
            }
            if (!Schema::hasColumn('courierapis', 'region')) {
                $table->string('region', 20)->nullable()->after('country_code'); // BD, ASIA, GLOBAL
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('courierapis')) {
            return;
        }
        Schema::table('courierapis', function (Blueprint $table) {
            if (Schema::hasColumn('courierapis', 'show_to_user')) {
                $table->dropColumn('show_to_user');
            }
            if (Schema::hasColumn('courierapis', 'region')) {
                $table->dropColumn('region');
            }
        });
    }
};
