<?php

use App\Http\Controllers\AuthController;
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
    Route::get('/services', fn () => view('services'))->name('services');
    Route::get('/reports', fn () => view('reports'))->name('reports');
    Route::get('/users', fn () => view('users'))->name('users');
});
