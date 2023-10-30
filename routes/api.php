<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
<<<<<<< Updated upstream
use App\Http\Controllers\Authentication;
=======
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
>>>>>>> Stashed changes

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

<<<<<<< Updated upstream
Route::middleware('auth:sanctum')->delete('/logout', [Authentication::class, 'logout']);

Route::post('/login', [Authentication::class, 'login']);
Route::post('/register', [Authentication::class, 'register']);
=======
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->delete('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('product-categories', ProductCategoryController::class);
    Route::resource('products', ProductController::class);
});
>>>>>>> Stashed changes
