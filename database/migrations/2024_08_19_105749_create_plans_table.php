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
        Schema::create('plans', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->autoIncrement();
            $table->unsignedSmallInteger('year')->unique();
        });

        Schema::create('plan_country_code_marketing_authorization_holder', function (Blueprint $table) {
            $table->unsignedSmallInteger('plan_id')
                ->foreign()
                ->references('id')
                ->on('plans');

            $table->unsignedInteger('country_code_id')
                ->foreign()
                ->references('id')
                ->on('country_codes');

            $table->unsignedInteger('marketing_authorization_holder_id')
                ->foreign()
                ->references('id')
                ->on('marketing_authorization_holders');

            $table->unsignedSmallInteger('January');
            $table->unsignedSmallInteger('February');
            $table->unsignedSmallInteger('March');
            $table->unsignedSmallInteger('April');
            $table->unsignedSmallInteger('May');
            $table->unsignedSmallInteger('June');
            $table->unsignedSmallInteger('July');
            $table->unsignedSmallInteger('August');
            $table->unsignedSmallInteger('September');
            $table->unsignedSmallInteger('October');
            $table->unsignedSmallInteger('November');
            $table->unsignedSmallInteger('December');

            $table->primary(['country_code_id', 'plan_id', 'marketing_authorization_holder_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
