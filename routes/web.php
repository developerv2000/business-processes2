<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth', 'auth.session')->group(function () {
    Route::get('/', function (Request $request) {
        return 'Home page';
    });
});

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// Route::middleware('auth', 'auth.session')->group(function () {
//     Route::get('/', [ManufacturerController::class, 'index'])->name('manufacturers.index');

//     Route::prefix('manufacturers')->controller(ManufacturerController::class)->name('manufacturers.')->group(function () {
//         Route::get('/create', 'create')->name('create');
//         Route::get('/edit/{item}', 'edit')->name('edit');
//         Route::get('/trash', 'trash')->name('trash');

//         Route::post('/store', 'store')->name('store');
//         Route::patch('/update/{item}', 'update')->name('update');
//         Route::delete('/destroy', 'destroy')->name('destroy');
//         Route::patch('/restore', 'restore')->name('restore');
//         Route::post('/export', 'export')->name('export');
//     });
// });

require __DIR__.'/auth.php';
