<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\AdminBrandingController;
use App\Http\Controllers\Admin\AdminCompanyController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUpdateController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyInviteController;
use App\Http\Controllers\CompanySetupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ObjectiveController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PlannedTopicController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Mail\MeetingSummary;
use App\Models\Meeting;
use Illuminate\Support\Facades\Route;

// Installation routes (only available when not installed)
Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'requirements'])->name('requirements');
    Route::get('/database', [InstallController::class, 'database'])->name('database');
    Route::post('/database', [InstallController::class, 'saveDatabase'])->name('database.save');
    Route::get('/migrate', [InstallController::class, 'migrate'])->name('migrate');
    Route::post('/migrate', [InstallController::class, 'runMigrations'])->name('migrate.run');
    Route::get('/admin', [InstallController::class, 'admin'])->name('admin');
    Route::post('/admin', [InstallController::class, 'saveAdmin'])->name('admin.save');
    Route::get('/finalize', [InstallController::class, 'finalize'])->name('finalize');
    Route::post('/finalize', [InstallController::class, 'complete'])->name('complete');
    Route::get('/success', [InstallController::class, 'success'])->name('success');
});

Route::get('/', function () {
    // Redirect to installer if not installed
    if (!InstallController::isInstalled()) {
        return redirect()->route('install.requirements');
    }
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Settings routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // People routes
    Route::get('/people', [PersonController::class, 'index'])->name('people.index');
    Route::get('/people/archived', [PersonController::class, 'archived'])->name('people.archived');
    Route::get('/people/create', [PersonController::class, 'create'])->name('people.create');
    Route::post('/people', [PersonController::class, 'store'])->name('people.store');
    Route::get('/people/{person}/edit', [PersonController::class, 'edit'])->name('people.edit');
    Route::put('/people/{person}', [PersonController::class, 'update'])->name('people.update');
    Route::post('/people/{person}/archive', [PersonController::class, 'archive'])->name('people.archive');
    Route::post('/people/{person}/restore', [PersonController::class, 'restore'])->name('people.restore');
    Route::delete('/people/{person}', [PersonController::class, 'destroy'])->name('people.destroy');

    // Meeting routes
    Route::get('/people/{person}/meetings', [MeetingController::class, 'index'])->name('people.meetings.index');
    Route::get('/people/{person}/meetings/create', [MeetingController::class, 'create'])->name('people.meetings.create');
    Route::post('/people/{person}/meetings', [MeetingController::class, 'store'])->name('people.meetings.store');
    Route::get('/meetings/{meeting}', [MeetingController::class, 'show'])->name('meetings.show');
    Route::get('/meetings/{meeting}/edit', [MeetingController::class, 'edit'])->name('meetings.edit');
    Route::put('/meetings/{meeting}', [MeetingController::class, 'update'])->name('meetings.update');
    Route::delete('/meetings/{meeting}', [MeetingController::class, 'destroy'])->name('meetings.destroy');
    Route::get('/meetings/{meeting}/email', [MeetingController::class, 'email'])->name('meetings.email');

    // Email preview
    Route::get('/meetings/{meeting}/email-preview', function (Meeting $meeting) {
        abort_unless($meeting->person->user_id === auth()->id(), 403);

        $meeting->load(['person', 'topics', 'actions.assignedToPerson']);
        $overdueActions = $meeting->person->actions()->overdue()->with('meeting')->get();
        $dueSoonActions = $meeting->person->actions()->dueSoon()->with('meeting')->get();

        return new MeetingSummary($meeting, auth()->user(), $overdueActions, $dueSoonActions);
    })->name('meetings.email-preview');

    // Action routes
    Route::get('/actions', [ActionController::class, 'globalIndex'])->name('actions.index');
    Route::get('/people/{person}/actions', [ActionController::class, 'index'])->name('people.actions.index');
    Route::patch('/actions/{action}/status', [ActionController::class, 'updateStatus'])->name('actions.updateStatus');
    Route::post('/actions/{action}/reminder', [ActionController::class, 'sendReminder'])->name('actions.sendReminder');
    Route::post('/people/{person}/actions/reminder-all', [ActionController::class, 'sendBatchReminder'])->name('actions.sendBatchReminder');

    // Objective routes
    Route::get('/objectives', [ObjectiveController::class, 'globalIndex'])->name('objectives.index');
    Route::get('/people/{person}/objectives', [ObjectiveController::class, 'index'])->name('people.objectives.index');
    Route::post('/people/{person}/objectives', [ObjectiveController::class, 'store'])->name('people.objectives.store');
    Route::put('/objectives/{objective}', [ObjectiveController::class, 'update'])->name('objectives.update');
    Route::delete('/objectives/{objective}', [ObjectiveController::class, 'destroy'])->name('objectives.destroy');
    Route::patch('/objectives/{objective}/status', [ObjectiveController::class, 'updateStatus'])->name('objectives.updateStatus');

    // Planned topics routes
    Route::post('/people/{person}/planned-topics', [PlannedTopicController::class, 'store'])->name('people.planned-topics.store');
    Route::delete('/planned-topics/{plannedTopic}', [PlannedTopicController::class, 'destroy'])->name('planned-topics.destroy');

    // Admin notification dismissal routes (for regular users)
    Route::post('/notifications/{notification}/dismiss', [AdminNotificationController::class, 'dismiss'])->name('notifications.dismiss');
    Route::post('/notifications/dismiss-all', [AdminNotificationController::class, 'dismissAll'])->name('notifications.dismiss-all');

    // Company switching
    Route::post('/company/{company}/switch', [CompanyController::class, 'switch'])->name('company.switch');

    // Company management (for company admins/owners)
    Route::get('/company/settings', [CompanyController::class, 'settings'])->name('company.settings');
    Route::put('/company/settings', [CompanyController::class, 'updateSettings'])->name('company.settings.update');

    // Company invites (for company admins/owners)
    Route::get('/company/invites', [CompanyInviteController::class, 'index'])->name('company.invites.index');
    Route::post('/company/invites', [CompanyInviteController::class, 'store'])->name('company.invites.store');
    Route::delete('/company/invites/{invite}', [CompanyInviteController::class, 'destroy'])->name('company.invites.destroy');
    Route::post('/company/invites/{invite}/resend', [CompanyInviteController::class, 'resend'])->name('company.invites.resend');
});

