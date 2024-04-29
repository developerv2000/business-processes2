<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Support\RouteGenerator;
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

    Route::prefix('comments')->controller(CommentController::class)->name('comments.')->group(function () {
        Route::get('/view/{commentable_type}/{commentable_id}', 'index')->name('index');
        RouteGenerator::defineDefaultCrudRoutesOnly(['edit', 'store', 'update', 'destroy']);
    });

    Route::prefix('manufacturers')->controller(ManufacturerController::class)->name('manufacturers.')->group(function () {
        RouteGenerator::defineDefaultCrudRoutesExcept(['index']);
    });

    Route::prefix('products')->controller(ProductController::class)->name('products.')->group(function () {
        RouteGenerator::defineAllDefaultCrudRoutes();
        Route::post('/export-vp', 'exportVp')->name('export-vp');
    });
});
