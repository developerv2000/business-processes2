<?php

use App\Models\Currency;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('users:reset-settings', function () {
    User::resetDefaultSettingsForAll();
    $this->info('All user settings have been reset!');
})->purpose("Reset all user settings");

Schedule::command('telescope:prune')->daily();

Schedule::call(function () {
    Currency::updateExchangeRatesExceptUSD();
})->daily();