// Company setup/create routes (exempt from has_company middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/company/setup', [CompanySetupController::class, 'show'])->name('company.setup');
    Route::post('/company/setup', [CompanySetupController::class, 'store'])->name('company.setup.store');
    Route::get('/company/create', [CompanyController::class, 'create'])->name('company.create');
    Route::post('/company/create', [CompanyController::class, 'store'])->name('company.store');
});

// Admin routes (super admin only)
Route::middleware(['auth', 'super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/unsuspend', [AdminUserController::class, 'unsuspend'])->name('users.unsuspend');
    Route::post('/users/{user}/force-password-reset', [AdminUserController::class, 'forcePasswordReset'])->name('users.force-password-reset');
    Route::post('/users/{user}/promote', [AdminUserController::class, 'promote'])->name('users.promote');
    Route::post('/users/{user}/demote', [AdminUserController::class, 'demote'])->name('users.demote');
    Route::post('/users/{user}/view-data', [AdminUserController::class, 'viewData'])->name('users.view-data');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Companies
    Route::get('/companies', [AdminCompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/{company}', [AdminCompanyController::class, 'show'])->name('companies.show');

    // Audit logs
    Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{auditLog}', [AdminAuditLogController::class, 'show'])->name('audit-logs.show');

    // Branding
    Route::get('/branding', [AdminBrandingController::class, 'index'])->name('branding.index');
    Route::put('/branding', [AdminBrandingController::class, 'update'])->name('branding.update');
    Route::delete('/branding/logo', [AdminBrandingController::class, 'removeLogo'])->name('branding.remove-logo');

    // Updates
    Route::get('/updates', [AdminUpdateController::class, 'index'])->name('updates.index');
    Route::post('/updates/check', [AdminUpdateController::class, 'check'])->name('updates.check');
    Route::post('/updates/apply', [AdminUpdateController::class, 'apply'])->name('updates.apply');
});

require __DIR__.'/auth.php';
