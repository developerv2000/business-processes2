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

            $table->unsignedInteger('invoice_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('invoices');

            $table->unsignedTinyInteger('category_id') // 'Product', 'Other payments' or 'Service'
                ->index()
                ->foreign()
                ->references('id')
                ->on('invoice_item_categories');

            $table->unsignedInteger('order_product_id') // Required only for items of 'Product' category
                ->nullable()
                ->index()
                ->foreign()
                ->references('id')
                ->on('order_products');

            $table->string('name')->nullable(); // Required only for items of 'Other payments' and 'Service' category

            $table->unsignedInteger('quantity');
            $table->decimal('amount_paid', 8, 2)->nullable();

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
