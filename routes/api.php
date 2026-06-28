<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\StockController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Products CRUD
Route::apiResource('products', ProductController::class);

// Warehouses CRUD
Route::apiResource('warehouses', WarehouseController::class);

// Inventory transactions & checks
Route::post('stock/in', [StockController::class, 'stockIn']);
Route::post('stock/out', [StockController::class, 'stockOut']);
Route::get('stock/product/{productId}/warehouse/{warehouseId}', [StockController::class, 'getStockByWarehouse']);
Route::get('stock/product/{productId}', [StockController::class, 'getProductStockAcrossWarehouses']);
