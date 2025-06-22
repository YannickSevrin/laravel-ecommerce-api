<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminProductController;
use App\Http\Controllers\Api\Admin\AdminCategoryController;
use App\Http\Controllers\Api\Admin\AdminOrderController;
use App\Http\Controllers\Api\Admin\AdminUserController;

// Public routes
Route::get('/products', [ShopController::class, 'index']);
Route::get('/products/{product}', [ShopController::class, 'show']);
Route::get('/categories', [ShopController::class, 'categories']);

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
});

// Protected routes for authenticated users
Route::middleware('auth:sanctum')->group(function () {
    
    // User profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::delete('/', [ProfileController::class, 'destroy']);
        
        // Addresses
        Route::get('/addresses', [AddressController::class, 'index']);
        Route::post('/addresses', [AddressController::class, 'store']);
        Route::put('/addresses/{address}', [AddressController::class, 'update']);
        Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);
        Route::post('/addresses/{address}/default', [AddressController::class, 'setDefault']);
    });
    
    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/add/{product}', [CartController::class, 'add']);
        Route::put('/update/{product}', [CartController::class, 'update']);
        Route::delete('/remove/{product}', [CartController::class, 'remove']);
        Route::delete('/clear', [CartController::class, 'clear']);
    });
    
    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::post('/', [CheckoutController::class, 'store']);
    });
});

// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    
    // Admin dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    
    // Product management
    Route::prefix('products')->group(function () {
        Route::get('/', [AdminProductController::class, 'index']);
        Route::post('/', [AdminProductController::class, 'store']);
        Route::get('/{product}', [AdminProductController::class, 'show']);
        Route::put('/{product}', [AdminProductController::class, 'update']);
        Route::delete('/{product}', [AdminProductController::class, 'destroy']);
    });
    
    // Category management
    Route::prefix('categories')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index']);
        Route::post('/', [AdminCategoryController::class, 'store']);
        Route::get('/{category}', [AdminCategoryController::class, 'show']);
        Route::put('/{category}', [AdminCategoryController::class, 'update']);
        Route::delete('/{category}', [AdminCategoryController::class, 'destroy']);
    });
    
    // Order management
    Route::prefix('orders')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index']);
        Route::get('/{order}', [AdminOrderController::class, 'show']);
        Route::put('/{order}', [AdminOrderController::class, 'update']);
    });
    
    // User management
    Route::prefix('users')->group(function () {
        Route::get('/', [AdminUserController::class, 'index']);
        Route::get('/{user}', [AdminUserController::class, 'show']);
        Route::put('/{user}', [AdminUserController::class, 'update']);
    });
}); 