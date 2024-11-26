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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();

            $table->unsignedTinyInteger('invoice_item_type_id') // 'Product', 'Additional payment' or 'Service'
                ->index()
                ->foreign()
                ->references('id')
                ->on('invoice_types');

            $table->unsignedInteger('order_product_id') // Required only for items of 'Product' type
                ->nullable()
                ->index()
                ->foreign()
                ->references('id')
                ->on('order_products');

            $table->string('name')->nullable(); // Required only for items of 'Additional payment' and 'Service' types

            $table->unsignedInteger('quantity');
            $table->decimal('price', 8, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
