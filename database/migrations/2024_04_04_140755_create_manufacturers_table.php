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
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('name')->unique();
            $table->string('website')->nullable();
            $table->string('about', 6000)->nullable();
            $table->string('relationship', 6000)->nullable();
            $table->boolean('is_active');
            $table->boolean('is_important')->default(0);
            $table->unsignedMediumInteger('usage_count')->default(0);
            $table->unsignedSmallInteger('bdm_user_id');
            $table->unsignedSmallInteger('analyst_user_id');
            $table->unsignedSmallInteger('country_id');
            $table->unsignedSmallInteger('category_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturers');
    }
};
