<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\StockExitController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->delete('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('product-categories', ProductCategoryController::class);
    Route::resource('products', ProductController::class);
    Route::post('products/{id}', [ProductController::class, 'update']);
    
    Route::resource('warehouses', WarehouseController::class);
    Route::post('warehouses/{id}', [WarehouseController::class, 'update']);
    Route::get('warehouses/inventory/{id}', [WarehouseController::class, 'inventory']);
    
    Route::resource('stock-entries', StockEntryController::class);
    Route::post('stock-entries/{id}', [StockEntryController::class, 'update']);
    Route::patch('stock-entries/confirm/{id}', [StockEntryController::class, 'confirm']);
    
    Route::resource('stock-exits', StockExitController::class);
    Route::post('stock-exits/{id}', [StockExitController::class, 'update']);
    Route::patch('stock-exits/confirm/{id}', [StockExitController::class, 'confirm']);
});
