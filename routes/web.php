<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ShopApprovalController;
use App\Http\Controllers\Buyer\ShopOnboardingController;
use App\Http\Controllers\ProductCatalogController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\ProductImageController as SellerProductImageController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Public Marketplace Catalog Routes
Route::get('/products', [ProductCatalogController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductCatalogController::class, 'show'])->name('products.show');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Buyer Routes (Shop Onboarding & Shopping Area)
Route::middleware(['auth', 'role:buyer,seller'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/area', fn () => response()->json(['status' => 'ok', 'message' => 'Buyer Area Access Granted']))->name('area');
    Route::get('/shop/apply', [ShopOnboardingController::class, 'create'])->name('shop.apply');
    Route::post('/shop/apply', [ShopOnboardingController::class, 'store'])->name('shop.store');
});

// Seller Routes (Protected by role seller AND approved shop)
Route::middleware(['auth', 'role:seller', 'shop.approved'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', SellerProductController::class);

    // Product Images Management
    Route::post('products/{product}/images', [SellerProductImageController::class, 'store'])->name('products.images.store');
    Route::put('products/{product}/images/{image}', [SellerProductImageController::class, 'update'])->name('products.images.update');
    Route::patch('products/{product}/images/{image}/primary', [SellerProductImageController::class, 'setPrimary'])->name('products.images.primary');
    Route::patch('products/{product}/images/reorder', [SellerProductImageController::class, 'reorder'])->name('products.images.reorder');
    Route::delete('products/{product}/images/{image}', [SellerProductImageController::class, 'destroy'])->name('products.images.destroy');
});

// Admin Routes (Shop Approval Center & Administrative Controls)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [ShopApprovalController::class, 'index'])->name('dashboard');
    Route::get('/shops', [ShopApprovalController::class, 'index'])->name('shops.index');
    Route::get('/shops/{shop}', [ShopApprovalController::class, 'show'])->name('shops.show');
    Route::post('/shops/{shop}/approve', [ShopApprovalController::class, 'approve'])->name('shops.approve');
    Route::post('/shops/{shop}/reject', [ShopApprovalController::class, 'reject'])->name('shops.reject');
    Route::post('/shops/{shop}/suspend', [ShopApprovalController::class, 'suspend'])->name('shops.suspend');
    Route::post('/shops/{shop}/reactivate', [ShopApprovalController::class, 'reactivate'])->name('shops.reactivate');

    // Admin Category Management
    Route::resource('categories', AdminCategoryController::class);
});

require __DIR__.'/auth.php';
