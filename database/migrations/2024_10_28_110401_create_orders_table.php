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
        Schema::create('orders', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();

            $table->unsignedInteger('manufacturer_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('manufacturers');

            $table->unsignedSmallInteger('country_code_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('country_codes');

            $table->date('receive_date')->nullable();
            $table->date('purchase_order_date')->nullable();
            $table->string('purchase_order_name')->nullable();

            $table->unsignedSmallInteger('currency_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('currencies');

            $table->date('readiness_date')->nullable();
            $table->date('expected_dispatch_date')->nullable();
            $table->boolean('is_confirmed'); // Auto
            $table->string('file')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
