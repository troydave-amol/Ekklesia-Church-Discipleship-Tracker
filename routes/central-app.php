<?php

use App\Http\Controllers\Admin\CentralDashboardController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\AdminAnnouncementController;
use App\Http\Controllers\Admin\AdminChangelogController;
use App\Http\Controllers\Admin\AdminRBACController;
use App\Http\Controllers\Admin\TenantSignupController;
use App\Http\Controllers\TenantRegistrationController;
use Illuminate\Support\Facades\Route;

// ── Central routes (no tenancy) ───────────────────────────────────────────────

Route::get('/', fn () => view('landing'))->name('home');
Route::get('/register', fn () => view('register'))->name('register');
Route::get('/provision', fn () => view('provision'))->name('provision');
Route::get('/registration-pending', fn () => view('registration-pending'))->name('registration.pending');

Route::post('/register-tenant', [TenantRegistrationController::class, 'store']);
Route::get('/check-subdomain', [TenantRegistrationController::class, 'checkSubdomain'])->name('check.subdomain');

Route::get('/forgot-password', fn () => view('auth.forgot-password'))->name('password.request');

// ── Admin Authentication (public routes) ──────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// ── Central SaaS Admin Management (protected) ──────────────────────────────────
Route::middleware(['auth:admin', 'admin_auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', CentralDashboardController::class)->name('dashboard');

    // Tenant Management
    Route::get('/signups', [TenantSignupController::class, 'index'])->name('signups.index');
    Route::post('/signups/{signup}/approve', [TenantSignupController::class, 'approve'])->name('signups.approve');
    Route::post('/signups/{signup}/reject', [TenantSignupController::class, 'reject'])->name('signups.reject');

    Route::resource('tenants', TenantRegistrationController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    Route::patch('/tenants/{tenant}/status', [TenantRegistrationController::class, 'toggleStatus'])->name('tenants.toggle-status');

    // Support Tickets from Tenants
    Route::resource('tickets', AdminTicketController::class)->only(['index', 'show']);
    Route::post('/tickets/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('tickets.reply');
    Route::patch('/tickets/{ticket}/close', [AdminTicketController::class, 'close'])->name('tickets.close');

    // Announcements to Tenants
    Route::resource('announcements', AdminAnnouncementController::class);
    Route::post('/announcements/{announcement}/publish', [AdminAnnouncementController::class, 'publish'])->name('announcements.publish');

    // System Changelog
    Route::get('/changelog', [AdminChangelogController::class, 'index'])->name('changelog.index');
    Route::post('/changelog/broadcast', [AdminChangelogController::class, 'broadcast'])->name('changelog.broadcast');

    // RBAC Management (superadmin only)
    Route::middleware('admin_role:super_admin')->prefix('rbac')->name('rbac.')->group(function () {
        Route::get('/tenants/{tenant}', [AdminRBACController::class, 'show'])->name('show');
        Route::get('/tenants/{tenant}/roles', [AdminRBACController::class, 'editRoles'])->name('edit-roles');
        Route::patch('/tenants/{tenant}/roles/{role}', [AdminRBACController::class, 'updateRolePermissions'])->name('update-permissions');
        Route::get('/tenants/{tenant}/audit', [AdminRBACController::class, 'auditLog'])->name('audit-log');
    });

    // Admin User Management (superadmin only)
    Route::middleware('admin_role:super_admin')->resource('users', AdminAuthController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});

// Tenant subdomain routes are defined in routes/tenant.php (loaded by TenancyServiceProvider).
