<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WarrantyRequestController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Không bắt buộc, nhưng giữ lại nếu cần đăng ký nhanh
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/warranty/request', [WarrantyRequestController::class, 'create'])->name('warranty.request');
    Route::post('/warranty/request', [WarrantyRequestController::class, 'store']);
    Route::get('/warranty/success', [WarrantyRequestController::class, 'success'])->name('warranty.success');
    Route::get('/warranty/requests', [WarrantyRequestController::class, 'index'])->name('warranty.index');
    Route::get('/warranty/requests/{warrantyRequest}', [WarrantyRequestController::class, 'show'])->name('warranty.show');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware('role:admin')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::resource('users', AdminUserController::class)->except(['show']);
    });
