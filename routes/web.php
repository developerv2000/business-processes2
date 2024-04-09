<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthenticatedSessionController::class)->group(function () {
    Route::get('login', 'create')->middleware('guest')->name('login');
    Route::post('login', 'store')->middleware('guest');
    Route::post('logout', 'destroy')->middleware('auth')->name('logout');
});

Route::middleware('auth', 'auth.session')->group(function () {
    Route::get('/', [ManufacturerController::class, 'index'])->name('manufacturers.index'); // home

    Route::controller(ProfileController::class)->name('profile.')->group(function () {
        Route::get('profile', 'edit')->name('edit');
        Route::patch('profile', 'update')->name('update');
        Route::patch('password', 'updatePassword')->name('update-password');
    });

    Route::controller(SettingController::class)->name('settings.')->group(function () {
        Route::patch('locale', 'updateLocale')->name('update-locale');
        Route::patch('body-width', 'updateBodyWidth')->name('update-body-width'); // ajax request
        Route::patch('table-columns', 'updateTableColumns')->name('update-table-columns'); // ajax request
    });

    Route::prefix('manufacturers')->controller(ManufacturerController::class)->name('manufacturers.')->group(function () {
        Route::get('/create', 'create')->name('create');
        Route::get('/{item}', 'edit')->name('edit');
        Route::get('/trash', 'trash')->name('trash');

        Route::post('/store', 'store')->name('store');
        Route::patch('{item}', 'update')->name('update');
        Route::delete('/destroy', 'destroy')->name('destroy');
        Route::patch('/restore', 'restore')->name('restore');
        Route::post('/export', 'export')->name('export');
    });
});
