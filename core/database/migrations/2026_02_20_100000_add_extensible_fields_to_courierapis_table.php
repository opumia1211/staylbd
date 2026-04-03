<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('courierapis')) {
            return;
        }

        Schema::table('courierapis', function (Blueprint $table) {
            if (!Schema::hasColumn('courierapis', 'name')) {
                $table->string('name')->nullable()->after('type');
            }
            if (!Schema::hasColumn('courierapis', 'country_code')) {
                $table->string('country_code', 10)->default('BD')->after('name');
            }
            if (!Schema::hasColumn('courierapis', 'config')) {
                $table->json('config')->nullable()->after('token');
            }
            if (!Schema::hasColumn('courierapis', 'sort_order')) {
                $table->unsignedSmallInteger('sort_order')->default(0)->after('status');
            }
        });

        // Backfill display names for existing rows
        if (Schema::hasColumn('courierapis', 'name')) {
            DB::table('courierapis')->where('type', 'steadfast')->update([
                'name' => 'Steadfast Courier',
                'country_code' => 'BD',
                'sort_order' => 1,
                'updated_at' => now(),
            ]);
            DB::table('courierapis')->where('type', 'pathao')->update([
                'name' => 'Pathao',
                'country_code' => 'BD',
                'sort_order' => 2,
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        Schema::table('courierapis', function (Blueprint $table) {
            $table->dropColumn(['name', 'country_code', 'config', 'sort_order']);
        });
    }
};
