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
            $table->unsignedInteger('manufacturer_id');
            $table->unsignedMediumInteger('inn_id');
            $table->string('brand')->nullable();
            $table->unsignedSmallInteger('form_id');
            $table->unsignedSmallInteger('class_id');
            $table->string('dosage', 300)->nullable();
            $table->string('pack')->nullable();
            $table->string('moq')->nullable();
            $table->unsignedSmallInteger('shelf_life_id');
            $table->string('dossier', 1000)->nullable();
            $table->string('bioequivalence', 600)->nullable();
            $table->string('down_payment', 400)->nullable();
            $table->string('validity_period', 400)->nullable();
            $table->boolean('registered_in_eu')->default(0);
            $table->boolean('sold_in_eu')->default(0);
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
