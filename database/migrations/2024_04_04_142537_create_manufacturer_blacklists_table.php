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
        Schema::create('manufacturer_blacklists', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->autoIncrement();
            $table->string('name')->unique();
        });

        Schema::create('manufacturer_manufacturer_blacklist', function (Blueprint $table) {
            $table->unsignedInteger('manufacturer_id');
            $table->unsignedSmallInteger('manufacturer_blacklist_id');
            $table->primary(['manufacturer_id', 'manufacturer_blacklist_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturer_blacklists');
        Schema::dropIfExists('manufacturer_manufacturer_blacklist');
    }
};
