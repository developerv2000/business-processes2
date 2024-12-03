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
        Schema::create('invoices', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('name');
            $table->timestamp('date');

            $table->unsignedTinyInteger('category_id') // 'Goods' or 'Service'
                ->index()
                ->foreign()
                ->references('id')
                ->on('invoice_categories');

            $table->unsignedTinyInteger('payment_type_id') // 'Prepayment', 'Final payment', or 'Full payment'
                ->index()
                ->foreign()
                ->references('id')
                ->on('invoice_payment_types');

            $table->unsignedSmallInteger('currency_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('currencies');

            $table->unsignedTinyInteger('prepayment_percentage'); // Required only for invoices of 'Prepayment' category
            $table->timestamp('sent_for_payment_date')->nullable(); // Auto
            $table->timestamp('payment_date')->nullable();

            $table->unsignedSmallInteger('payer_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('payers');

            $table->string('group_name')->nullable();
            $table->boolean('cancelled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
