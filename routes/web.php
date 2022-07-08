<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Reports;
use App\Http\Controllers\MasterData;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::middleware(['guest'])->group(function () {
    Route::get('/pages/login', [ApplicationController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'authenticate'])->name('authenticate');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/{any}', [ApplicationController::class, 'index'])->where('any', '.*');
    Route::post('change-password', [AccountController::class, 'changePassword'])->name('change.password');
    Route::post('fetch-order', [Reports::class, 'fetchOrder'])->name('fetch.order');
    Route::post('fetch-store-desktop', [MasterData::class, 'fetchStoreDesktop'])->name('fetch.store.desktop');
    Route::post('add-store-desktop', [MasterData::class, 'addStoreDesktop'])->name('add.store.desktop');
    Route::post('update-store', [MasterData::class, 'updateStore'])->name('update.store');
    Route::post('add-category-desktop', [MasterData::class, 'addCategoryDesktop'])->name('add.category.desktop');
    Route::post('update-category', [MasterData::class, 'updateCategory'])->name('update.category');
});


