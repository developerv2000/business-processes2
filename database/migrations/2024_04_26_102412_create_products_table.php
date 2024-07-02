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
        Schema::create('products', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('brand')->nullable();
            $table->string('dosage', 300)->nullable();
            $table->string('pack')->nullable();
            $table->string('moq')->nullable();

            $table->string('dossier', 1000)->nullable();
            $table->string('bioequivalence', 600)->nullable();
            $table->string('down_payment', 400)->nullable();
            $table->string('validity_period', 400)->nullable();
            $table->boolean('registered_in_eu')->default(0);
            $table->boolean('sold_in_eu')->default(0);

            $table->unsignedInteger('manufacturer_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('manufacturers');

            $table->unsignedMediumInteger('inn_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('inns');

            $table->unsignedSmallInteger('form_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('product_forms');

            $table->unsignedSmallInteger('class_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('product_classes');

            $table->unsignedSmallInteger('shelf_life_id')
                ->index()
                ->foreign()
                ->references('id')
                ->on('product_shelf_lives');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
