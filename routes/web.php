<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\KvppController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ProcessStatusHistoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSelectionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\TemplatedModelController;
use App\Http\Controllers\UserController;
use App\Support\RouteGenerator;
use Illuminate\Support\Facades\Route;

Route::controller(AuthenticatedSessionController::class)->group(function () {
    Route::get('login', 'create')->middleware('guest')->name('login');
    Route::post('login', 'store')->middleware('guest');
    Route::post('logout', 'destroy')->middleware('auth')->name('logout');
});

Route::middleware('auth', 'auth.session')->group(function () {
    Route::get('/', [ManufacturerController::class, 'index'])->name('manufacturers.index'); // home page
    Route::get('/statistics', [StatisticController::class, 'index'])->name('statistics.index');

    Route::controller(ProfileController::class)->name('profile.')->group(function () {
        Route::get('profile', 'edit')->name('edit');
        Route::patch('profile', 'update')->name('update');
        Route::patch('password', 'updatePassword')->name('update-password');
    });

    Route::controller(SettingController::class)->name('settings.')->group(function () {
        Route::patch('theme', 'toggleTheme')->name('toggle-theme');
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
        Route::post('/get-similar-records', 'getSimilarRecords');  // ajax request on create form for uniqness
    });

    Route::prefix('products-selection')->controller(ProductSelectionController::class)->name('products-selection.')->group(function () {
        RouteGenerator::defineDefaultCrudRoutesOnly(['export']);
    });

    Route::prefix('processes')->controller(ProcessController::class)->name('processes.')->group(function () {
        RouteGenerator::defineAllDefaultCrudRoutes();

        Route::get('/duplicate/{instance}', 'duplication')->name('duplication');
        Route::post('/duplicate', 'duplicate')->name('duplicate');

        Route::post('/get-create-form-stage-inputs', 'getCreateFormStageInputs');  // ajax request on create form status select change
        Route::post('/get-create-form-forecast-inputs', 'getCreateFormForecastInputs');  // ajax request on create form search countries change
        Route::post('/get-edit-form-stage-inputs', 'getEditFormStageInputs');  // ajax request on edit form status select change
    });

    Route::prefix('process/{process}/status-history')
        ->controller(ProcessStatusHistoryController::class)
        ->name('process-status-history.')
        ->middleware('role:admin,moderator')
        ->group(function () {
            RouteGenerator::defineDefaultCrudRoutesOnly(['index', 'edit', 'update', 'destroy']);
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

    Route::prefix('templated-models')->controller(TemplatedModelController::class)->name('templated-models.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{modelName}', 'show')->name('show');
        Route::get('/{modelName}/create', 'create')->name('create');
        Route::get('/{modelName}/edit/{id}', 'edit')->name('edit');

        Route::post('{modelName}/store', 'store')->name('store');
        Route::patch('{modelName}/update/{id}', 'update')->name('update');
        Route::delete('{modelName}/destroy', 'destroy')->name('destroy');
    });

    Route::prefix('notifications')->controller(NotificationController::class)->name('notifications.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::patch('/mark-as-read', 'markAsRead')->name('mark-as-read');
        Route::delete('/destroy', 'destroy')->name('destroy');
    });

    Route::prefix('model-attachments')->controller(AttachmentController::class)->name('attachments.')->group(function () {
        Route::get('/{modelName}/{modelID}', 'index')->name('index');
        Route::patch('/mark-as-read', 'markAsRead')->name('mark-as-read');
        Route::delete('/destroy', 'destroy')->name('destroy');
    });
});
