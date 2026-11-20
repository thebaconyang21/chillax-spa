<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/appointments', function () {
    return view('appointments');
})->name('appointments');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/services', function () {
    return view('services');
})->name('services');

Route::get('/customers', function () {
    return view('customers');
})->name('customers');

Route::get('/pos', function () {
    return view('pos');
})->name('pos');
