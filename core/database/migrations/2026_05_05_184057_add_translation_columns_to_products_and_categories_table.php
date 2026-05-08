<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('name_bn')->nullable()->after('name');
            $table->string('name_hi')->nullable()->after('name_bn');
            $table->string('name_ar')->nullable()->after('name_hi');
            $table->string('name_ru')->nullable()->after('name_ar');
            
            $table->text('summary_bn')->nullable()->after('summary');
            $table->text('summary_hi')->nullable()->after('summary_bn');
            $table->text('summary_ar')->nullable()->after('summary_hi');
            $table->text('summary_ru')->nullable()->after('summary_ar');

            $table->longText('description_bn')->nullable()->after('description');
            $table->longText('description_hi')->nullable()->after('description_bn');
            $table->longText('description_ar')->nullable()->after('description_hi');
            $table->longText('description_ru')->nullable()->after('description_ar');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('name_bn')->nullable()->after('name');
            $table->string('name_hi')->nullable()->after('name_bn');
            $table->string('name_ar')->nullable()->after('name_hi');
            $table->string('name_ru')->nullable()->after('name_ar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name_bn', 'name_hi', 'name_ar', 'name_ru', 'summary_bn', 'summary_hi', 'summary_ar', 'summary_ru', 'description_bn', 'description_hi', 'description_ar', 'description_ru']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['name_bn', 'name_hi', 'name_ar', 'name_ru']);
        });
    }
};
