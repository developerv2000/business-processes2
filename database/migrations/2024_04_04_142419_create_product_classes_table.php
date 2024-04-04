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
        Schema::create('product_classes', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->autoIncrement();
            $table->string('name')->unique();
        });

        Schema::create('manufacturer_product_class', function (Blueprint $table) {
            $table->unsignedInteger('manufacturer_id');
            $table->unsignedSmallInteger('product_class_id');
            $table->primary(['manufacturer_id', 'product_class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_classes');
        Schema::dropIfExists('manufacturer_product_class');
    }
};
