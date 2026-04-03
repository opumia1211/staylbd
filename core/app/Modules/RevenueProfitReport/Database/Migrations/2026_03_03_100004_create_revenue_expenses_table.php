<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('revenue_expenses')) {
            return;
        }
        Schema::create('revenue_expenses', function (Blueprint $table) {
            $table->id();
            $table->date('expense_date');
            $table->decimal('amount', 14, 2);
            $table->string('category', 100)->nullable();
            $table->string('note', 500)->nullable();
            $table->unsignedBigInteger('added_by_admin_id')->nullable();
            $table->timestamps();
            $table->index(['expense_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenue_expenses');
    }
};
