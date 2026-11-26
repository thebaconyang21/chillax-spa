<?php

use App\Http\Controllers\AddonController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin,cashier'])->group(function (): void {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
    Route::get('/appointments', fn () => view('appointments'))->name('appointments');
    Route::get('/customers', fn () => view('customers'))->name('customers');
    Route::get('/pos', fn () => view('pos'))->name('pos');
    Route::get('/transactions', fn () => view('transactions'))->name('transactions');
});

Route::middleware(['auth', 'role:admin'])->group(function (): void {
    Route::get('/therapists', fn () => view('therapists'))->name('therapists');
    Route::get('/services', [ServiceController::class, 'index'])->name('services');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    Route::post('/addons', [AddonController::class, 'store'])->name('addons.store');
    Route::put('/addons/{addon}', [AddonController::class, 'update'])->name('addons.update');
    Route::delete('/addons/{addon}', [AddonController::class, 'destroy'])->name('addons.destroy');
    Route::get('/reports', fn () => view('reports'))->name('reports');
    Route::get('/users', fn () => view('users'))->name('users');
});
