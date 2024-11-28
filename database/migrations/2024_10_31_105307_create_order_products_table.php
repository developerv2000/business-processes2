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
        Schema::create('order_products', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();

            $table->unsignedInteger('order_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('orders');

            $table->unsignedInteger('process_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('processes');

            $table->unsignedSmallInteger('marketing_authorization_holder_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('marketing_authorization_holders');

            $table->unsignedInteger('quantity');
            $table->decimal('price', 8, 2);

            // Filled ONCE while creating invoices of 'Prepayment' or 'Full payment' types
            $table->decimal('invoice_price', 8, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
