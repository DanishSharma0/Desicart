<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Shopkeeper\ShopkeeperDashboardController;
use App\Http\Controllers\Shopkeeper\ShopkeeperProductController;
use Illuminate\Support\Facades\Route;

// Guest / Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Product Catalog
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/deals', [ProductController::class, 'deals'])->name('products.deals');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::post('/products/{id}/review', [ProductController::class, 'storeReview'])
    ->middleware(['auth'])->name('products.review');

// Cart System (available to guests and users)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/store', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/update/{productId}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/destroy/{productId}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::post('/cart/toggle-save/{productId}', [CartController::class, 'toggleSaveForLater'])->name('cart.toggle-save');

// Checkout Routes (public)
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/store', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

// Authenticated User Routes
Route::middleware(['auth', 'web'])->group(function () {
    // Profile Management (Laravel Breeze default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);



    // Customer Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [DashboardController::class, 'orders'])->name('dashboard.orders');
        
        // Wishlist
        Route::get('/wishlist', [DashboardController::class, 'wishlist'])->name('dashboard.wishlist');
        Route::post('/wishlist/toggle/{productId}', [DashboardController::class, 'toggleWishlist'])->name('dashboard.wishlist.toggle');
        
        // Addresses
        Route::get('/addresses', [DashboardController::class, 'addresses'])->name('dashboard.addresses');
        Route::post('/addresses/store', [DashboardController::class, 'storeAddress'])->name('dashboard.addresses.store');
        Route::delete('/addresses/{id}', [DashboardController::class, 'destroyAddress'])->name('dashboard.addresses.destroy');
    });
});

// Admin Panel (Protected by Auth and Admin Role)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Products CRUD & Inventory Logs
    Route::get('/products/{id}/logs', [AdminProductController::class, 'logs'])->name('products.logs');
    Route::resource('products', AdminProductController::class);
    
    // Categories CRUD
    Route::resource('categories', AdminCategoryController::class);
    
    // Coupons CRUD
    Route::resource('coupons', AdminCouponController::class);
    
    // Order Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('/orders/{id}/payment', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.update-payment');
});

// Shopkeeper Panel (Protected by Auth and Shopkeeper Role)
Route::middleware(['auth', 'role:shopkeeper'])->prefix('shopkeeper')->name('shopkeeper.')->group(function () {
    Route::get('/', [ShopkeeperDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ShopkeeperProductController::class);
});

require __DIR__.'/auth.php';
