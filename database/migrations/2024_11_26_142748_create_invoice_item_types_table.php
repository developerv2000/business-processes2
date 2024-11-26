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
        Schema::create('invoice_item_types', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->autoIncrement();
            // 'Product' and 'Additional payment' for invoices of 'Goods' type
            // 'Service' for invoices of 'Service' type
            $table->string('name')->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_item_types');
    }
};
