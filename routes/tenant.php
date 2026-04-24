<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChurchController;
use App\Http\Controllers\DiscipleController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DevotionalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SystemInfoController;
use App\Http\Controllers\SystemChangelogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// Password Reset routes (Breeze-compatible, backed by Auth controllers)
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');

Route::middleware(['auth', 'church.tenant'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');

    // Church Admin (registering pastor) — legacy role slug "pastor" still accepted by middleware
    Route::middleware('role:church_admin,pastor')->group(function () {

        // Users: church_admin and program_leader may view/create/update (program_leader limited by service-level scoping). Delete only for church_admin.
        Route::middleware('role:church_admin,program_leader')->group(function () {
            Route::get('/users', [DiscipleController::class, 'index'])->name('users.index');
            Route::get('/users/create', [DiscipleController::class, 'create'])->name('users.create');
            Route::post('/users', [DiscipleController::class, 'store'])->middleware('subscription:add_disciple')->name('users.store');
            Route::get('/users/{id}/edit', [DiscipleController::class, 'edit'])->name('users.edit');
            Route::put('/users/{id}', [DiscipleController::class, 'update'])->name('users.update');
            Route::post('/users/import', [DiscipleController::class, 'import'])->middleware('subscription:bulk_import')->name('users.import');
        });

        // Delete user only for church_admin
        Route::delete('/users/{id}', [DiscipleController::class, 'destroy'])->middleware('role:church_admin')->name('users.destroy');

        // Programs
        Route::get('/programs', [ProgramController::class, 'index'])->middleware('role:church_admin,program_leader,disciple')->name('programs.index');
        Route::get('/programs/{program}', [ProgramController::class, 'show'])->middleware('role:church_admin,program_leader,disciple')->name('programs.show');

        // Program write actions: church_admin + subscription gate
        Route::post('/programs', [ProgramController::class, 'store'])->middleware(['role:church_admin', 'subscription:manage-programs'])->name('programs.store');
        Route::put('/programs/{id}', [ProgramController::class, 'update'])->middleware(['role:church_admin', 'subscription:manage-programs'])->name('programs.update');
        Route::delete('/programs/{id}', [ProgramController::class, 'destroy'])->middleware(['role:church_admin', 'subscription:manage-programs'])->name('programs.destroy');
        Route::patch('programs/{program}/toggle', [ProgramController::class, 'toggle'])->middleware(['role:church_admin', 'subscription:manage-programs'])->name('programs.toggle');
        Route::get('programs/{program}/edit', [ProgramController::class, 'edit'])->middleware(['role:church_admin', 'subscription:manage-programs'])->name('programs.edit');

        // Enroll endpoint for disciples
        Route::post('/programs/{id}/enroll', [ProgramController::class, 'enroll'])->middleware('role:disciple')->name('programs.enroll');

        // Lessons: create by church_admin or program_leader; leaders limited by create-lesson permission and program assignment
        Route::post('/lessons', [LessonController::class, 'store'])->middleware(['role:church_admin,program_leader', 'permission:create-lesson'])->name('lessons.store');
        Route::put('/lessons/{id}', [LessonController::class, 'update'])->middleware(['role:church_admin,program_leader', 'permission:edit-own-lesson'])->name('lessons.update');
        Route::delete('/lessons/{id}', [LessonController::class, 'destroy'])->middleware(['role:church_admin,program_leader', 'permission:edit-own-lesson'])->name('lessons.destroy');
        Route::get('lessons/create', [LessonController::class, 'create'])->middleware('role:church_admin,program_leader')->name('lessons.create');
        Route::get('lessons/{lesson}/edit', [LessonController::class, 'edit'])->middleware('role:church_admin,program_leader')->name('lessons.edit');

        Route::get('/church/subscription', [ChurchController::class, 'subscription'])->name('church.subscription');
        Route::post('/church/subscription', [ChurchController::class, 'updatePlan'])->middleware('role:church_admin')->name('church.subscription.update');

        Route::middleware('permission:view-reports')->group(function () {
            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        Route::middleware('subscription:export_sheets')->group(function () {
            Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
            Route::get('/reports/attendance', [ReportController::class, 'attendanceExport'])->name('reports.attendance');
            Route::get('/reports/devotionals', [ReportController::class, 'devotionalsExport'])->name('reports.devotionals');
            Route::get('/reports/programs', [ReportController::class, 'programsExport'])->name('reports.programs');
            Route::get('/reports/historical', [ReportController::class, 'historicalExport'])->name('reports.historical');
        });
        });

        Route::middleware('role:church_admin,pastor')->group(function () {
            Route::get('/settings/church', [SettingsController::class, 'church'])->name('settings.church');
            Route::patch('/settings/church', [SettingsController::class, 'updateChurch'])->name('settings.church.update');
            Route::get('/settings/upgrade/{plan}', [SettingsController::class, 'upgrade'])->name('settings.upgrade');
        });
    });

    Route::middleware('role:church_admin,pastor,program_leader,leader,cell_leader')
    ->get('/users/{id}', [DiscipleController::class, 'show'])
    ->whereNumber('id')
    ->name('users.show');

    // Attendance & Devotional viewers for church_admin and program_leader
    Route::middleware(['role:church_admin,program_leader'])->group(function () {
        Route::get('/my-disciples', [DiscipleController::class, 'leaderDisciples'])->name('users.disciples');
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/devotionals', [DevotionalController::class, 'index'])->name('devotionals.index');
    });

    // Disciple actions: enroll, mark attendance, mark devotionals
    Route::middleware(['role:disciple'])->group(function () {
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::put('/attendance/{id}', [AttendanceController::class, 'update'])->name('attendance.update');

        Route::post('/devotionals', [DevotionalController::class, 'bulkStore'])->name('devotionals.store');
        Route::post('/devotionals/self', [DevotionalController::class, 'markComplete'])->name('devotionals.complete');
    });

    Route::middleware(['role:disciple', 'permission:dashboard-view'])->group(function () {
        Route::get('/my-lessons', [LessonController::class, 'myLessons'])->name('lessons.my');
        Route::get('/my-progress', [DashboardController::class, 'discipleDashboard'])->name('progress.my');
    });

    Route::middleware(['role:disciple', 'permission:dashboard-view', 'subscription:devotional'])->group(function () {
        Route::get('/my-devotionals', [DevotionalController::class, 'mine'])->name('devotionals.mine');
        Route::post('/devotionals/self', [DevotionalController::class, 'markComplete'])->name('devotionals.complete');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Password update
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

<<<<<<< HEAD
    // Email verification
    Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Confirm password
    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store']);
=======
    Route::middleware('role:church_admin,pastor')->group(function () {
        Route::get('/settings/church', [SettingsController::class, 'church'])->name('settings.church');
        Route::patch('/settings/church', [SettingsController::class, 'updateChurch'])->name('settings.church.update');
        Route::get('/settings/upgrade/{plan}', [SettingsController::class, 'upgrade'])->name('settings.upgrade');

        // Appearance / White-labeling
        Route::get('/settings/appearance', [\App\Http\Controllers\AppearanceController::class, 'edit'])->name('settings.appearance');
        Route::put('/settings/appearance', [\App\Http\Controllers\AppearanceController::class, 'update'])->name('settings.appearance.update');
    });
>>>>>>> 457e519db4921dfdfd1d9e2c5e09d25095a19d8e

    // Support Tickets
    Route::get('/support', [SupportTicketController::class, 'index'])->name('support.index');
    Route::post('/support', [SupportTicketController::class, 'store'])->name('support.store');
    Route::get('/support/admin', [SupportTicketController::class, 'adminIndex'])->middleware('role:church_admin')->name('support.admin');
    Route::get('/support/{ticket}', [SupportTicketController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('support.reply');
    Route::patch('/support/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('support.updateStatus');

    // Announcements
    Route::get('/announcements', [\App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // System Info (church_admin only)
    Route::middleware('role:church_admin')->group(function () {
        Route::get('/system/info',        [SystemInfoController::class, 'index'])->name('system.info');
        Route::get('/system/migrations',  [SystemInfoController::class, 'migrations'])->name('system.migrations');

        // Product release changelog (now DB-driven via SystemChangelogController)
        Route::get('/system/changelog',   [SystemChangelogController::class, 'index'])->name('system.changelog');
        Route::post('/system/changelog/{id}/acknowledge', [SystemChangelogController::class, 'acknowledge'])
             ->name('system.changelog.acknowledge');
    });
});
