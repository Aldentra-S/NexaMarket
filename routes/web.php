<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\RefundController as AdminRefundController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ShopApprovalController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Buyer\DashboardController as BuyerDashboardController;
use App\Http\Controllers\Buyer\ShopOnboardingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductCatalogController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\ProductImageController as SellerProductImageController;
use App\Http\Controllers\Seller\ReviewController as SellerReviewController;
use App\Http\Controllers\Seller\ShopSettingsController as SellerShopSettingsController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Public Marketplace Catalog Routes
Route::get('/products', [ProductCatalogController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductCatalogController::class, 'show'])->name('products.show');

// Shopping Cart Routes
Route::middleware(['auth'])->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/items', [CartController::class, 'store'])->name('items.store');
    Route::patch('/items/{item}', [CartController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [CartController::class, 'destroy'])->name('items.destroy');
});

// Checkout Routes
Route::middleware(['auth'])->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'create'])->name('create');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
});

// Buyer Order History & Payment Routes
Route::middleware(['auth'])->prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::post('/{order}/payment/simulate', [PaymentController::class, 'simulate'])->name('payment.simulate');
    Route::post('/{order}/payment/snap', [PaymentController::class, 'createSnapToken'])->name('payment.snap');
});

// Midtrans Webhook Notification Route (Public Webhook & CSRF Exempt)
Route::post('payments/midtrans/notification', [PaymentController::class, 'handleNotification'])->name('payments.midtrans.notification');
Route::post('api/payments/midtrans/notification', [PaymentController::class, 'handleNotification'])->name('api.payments.midtrans.notification');

Route::get('dashboard', [BuyerDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::get('profile', function () {
    if (auth()->user()?->isAdmin()) {
        return redirect()->route('admin.profile');
    }
    return view('profile');
})->middleware(['auth'])->name('profile');

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

    // Order Fulfillment Management
    Route::get('orders', [SellerOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{shopOrder}', [SellerOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{shopOrder}/status', [SellerOrderController::class, 'updateStatus'])->name('orders.status.update');

    // Shop Settings & Profile
    Route::get('shop/settings', [SellerShopSettingsController::class, 'edit'])->name('shop.settings');
    Route::put('shop/settings', [SellerShopSettingsController::class, 'update'])->name('shop.settings.update');

    // Product Reviews Overview
    Route::get('reviews', [SellerReviewController::class, 'index'])->name('reviews.index');
});

// Admin Routes (Shop Approval Center & Administrative Controls)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Shop Approval Center
    Route::get('/shops', [ShopApprovalController::class, 'index'])->name('shops.index');
    Route::get('/shops/{shop}', [ShopApprovalController::class, 'show'])->name('shops.show');
    Route::post('/shops/{shop}/approve', [ShopApprovalController::class, 'approve'])->name('shops.approve');
    Route::post('/shops/{shop}/reject', [ShopApprovalController::class, 'reject'])->name('shops.reject');
    Route::post('/shops/{shop}/suspend', [ShopApprovalController::class, 'suspend'])->name('shops.suspend');
    Route::post('/shops/{shop}/reactivate', [ShopApprovalController::class, 'reactivate'])->name('shops.reactivate');

    // Platform User Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');

    // Global Product Oversight
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');

    // Platform Order Oversight
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');

    // Gateway Payment Records
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');

    // Refunds & Cancellations Oversight
    Route::get('/refunds', [AdminRefundController::class, 'index'])->name('refunds.index');

    // Admin Category Management
    Route::resource('categories', AdminCategoryController::class);

    // Business Intelligence & Platform Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/csv', [AdminReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('/reports/export/pdf', [AdminReportController::class, 'exportPdf'])->name('reports.export.pdf');

    // Admin Profile
    Route::view('/profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
