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
            $table->unsignedSmallInteger('id')->autoIncrement();

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

            $table->primary(['id', 'country_code_id', 'plan_id', 'marketing_authorization_holder_id']);

            // Europe contract plans
            $table->unsignedSmallInteger('January_europe_contract_plan')->default(0);
            $table->unsignedSmallInteger('February_europe_contract_plan')->default(0);
            $table->unsignedSmallInteger('March_europe_contract_plan')->default(0);
            $table->unsignedSmallInteger('April_europe_contract_plan')->default(0);
            $table->unsignedSmallInteger('May_europe_contract_plan')->default(0);
            $table->unsignedSmallInteger('June_europe_contract_plan')->default(0);
            $table->unsignedSmallInteger('July_europe_contract_plan')->default(0);
            $table->unsignedSmallInteger('August_europe_contract_plan')->default(0);
            $table->unsignedSmallInteger('September_europe_contract_plan')->default(0);
            $table->unsignedSmallInteger('October_europe_contract_plan')->default(0);
            $table->unsignedSmallInteger('November_europe_contract_plan')->default(0);
            $table->unsignedSmallInteger('December_europe_contract_plan')->default(0);

            // India contract plans
            $table->unsignedSmallInteger('January_india_contract_plan')->default(0);
            $table->unsignedSmallInteger('February_india_contract_plan')->default(0);
            $table->unsignedSmallInteger('March_india_contract_plan')->default(0);
            $table->unsignedSmallInteger('April_india_contract_plan')->default(0);
            $table->unsignedSmallInteger('May_india_contract_plan')->default(0);
            $table->unsignedSmallInteger('June_india_contract_plan')->default(0);
            $table->unsignedSmallInteger('July_india_contract_plan')->default(0);
            $table->unsignedSmallInteger('August_india_contract_plan')->default(0);
            $table->unsignedSmallInteger('September_india_contract_plan')->default(0);
            $table->unsignedSmallInteger('October_india_contract_plan')->default(0);
            $table->unsignedSmallInteger('November_india_contract_plan')->default(0);
            $table->unsignedSmallInteger('December_india_contract_plan')->default(0);

            // Comments
            $table->text('January_comment')->nullable();
            $table->text('February_comment')->nullable();
            $table->text('March_comment')->nullable();
            $table->text('April_comment')->nullable();
            $table->text('May_comment')->nullable();
            $table->text('June_comment')->nullable();
            $table->text('July_comment')->nullable();
            $table->text('August_comment')->nullable();
            $table->text('September_comment')->nullable();
            $table->text('October_comment')->nullable();
            $table->text('November_comment')->nullable();
            $table->text('December_comment')->nullable();
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
