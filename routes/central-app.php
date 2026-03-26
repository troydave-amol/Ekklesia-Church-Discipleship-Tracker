<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TenantRegistrationController;


// Landing page
Route::get('/', fn() => view('welcome'))->name('home');

// Registration API endpoints
Route::post('/register-tenant', [TenantRegistrationController::class, 'store']);
Route::get('/check-subdomain', [TenantRegistrationController::class, 'checkSubdomain']);

// ══════════════════════════════════════════════
//  GUEST ROUTES
// ══════════════════════════════════════════════
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');


