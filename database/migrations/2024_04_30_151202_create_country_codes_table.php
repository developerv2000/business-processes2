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
        Schema::create('country_codes', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->autoIncrement();
            $table->string('name')->unique();
            $table->unsignedMediumInteger('usage_count')->default(0);
        });

        Schema::create('country_code_kvpp', function (Blueprint $table) {
            $table->unsignedInteger('country_code_id')
                ->foreign()
                ->references('id')
                ->on('country_codes');

            $table->unsignedSmallInteger('kvpp_id')
                ->foreign()
                ->references('id')
                ->on('kvpps');

            $table->primary(['country_code_id', 'kvpp_id']);
        });

        Schema::create('country_code_plan', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->autoIncrement();

            $table->unsignedInteger('country_code_id')
                ->foreign()
                ->references('id')
                ->on('country_codes');

            $table->unsignedSmallInteger('plan_id')
                ->foreign()
                ->references('id')
                ->on('plans');

            $table->primary(['id', 'country_code_id', 'plan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_codes');
        Schema::dropIfExists('country_code_kvpp');
    }
};
