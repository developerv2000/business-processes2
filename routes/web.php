<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\KvppController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\UserController;
use App\Support\RouteGenerator;
use Illuminate\Support\Facades\Route;

Route::controller(AuthenticatedSessionController::class)->group(function () {
    Route::get('login', 'create')->middleware('guest')->name('login');
    Route::post('login', 'store')->middleware('guest');
    Route::post('logout', 'destroy')->middleware('auth')->name('logout');
});

Route::middleware('auth', 'auth.session')->group(function () {
    Route::get('/', [StatisticController::class, 'index'])->name('statistics.index'); // home page

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
        RouteGenerator::defineAllDefaultCrudRoutes();
    });

    Route::prefix('products')->controller(ProductController::class)->name('products.')->group(function () {
        RouteGenerator::defineAllDefaultCrudRoutes();
        Route::post('/export-vp', 'exportVp')->name('export-vp');
        Route::post('/get-similar-records', 'getSimilarRecords');  // ajax request on create form for uniqness
    });

    Route::prefix('processes')->controller(ProcessController::class)->name('processes.')->group(function () {
        RouteGenerator::defineAllDefaultCrudRoutes();
    });

    Route::prefix('kvpp')->controller(KvppController::class)->name('kvpp.')->group(function () {
        RouteGenerator::defineAllDefaultCrudRoutes();
        Route::post('/get-similar-records', 'getSimilarRecords');  // ajax request on create form for uniqness
    });

    Route::prefix('meetings')->controller(MeetingController::class)->name('meetings.')->group(function () {
        RouteGenerator::defineAllDefaultCrudRoutes();
    });

    Route::prefix('users')->controller(UserController::class)->name('users.')->group(function () {
        RouteGenerator::defineDefaultCrudRoutesExcept(['trash', 'restore', 'export']);
        Route::patch('/update-password/{instance}', 'updatePassword')->name('update-password');
    });
});
