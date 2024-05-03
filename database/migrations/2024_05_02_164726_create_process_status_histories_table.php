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
        Schema::create('process_status_histories', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('process_id');
            $table->unsignedSmallInteger('status_id');
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->unsignedSmallInteger('duration_days')->nullable();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_status_histories');
    }
};
