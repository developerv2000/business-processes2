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
        Schema::create('meetings', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();

            $table->unsignedInteger('manufacturer_id')
                ->foreign()
                ->references('id')
                ->on('manufacturers');

            $table->unsignedSmallInteger('year');
            $table->unique(['manufacturer_id', 'year']);

            $table->string('who_met', 600)->nullable();
            $table->string('plan', 2000)->nullable();
            $table->string('topic', 2000)->nullable();
            $table->text('result')->nullable();
            $table->text('outside_the_exhibition')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
