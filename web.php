<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Landing Page
Route::get('/', function () {
    return view('welcome');
});

// Unified dashboard route – redirects users to their role‑specific dashboard
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->hasRole('Doctor')) {
            return redirect()->route('doctor.dashboard');
        }
        // Default to patient dashboard
        return redirect()->route('patient.dashboard');
    })->name('dashboard');

    // Shortcut redirects
    Route::get('/record', fn() => redirect()->route('patient.records.index'));
    Route::get('/emergency-card', fn() => redirect()->route('patient.emergency-card.show'));
});

// Public Emergency Card (no auth needed)
Route::get('/emergency/{token}', [\App\Http\Controllers\EmergencyCardController::class, 'publicView'])->name('emergency.public');

// Public Shared Record
Route::get('/shared/{token}', [\App\Http\Controllers\ShareableLinkController::class, 'show'])->name('records.shared');

// Google Auth
Route::get('auth/google', [\App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback']);

// ─── ADMIN ROUTES ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/doctors', [\App\Http\Controllers\Admin\DoctorVerificationController::class, 'index'])->name('doctors.index');
    Route::post('/doctors/{doctor}/verify', [\App\Http\Controllers\Admin\DoctorVerificationController::class, 'verify'])->name('doctors.verify');
    Route::post('/doctors/{doctor}/reject', [\App\Http\Controllers\Admin\DoctorVerificationController::class, 'reject'])->name('doctors.reject');
    Route::post('/doctors/{doctor}/force-revoke', [\App\Http\Controllers\Admin\DashboardController::class, 'forceRevokeAll'])->name('doctors.force-revoke');
});

// ─── DOCTOR ROUTES ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:Doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/unverified', function() { return view('doctor.unverified'); })->name('unverified');

    Route::middleware(['doctor.verified'])->group(function () {
        Route::get('/dashboard', function() { return view('doctor.dashboard'); })->name('dashboard');
    Route::get('/patients/search', [\App\Http\Controllers\DoctorPatientController::class, 'search'])->name('patients.search');
    Route::get('/patients', [\App\Http\Controllers\DoctorPatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/{patient}', [\App\Http\Controllers\DoctorPatientController::class, 'show'])->name('patients.show');
    Route::post('/patients/{patient}/emergency-override', [\App\Http\Controllers\DoctorPatientController::class, 'emergencyOverride'])->name('patients.emergency-override');
    Route::post('/requests', [\App\Http\Controllers\AccessRequestController::class, 'store'])->name('requests.store');
    });
});

// ─── PATIENT ROUTES ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:Patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/dashboard', function() { return view('patient.dashboard'); })->name('dashboard');

    // Medical Records
    Route::get('/records', [\App\Http\Controllers\RecordController::class, 'index'])->name('records.index');
    Route::get('/records/create', [\App\Http\Controllers\RecordController::class, 'create'])->name('records.create');
    Route::post('/records', [\App\Http\Controllers\RecordController::class, 'store'])->name('records.store');
    Route::delete('/records/{record}', [\App\Http\Controllers\RecordController::class, 'destroy'])->name('records.destroy');
    Route::patch('/records/{record}/toggle-visibility', [\App\Http\Controllers\RecordController::class, 'toggleVisibility'])->name('records.toggle-visibility');
    Route::get('/records/{record}/versions', [\App\Http\Controllers\RecordController::class, 'versions'])->name('records.versions');
    Route::get('/records/export-csv', [\App\Http\Controllers\RecordController::class, 'exportCsv'])->name('records.export-csv');
    
    // Shareable Links
    Route::post('/records/{record}/share', [\App\Http\Controllers\ShareableLinkController::class, 'store'])->name('records.share');
    Route::delete('/shareable-links/{link}', [\App\Http\Controllers\ShareableLinkController::class, 'destroy'])->name('shareable-links.destroy');

    // Access Management
    Route::get('/requests', [\App\Http\Controllers\AccessRequestController::class, 'index'])->name('requests.index');
    Route::patch('/requests/{accessRequest}', [\App\Http\Controllers\AccessRequestController::class, 'update'])->name('requests.update');

    Route::get('/grants', [\App\Http\Controllers\AccessGrantController::class, 'index'])->name('grants.index');
    Route::delete('/grants/{accessGrant}', [\App\Http\Controllers\AccessGrantController::class, 'destroy'])->name('grants.destroy');

    // Emergency Card
    Route::get('/emergency-card', [\App\Http\Controllers\EmergencyCardController::class, 'show'])->name('emergency-card.show');
    Route::post('/emergency-card', [\App\Http\Controllers\EmergencyCardController::class, 'update'])->name('emergency-card.update');

    // Audit Log
    Route::get('/audit', [\App\Http\Controllers\PatientAuditController::class, 'index'])->name('audit.index');
});

// ─── SHARED PROFILE ROUTES ────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/sessions', [ProfileController::class, 'logoutOtherBrowserSessions'])->name('profile.sessions.destroy');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Global Search
    Route::get('/search/global', [\App\Http\Controllers\SearchController::class, 'globalSearch'])->name('search.global');

    // Secure File Access
    Route::get('/records/{record}/download', [\App\Http\Controllers\RecordController::class, 'download'])->name('records.download');
});

require __DIR__.'/auth.php';
