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
        Schema::create('kvpps', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedSmallInteger('status_id');
            $table->unsignedSmallInteger('country_code_id');
            $table->unsignedSmallInteger('priority_id');
            $table->unsignedSmallInteger('source_id');
            $table->unsignedSmallInteger('inn_id');
            $table->unsignedSmallInteger('form_id');
            $table->string('dosage', 300)->nullable();
            $table->string('pack')->nullable();
            $table->unsignedSmallInteger('marketing_authorization_holder_id');
            $table->string('information', 1000)->nullable();
            $table->date('date_of_forecast')->nullable();
            $table->unsignedInteger('forecast_year_1')->nullable();
            $table->unsignedInteger('forecast_year_2')->nullable();
            $table->unsignedInteger('forecast_year_3')->nullable();
            $table->unsignedSmallInteger('portfolio_manager_id')->nullable();
            $table->unsignedSmallInteger('analyst_user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kvpps');
    }
};
