<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\StockMovementApiController;
use Illuminate\Support\Facades\Route;

// Public Auth Endpoints
Route::post('/login', [AuthController::class, 'issueToken']);
Route::post('/sanctum/token', [AuthController::class, 'issueToken']);

// Protected API Endpoints
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'revokeToken']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/{product}', [ProductApiController::class, 'show']);
    Route::get('/low-stock', [ProductApiController::class, 'lowStock']);

    Route::get('/stock-movements', [StockMovementApiController::class, 'index']);
    Route::post('/stock-movements', [StockMovementApiController::class, 'store']);
});
