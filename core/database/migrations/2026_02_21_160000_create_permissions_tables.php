<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();  // e.g. products.view, products.create
            $table->string('group', 50)->nullable(); // e.g. products, orders
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();
            $table->unique(['admin_id', 'permission_id']);
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role', 50); // owner, super_admin, admin, manager, support
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();
            $table->unique(['role', 'permission_id']);
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_permissions');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
    }
};
