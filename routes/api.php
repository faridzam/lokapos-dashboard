<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Reports;
use App\Http\Controllers\MasterData;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//reports
Route::get('/get-stores', [Reports::class, 'getStores'])->name('get.stores');
Route::get('/get-stores-mobile', [Reports::class, 'getStoresMobile'])->name('get.stores.mobile');
Route::get('/get-order-data', [Reports::class, 'getOrderData'])->name('get.order.data');
Route::get('get-exist-order-id', [Reports::class, 'getExistOrderId'])->name('get.exist.order.id');
Route::get('get-sales-order/{store}/{dateStart}/{dateEnd}', [Reports::class, 'getSalesOrder'])->name('get.sales.order');
Route::get('get-sales-order-mobile/{store}/{dateStart}/{dateEnd}', [Reports::class, 'getSalesOrderMobile'])->name('get.sales.order.mobile');
Route::get('get-item-sales-order/{store}/{dateStart}/{dateEnd}', [Reports::class, 'getItemSalesOrder'])->name('get.item.sales.order');
Route::get('get-item-sales-order-mobile/{store}/{dateStart}/{dateEnd}', [Reports::class, 'getItemSalesOrderMobile'])->name('get.item.sales.order.mobile');
Route::get('export-sales/{store}/{dateStart}/{dateEnd}', [Reports::class, 'exportSales'])->name('export.sales');
Route::get('export-sales-mobile/{store}/{dateStart}/{dateEnd}', [Reports::class, 'exportSalesMobile'])->name('export.sales.mobile');
Route::get('export-item-sales/{store}/{dateStart}/{dateEnd}', [Reports::class, 'exportItemSales'])->name('export.item.sales');
Route::get('export-item-sales-mobile/{store}/{dateStart}/{dateEnd}', [Reports::class, 'exportItemSalesMobile'])->name('export.item.sales.mobile');

//master-data
Route::get('get-data-stores', [MasterData::class, 'getDataStores'])->name('get.data.stores');
Route::get('get-exist-desktop-store-id', [MasterData::class, 'getStoreDesktopID'])->name('get.exist..desktop.store.id');
Route::get('get-data-categories', [MasterData::class, 'getDataCategories'])->name('get.data.categories');
Route::get('get-data-products', [MasterData::class, 'getDataProducts'])->name('get.data.products');