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

            $table->string('name')->unique();

            $table->unsignedInteger('process_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('processes');

            $table->date('receive_date');
            $table->date('purchase_order_date');
            $table->unsignedInteger('quantity');
            $table->decimal('price', 8, 2);

            $table->unsignedSmallInteger('currency_id')
                ->nullable()
                ->index()
                ->foreign()
                ->references('id')
                ->on('currencies');

            $table->date('readiness_date');
            $table->date('expected_dispatch_date');

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
