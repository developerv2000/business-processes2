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
            // Globals
            $table->unsignedInteger('id')->autoIncrement(); // auto
            $table->unsignedInteger('product_id'); // auto
            $table->unsignedSmallInteger('status_id'); // required
            $table->date('status_update_date'); // auto, but also can be set manually only on create

            // Stage 1 (ВП)
            $table->unsignedSmallInteger('country_code_id'); // required and immutable after stage 1
            $table->date('responsible_people_update_date'); // auto

            // Stage 2 (ПО)
            $table->unsignedInteger('forecast_year_1')->nullable(); // required
            $table->unsignedInteger('forecast_year_2')->nullable(); // required
            $table->unsignedInteger('forecast_year_3')->nullable(); // required
            $table->date('forecast_year_1_update_date')->nullable(); // auto

            $table->string('dossier_status')->nullable(); // nullable until the end
            $table->string('clinical_trial_year')->nullable(); // nullable until the end
            $table->string('clinical_trial_ich_country')->nullable(); // nullable until the end
            $table->string('down_payment_1')->nullable(); // nullable until the end
            $table->string('down_payment_2')->nullable(); // nullable until the end
            $table->string('down_payment_condition')->nullable(); // nullable until the end

            // Stage 3 (АЦ)
            $table->unsignedSmallInteger('currency_id')->nullable(); // required
            $table->decimal('manufacturer_first_offered_price', 8, 2)->nullable(); // required
            $table->decimal('manufacturer_followed_offered_price', 8, 2)->nullable(); // required
            $table->decimal('manufacturer_followed_offered_price_in_usd', 8, 2)->nullable(); // auto
            $table->decimal('our_first_offered_price', 8, 2)->nullable(); // required
            $table->decimal('our_followed_offered_price', 8, 2)->nullable(); // required

            $table->unsignedSmallInteger('marketing_authorization_holder_id')->nullable(); // nullable at stages (3, 4) and became required at stage 5
            $table->string('trademark_en')->nullable(); // nullable at stages (3, 4) and became required at stage 5
            $table->string('trademark_ru')->nullable(); // nullable at stages (3, 4) and became required at stage 5

            // Stage 5 (КК)
            $table->decimal('agreed_price', 8, 2)->nullable(); // required

            // Stage 6 (КД) until the end
            $table->decimal('increased_price', 8, 2)->nullable(); // nullable
            $table->decimal('increased_price_percentage', 8, 2)->nullable(); // auto
            $table->date('increased_price_date')->nullable(); // auto

            // Globals
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
