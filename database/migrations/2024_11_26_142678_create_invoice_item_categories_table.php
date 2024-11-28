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
        Schema::create('invoice_item_categories', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->autoIncrement();
            // 'Product' and 'Other payments' for invoices of 'Goods' category
            // 'Service' for invoices of 'Service' category
            $table->string('name')->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_item_categories');
    }
};
