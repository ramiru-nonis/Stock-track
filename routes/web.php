<?php

use App\Http\Middleware\EnsureOwner;
use App\Livewire\CategoriesList;
use App\Livewire\Dashboard;
use App\Livewire\LowStockAlerts;
use App\Livewire\ProductsList;
use App\Livewire\StaffManagement;
use App\Livewire\StockHistory;
use App\Livewire\StockIn;
use App\Livewire\StockOut;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/products', ProductsList::class)->name('products.index');
    Route::get('/products/{product}/image', [App\Http\Controllers\ProductImageController::class, 'show'])->name('products.image');
    Route::get('/categories', CategoriesList::class)->name('categories.index');
    Route::get('/stock-in', StockIn::class)->name('stock.in');
    Route::get('/stock-out', StockOut::class)->name('stock.out');
    Route::get('/low-stock', LowStockAlerts::class)->name('low-stock');

    // Owner-Only Administration Routes
    Route::middleware([EnsureOwner::class])->group(function () {
        Route::get('/stock-history', StockHistory::class)->name('stock.history');
        Route::get('/staff', StaffManagement::class)->name('staff.index');
    });
});
