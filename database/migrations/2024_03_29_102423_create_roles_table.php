<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->autoIncrement();
            $table->string('name')->unique();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->unsignedSmallInteger('role_id')
                ->foreign()
                ->references('id')
                ->on('roles');

            $table->unsignedSmallInteger('user_id')
                ->foreign()
                ->references('id')
                ->on('users');

            $table->primary(['role_id', 'user_id']);
        });

        Schema::create('role_permission', function (Blueprint $table) {
            $table->unsignedSmallInteger('role_id')
                ->foreign()
                ->references('id')
                ->on('roles');

            $table->unsignedSmallInteger('permission_id')
                ->foreign()
                ->references('id')
                ->on('permissions');

            $table->primary(['role_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('role_permission');
    }
};
