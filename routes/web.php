<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ForOrderController;
use App\Http\Controllers\KvppController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ProcessesForOrderController;
use App\Http\Controllers\ProcessStatusHistoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSelectionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
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
    Route::get('/', [MainController::class, 'redirectToHomePage'])->name('home');

    Route::get('/statistics', [StatisticController::class, 'index'])->name('statistics.index')->middleware('can:view-kpe');
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index')->middleware('can:view-roles');

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
        RouteGenerator::defineDefaultCrudRoutesOnly(['edit', 'store', 'update', 'destroy'], null, 'can:edit-comments');
    });

    Route::prefix('manufacturers')->controller(ManufacturerController::class)->name('manufacturers.')->group(function () {
        RouteGenerator::defineAllDefaultCrudRoutes('can:view-epp', 'can:edit-epp');
    });

    Route::prefix('products')->controller(ProductController::class)->name('products.')->group(function () {
        RouteGenerator::defineAllDefaultCrudRoutes('can:view-ivp', 'can:edit-ivp');
        Route::post('/get-similar-records', 'getSimilarRecords');  // ajax request on create form for uniqness
    });

    Route::prefix('products-selection')->controller(ProductSelectionController::class)->name('products-selection.')->group(function () {
        RouteGenerator::defineDefaultCrudRoutesOnly(['export']);
    });

    Route::prefix('processes')->controller(ProcessController::class)->name('processes.')->group(function () {
        RouteGenerator::defineAllDefaultCrudRoutes('can:view-vps', 'can:edit-vps');

        Route::get('/duplicate/{instance}', 'duplication')->name('duplication')->middleware('can:edit-vps');
        Route::post('/duplicate', 'duplicate')->name('duplicate')->middleware('can:edit-vps');

        Route::post('/get-create-form-stage-inputs', 'getCreateFormStageInputs');  // ajax request on create form status select change
        Route::post('/get-create-form-forecast-inputs', 'getCreateFormForecastInputs');  // ajax request on create form search countries change
        Route::post('/get-edit-form-stage-inputs', 'getEditFormStageInputs');  // ajax request on edit form status select change

        // ajax request on checkbox toggle
        Route::post('/update-contracted-in-plan-value', 'updateContractedInPlanValue')->middleware('can:control-spg-processes');
        // ajax request on checkbox toggle
        Route::post('/update-registered-in-plan-value', 'updateRegisteredInPlanValue')->middleware('can:control-spg-processes');
        // ajax request on checkbox check
        Route::post('/mark-as-ready-for-order', 'markAsReadyForOrder')->middleware('can:mark-process-as-ready-for-order');
    });

    Route::prefix('process/{process}/status-history')
        ->controller(ProcessStatusHistoryController::class)
        ->name('process-status-history.')
        ->middleware('can:edit-processes-status-history')
        ->group(function () {
            RouteGenerator::defineDefaultCrudRoutesOnly(['index', 'edit', 'update', 'destroy']);
        });

    Route::prefix('kvpp')->controller(KvppController::class)->name('kvpp.')->group(function () {
        RouteGenerator::defineAllDefaultCrudRoutes('can:view-kvpp', 'can:edit-kvpp');
        Route::post('/get-similar-records', 'getSimilarRecords');  // ajax request on create form for uniqness
    });

    Route::prefix('meetings')->controller(MeetingController::class)->name('meetings.')->group(function () {
        RouteGenerator::defineAllDefaultCrudRoutes('can:view-meetings', 'can:edit-meetings');
    });

    Route::prefix('users')->controller(UserController::class)->name('users.')->group(function () {
        RouteGenerator::defineDefaultCrudRoutesExcept(['trash', 'restore', 'export'], 'can:view-users', 'can:edit-users');
        Route::patch('/update-password/{instance}', 'updatePassword')->name('update-password')->middleware('can:edit-users');
        Route::patch('/update-permissions/{instance}', 'updatePermissions')->name('update-permissions')->middleware('can:edit-users');
    });

    Route::prefix('templated-models')->controller(TemplatedModelController::class)->name('templated-models.')->group(function () {
        Route::get('/', 'index')->name('index')->middleware('can:view-differents');
        Route::get('/{modelName}', 'show')->name('show')->middleware('can:view-differents');
        Route::get('/{modelName}/create', 'create')->name('create')->middleware('can:edit-differents');
        Route::get('/{modelName}/edit/{id}', 'edit')->name('edit')->middleware('can:edit-differents');

        Route::post('{modelName}/store', 'store')->name('store')->middleware('can:edit-differents');
        Route::patch('{modelName}/update/{id}', 'update')->name('update')->middleware('can:edit-differents');
        Route::delete('{modelName}/destroy', 'destroy')->name('destroy')->middleware('can:edit-differents');
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

    Route::prefix('plan')->controller(PlanController::class)->name('plan.')->group(function () {
        RouteGenerator::defineDefaultCrudRoutesExcept(['trash', 'restore', 'export'], 'can:view-spg', 'can:edit-spg');

        Route::get('/show/{plan:year}', 'show')->name('show')->middleware('can:view-spg');

        // Country codes
        Route::prefix('/{plan}/country-codes')->name('country.codes.')->middleware('can:edit-spg')->group(function () {
            Route::get('/index', 'countryCodesIndex')->name('index');
            Route::get('/create', 'countryCodesCreate')->name('create');
            // Route::get('/edit/{countryCode}', 'countryCodesEdit')->name('edit'); // removed

            Route::post('/store', 'countryCodesStore')->name('store');
            // Route::patch('/update/{countryCode}', 'countryCodesUpdate')->name('update'); // removed
            Route::delete('/destroy', 'countryCodesDestroy')->name('destroy');
        });

        // Maarketing authorization holders
        Route::prefix('/{plan}/country-codes/{countryCode}/marketing-authorization-holders')->name('marketing.authorization.holders.')->middleware('can:edit-spg')->group(function () {
            Route::get('/index', 'MAHsIndex')->name('index');
            Route::get('/create', 'MAHsCreate')->name('create');
            Route::get('/edit/{marketingAuthorizationHolder}', 'MAHsEdit')->name('edit');

            Route::post('/store', 'MAHsStore')->name('store');
            Route::patch('/update/{marketingAuthorizationHolder}', 'MAHsUpdate')->name('update');
            Route::delete('/destroy', 'MAHsDestroy')->name('destroy');
        });
    });

    Route::prefix('processes-for-order')->controller(ProcessesForOrderController::class)->name('processes_for_order.')->group(function () {
        RouteGenerator::defineDefaultCrudRoutesOnly(['index', 'edit', 'update'], 'can:view-processes-for-order', 'can:edit-processes-for-order');
    });

    Route::prefix('orders')->controller(OrderController::class)->name('orders.')->group(function () {
        RouteGenerator::defineDefaultCrudRoutesExcept(['create'], 'can:view-orders', 'can:edit-orders');
    });
});
