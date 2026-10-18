<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Role-Protected Route Boundaries (for Phase 1 verification and subsequent phases)
Route::middleware(['auth', 'role:buyer'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/area', fn () => response()->json(['status' => 'ok', 'message' => 'Buyer Area Access Granted']))->name('area');
});

Route::middleware(['auth', 'role:seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', fn () => response()->json(['status' => 'ok', 'message' => 'Seller Center Access Granted']))->name('dashboard');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => response()->json(['status' => 'ok', 'message' => 'Admin Portal Access Granted']))->name('dashboard');
});

require __DIR__.'/auth.php';
