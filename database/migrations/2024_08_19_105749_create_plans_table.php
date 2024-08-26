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

            $table->unsignedSmallInteger('January_contract_plan')->default(0);
            $table->unsignedSmallInteger('February_contract_plan')->default(0);
            $table->unsignedSmallInteger('March_contract_plan')->default(0);
            $table->unsignedSmallInteger('April_contract_plan')->default(0);
            $table->unsignedSmallInteger('May_contract_plan')->default(0);
            $table->unsignedSmallInteger('June_contract_plan')->default(0);
            $table->unsignedSmallInteger('July_contract_plan')->default(0);
            $table->unsignedSmallInteger('August_contract_plan')->default(0);
            $table->unsignedSmallInteger('September_contract_plan')->default(0);
            $table->unsignedSmallInteger('October_contract_plan')->default(0);
            $table->unsignedSmallInteger('November_contract_plan')->default(0);
            $table->unsignedSmallInteger('December_contract_plan')->default(0);

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
