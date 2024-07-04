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

            $table->boolean('source_eu');
            $table->boolean('source_in');

            $table->string('dosage', 300)->nullable();
            $table->string('pack')->nullable();

            $table->string('additional_search_information', 1000)->nullable();
            $table->unsignedInteger('forecast_year_1')->nullable();
            $table->unsignedInteger('forecast_year_2')->nullable();
            $table->unsignedInteger('forecast_year_3')->nullable();

            $table->unsignedSmallInteger('status_id')
                // ->index() // non-index because already 4 indexes used for this table
                ->foreign()
                ->references('id')
                ->on('kvpp_statuses');

            $table->unsignedSmallInteger('country_code_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('country_codes');

            $table->unsignedSmallInteger('priority_id')
                // ->index() // non-index because already 4 indexes used for this table
                ->foreign()
                ->references('id')
                ->on('kvpp_priorities');

            $table->unsignedSmallInteger('inn_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('inns');

            $table->unsignedSmallInteger('form_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('product_forms');

            $table->unsignedSmallInteger('marketing_authorization_holder_id')
                // ->index() // non-index because already 4 indexes used for this table
                ->foreign()
                ->references('id')
                ->on('marketing_authorization_holders');

            $table->unsignedSmallInteger('portfolio_manager_id')
                ->nullable()
                // ->index() // non-index because already 4 indexes used for this table
                ->foreign()
                ->references('id')
                ->on('portfolio_managers');

            $table->unsignedSmallInteger('analyst_user_id')
                ->nullable()
                ->index()
                ->foreign()
                ->references('id')
                ->on('users');

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
