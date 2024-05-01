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
        Schema::create('process_responsible_people', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->autoIncrement();
            $table->string('name')->unique();
        });

        Schema::create('process_process_responsible_people', function (Blueprint $table) {
            $table->unsignedInteger('process_id');
            $table->unsignedSmallInteger('responsible_person_id');
            $table->primary(['process_id', 'responsible_person_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_responsible_people');
        Schema::dropIfExists('process_process_responsible_people');
    }
};
