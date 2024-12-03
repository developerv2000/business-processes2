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
        Schema::create('invoice_order', function (Blueprint $table) {
            $table->unsignedSmallInteger('invoice_id')
                ->foreign()
                ->references('id')
                ->on('invoices');

            $table->unsignedInteger('order_id')
                ->foreign()
                ->references('id')
                ->on('orders');

            $table->primary(['invoice_id', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_order');
    }
};
