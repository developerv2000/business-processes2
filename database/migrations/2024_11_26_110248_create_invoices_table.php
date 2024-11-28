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

            $table->unsignedTinyInteger('invoice_category_id') // 'Goods' or 'Service'
                ->index()
                ->foreign()
                ->references('id')
                ->on('invoice_categories');

            $table->unsignedTinyInteger('invoice_payment_type_id') // 'Prepayment', 'Interim payment', 'Final payment', or 'Full payment'
                ->index()
                ->foreign()
                ->references('id')
                ->on('invoice_payment_types');

            $table->unsignedInteger('order_id')
                ->nullable() // nullable for invoices of 'Service' category
                ->index()
                ->foreign()
                ->references('id')
                ->on('orders');

            $table->unsignedSmallInteger('currency_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('currencies');

            $table->unsignedTinyInteger('payment_percentage'); // 100% for items of 'Full payment' category
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
