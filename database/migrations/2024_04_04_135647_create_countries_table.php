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
        Schema::create('countries', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->autoIncrement();
            $table->string('name')->unique();
        });

        Schema::create('clinical_trial_country_process', function (Blueprint $table) {
            $table->unsignedInteger('process_id')
                ->foreign()
                ->references('id')
                ->on('processes');

            $table->unsignedSmallInteger('country_id')
                ->foreign()
                ->references('id')
                ->on('countries');

            $table->primary(['process_id', 'country_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
        Schema::dropIfExists('clinical_trial_country_process');
    }
};
