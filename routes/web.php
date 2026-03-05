<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChurchController;
use App\Http\Controllers\DiscipleController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DevotionalController;
use App\Http\Controllers\ReportController;

// ══════════════════════════════════════════════
//  GUEST ROUTES
// ══════════════════════════════════════════════
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/', fn() => view('welcome'))->name('home');
    Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
});

// ══════════════════════════════════════════════
//  AUTHENTICATED ROUTES
//  Stack: auth → church.tenant (binds church_id)
// ══════════════════════════════════════════════
Route::middleware(['auth', 'church.tenant'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Single dashboard — dispatches by role internally
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── PASTOR ONLY ──────────────────────────────────────────
    Route::middleware('role:pastor')->group(function () {

        // Church & subscription management
        Route::get('/church/subscription',  [ChurchController::class, 'subscription'])->name('church.subscription');
        Route::post('/church/subscription', [ChurchController::class, 'updatePlan'])->name('church.subscription.update');

        // Member management (with plan limit gates)
        Route::get('/disciples',         [DiscipleController::class, 'index'])->name('disciples.index');
        Route::get('/disciples/create',  [DiscipleController::class, 'create'])->name('disciples.create');
        Route::post('/disciples',        [DiscipleController::class, 'store'])->middleware('subscription:add_disciple')->name('disciples.store');
        Route::get('/disciples/{id}',    [DiscipleController::class, 'show'])->name('disciples.show');
        Route::get('/disciples/{id}/edit', [DiscipleController::class, 'edit'])->name('disciples.edit');
        Route::put('/disciples/{id}',    [DiscipleController::class, 'update'])->name('disciples.update');
        Route::delete('/disciples/{id}', [DiscipleController::class, 'destroy'])->name('disciples.destroy');
        Route::post('/disciples/import', [DiscipleController::class, 'import'])->middleware('subscription:bulk_import')->name('disciples.import');

        // Programs
        Route::get('/programs',          [ProgramController::class, 'index'])->name('programs.index');
        Route::post('/programs',         [ProgramController::class, 'store'])->name('programs.store');
        Route::put('/programs/{id}',     [ProgramController::class, 'update'])->name('programs.update');
        Route::delete('/programs/{id}',  [ProgramController::class, 'destroy'])->name('programs.destroy');

        // Lessons (pastor manages, disciples read)
        Route::post('/lessons',          [LessonController::class, 'store'])->name('lessons.store');
        Route::put('/lessons/{id}',      [LessonController::class, 'update'])->name('lessons.update');
        Route::delete('/lessons/{id}',   [LessonController::class, 'destroy'])->name('lessons.destroy');

        // Reports — Standard/Premium only
        Route::middleware('subscription:export_sheets')->group(function () {
            Route::get('/reports',         [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/export',  [ReportController::class, 'export'])->name('reports.export');
        });
    });

    // ── PASTOR + LEADER ───────────────────────────────────────
    Route::middleware('role:pastor,leader')->group(function () {

        // Attendance tracking
        Route::get('/attendance',   [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance',  [AttendanceController::class, 'store'])->name('attendance.store');

        // Devotional tracking — Basic+ only
        Route::middleware('subscription:devotional')->group(function () {
            Route::get('/devotionals',              [DevotionalController::class, 'index'])->name('devotionals.index');
            Route::post('/devotionals/mark',        [DevotionalController::class, 'markComplete'])->name('devotionals.mark');
        });
    });

    // ── DISCIPLE ONLY ─────────────────────────────────────────
    Route::middleware('role:disciple')->group(function () {
        Route::get('/my-lessons',   [LessonController::class, 'myLessons'])->name('lessons.my');
        Route::get('/my-progress',  [DashboardController::class, 'disciple'])->name('progress.my');
        Route::post('/devotionals/complete', [DevotionalController::class, 'markComplete'])->name('devotionals.complete');
    });

    // ── SHARED READ (any authenticated role) ──────────────────
    Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');
});
