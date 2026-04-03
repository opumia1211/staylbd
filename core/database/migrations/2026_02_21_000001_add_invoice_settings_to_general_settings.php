<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'invoice_logo')) {
                $table->string('invoice_logo', 191)->nullable()->after('favicon');
            }
            if (!Schema::hasColumn('general_settings', 'invoice_signature')) {
                $table->string('invoice_signature', 191)->nullable()->after('invoice_logo');
            }
            if (!Schema::hasColumn('general_settings', 'invoice_authorized_name')) {
                $table->string('invoice_authorized_name', 191)->nullable()->after('invoice_signature');
            }
        });
    }

    public function down()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $cols = ['invoice_logo', 'invoice_signature', 'invoice_authorized_name'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('general_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
