<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MyOrderController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Profile\AddressController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard (facultatif)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // 🔐 Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 📦 Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // 🧾 My orders routes
    Route::get('/my-orders', [MyOrderController::class, 'index'])->name('my-orders.index');
    Route::get('/my-orders/{order}', [MyOrderController::class, 'show'])->name('my-orders.show');

    // ✅ Checkout routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/thank-you', fn () => view('checkout.thank-you'))->name('checkout.thank-you');

    // 📍 Address management (in profile)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::resource('addresses', AddressController::class)->except(['show', 'edit']);
        Route::post('addresses/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.default');
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/customer.php';
require __DIR__.'/admin.php';