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
        Schema::create('processes', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('product_id');
            $table->unsignedSmallInteger('status_id');
            $table->date('status_update_date');

            // stage 1 (ВП)
            $table->unsignedSmallInteger('country_code_id');
            $table->smallInteger('days_past')->nullable();

            // stage 2 (ПО)
            $table->date('stage_2_start_date')->nullable(); // forecast date
            $table->unsignedInteger('forecast_year_1')->nullable();
            $table->unsignedInteger('forecast_year_2')->nullable();
            $table->unsignedInteger('forecast_year_3')->nullable();

            // Stage 3 (АЦ)
            $table->decimal('manufacturer_first_offered_price', 8, 2)->nullable();
            $table->decimal('manufacturer_followed_offered_price', 8, 2)->nullable();
            $table->decimal('our_first_offered_price', 8, 2)->nullable();
            $table->decimal('our_followed_offered_price', 8, 2)->nullable();
            $table->unsignedSmallInteger('currency_id')->nullable();
            $table->decimal('manufacturer_followed_offered_price_in_usd', 8, 2)->nullable();

            // Stage 4 (СЦ)
            $table->decimal('agreed_price', 8, 2)->nullable();

            // Stage 5 (КК)
            $table->unsignedSmallInteger('marketing_authorization_holder_id')->nullable();
            $table->string('trademark_en')->nullable();
            $table->string('trademark_ru')->nullable();

            // After Stage 5 (КК)
            $table->decimal('increased_price', 8, 2)->nullable();
            $table->decimal('increased_price_percentage', 8, 2)->nullable();
            $table->date('increased_price_date')->nullable();
            $table->string('dossier_status')->nullable();
            $table->string('clinical_trial_year')->nullable();
            $table->string('clinical_trial_ich_country')->nullable();
            $table->string('down_payment_1')->nullable();
            $table->string('down_payment_2')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processes');
    }
};
