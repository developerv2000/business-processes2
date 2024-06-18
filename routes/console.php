<?php

use App\Models\Currency;
use App\Models\Kvpp;
use App\Models\Process;
use App\Models\Product;
use App\Models\User;
use App\Support\Helper;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('telescope:prune')->daily();

Schedule::call(function () {
    Currency::updateExchangeRatesExceptUSD();
    Process::updateAllManufacturerPricesInUSD();
})->daily();

Artisan::command('users:reset-settings', function () {
    User::resetDefaultSettingsForAll();
    $this->info('All user settings have been reset!');
})->purpose("Reset all user settings");

Artisan::command('validate-dosage-and-packs', function () {
    Product::all()->each(function ($instance) {
        $instance->dosage = Helper::formatSpecificString($instance->dosage);
        $instance->pack = Helper::formatSpecificString($instance->pack);
        $instance->timestamps = false;
        $instance->saveQuietly();
    });

    Kvpp::all()->each(function ($instance) {
        $instance->dosage = Helper::formatSpecificString($instance->dosage);
        $instance->pack = Helper::formatSpecificString($instance->pack);
        $instance->timestamps = false;
        $instance->saveQuietly();
        $this->info('Dosages and packs have been updated!');
    });
})->purpose("Validate dosages and packs for Product & KVPP tables");
