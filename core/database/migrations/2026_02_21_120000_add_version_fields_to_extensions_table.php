<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('extensions')) {
            return;
        }

        Schema::table('extensions', function (Blueprint $table) {
            if (!Schema::hasColumn('extensions', 'version')) {
                $table->string('version', 32)->nullable()->after('status');
            }
            if (!Schema::hasColumn('extensions', 'dependency')) {
                $table->json('dependency')->nullable()->after('version')->comment('Required extensions/versions');
            }
            if (!Schema::hasColumn('extensions', 'last_updated')) {
                $table->timestamp('last_updated')->nullable()->after('updated_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('extensions')) {
            return;
        }

        Schema::table('extensions', function (Blueprint $table) {
            $cols = ['version', 'dependency', 'last_updated'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('extensions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
