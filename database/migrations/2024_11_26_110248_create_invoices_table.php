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

            $table->unsignedTinyInteger('invoice_type_id') // 'Goods' or 'Service'
                ->index()
                ->foreign()
                ->references('id')
                ->on('invoice_types');

            $table->unsignedInteger('order_id')
                ->nullable() // nullable for invoices of 'Service' type
                ->index()
                ->foreign()
                ->references('id')
                ->on('orders');

            $table->unsignedSmallInteger('currency_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('currencies');

            $table->unsignedTinyInteger('prepayment_percentage')->nullable();
            $table->timestamp('sending_for_payment_date')->nullable(); // Auto
            $table->decimal('amount_paid', 8, 2)->nullable();
            $table->timestamp('payment_date')->nullable();

            $table->unsignedSmallInteger('payer_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('payers');

            $table->timestamp('payment_refer')->nullable();
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
