<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\StaffApiController;
use App\Http\Controllers\Api\StockMovementApiController;
use Illuminate\Support\Facades\Route;

// Public Auth Endpoints
Route::post('/login', [AuthController::class, 'issueToken']);
Route::post('/sanctum/token', [AuthController::class, 'issueToken']);

// Protected REST API Endpoints (Sanctum Authenticated)
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth Token & User Info
    Route::post('/logout', [AuthController::class, 'revokeToken']);
    Route::get('/me', [AuthController::class, 'me']);

    // Products Endpoints
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::post('/products', [ProductApiController::class, 'store']);
    Route::get('/products/{product}', [ProductApiController::class, 'show']);
    Route::put('/products/{product}', [ProductApiController::class, 'update']);
    Route::delete('/products/{product}', [ProductApiController::class, 'destroy']);
    Route::get('/low-stock', [ProductApiController::class, 'lowStock']);

    // Categories Endpoints
    Route::get('/categories', [CategoryApiController::class, 'index']);
    Route::post('/categories', [CategoryApiController::class, 'store']);
    Route::get('/categories/{category}', [CategoryApiController::class, 'show']);
    Route::put('/categories/{category}', [CategoryApiController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryApiController::class, 'destroy']);

    // Stock Movements Endpoints
    Route::get('/stock-movements', [StockMovementApiController::class, 'index']);
    Route::post('/stock-movements', [StockMovementApiController::class, 'store']);

    // Staff Management Endpoints (Owner Administration)
    Route::get('/staff', [StaffApiController::class, 'index']);
    Route::post('/staff', [StaffApiController::class, 'store']);
    Route::delete('/staff/{user}', [StaffApiController::class, 'destroy']);
});
