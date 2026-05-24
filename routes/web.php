<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ConsentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmergencyCardController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShareLinkController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\RecycleBinController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ─── Public Routes (No Auth) ───────────────────────────────────────
Route::get('/emergency/card/{qrToken}', [EmergencyCardController::class, 'publicView'])
    ->name('emergency.card.view');

// Public shared record route
Route::match(['get', 'post'], '/shared/record/{token}', [ShareLinkController::class, 'viewShared'])
    ->name('share.view');

// ─── Authenticated Routes ──────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Profile & Sessions
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/sessions', [SessionController::class, 'index'])->name('profile.sessions');
    Route::post('/sessions/revoke', [SessionController::class, 'revoke'])->name('profile.sessions.revoke');

    // ─── Medical Records & Recycle Bin ───────────────────────────────
    Route::get('/records/create', [RecordController::class, 'create'])->name('records.create');
    Route::post('/records', [RecordController::class, 'store'])->name('records.store');
    Route::get('/records/{record}/edit', [RecordController::class, 'edit'])->name('records.edit');
    Route::patch('/records/{record}', [RecordController::class, 'update'])->name('records.update');
    Route::delete('/records/{record}', [RecordController::class, 'destroy'])->name('records.destroy');
    Route::get('/records/{record}/versions', [RecordController::class, 'versions'])->name('records.versions');

    Route::get('/recycle-bin', [RecycleBinController::class, 'index'])->name('recycle-bin.index');
    Route::post('/recycle-bin/restore/{record}', [RecycleBinController::class, 'restore'])->name('recycle-bin.restore');
    Route::delete('/recycle-bin/force-delete/{record}', [RecycleBinController::class, 'forceDelete'])->name('recycle-bin.force-delete');

    // Share link generation
    Route::post('/records/{record}/share', [ShareLinkController::class, 'generate'])->name('records.share');

    // Doctor views patient records (requires active grant — enforced in controller)
    Route::get('/records/patient/{patient}', [RecordController::class, 'patientRecords'])->name('records.patient');

    // ─── Consent Management ────────────────────────────────────────
    // Doctor → submit a request
    Route::post('/consent/request', [ConsentController::class, 'request'])->name('consent.request');
    // Patient → approve/deny incoming requests
    Route::post('/consent/approve/{requestId}', [ConsentController::class, 'approve'])->name('consent.approve');
    Route::post('/consent/deny/{requestId}', [ConsentController::class, 'deny'])->name('consent.deny');
    // Patient → revoke active grant
    Route::post('/consent/revoke/{grantId}', [ConsentController::class, 'revoke'])->name('consent.revoke');
    // Patient → update granular visibility
    Route::post('/consent/grant/{grantId}/visibility', [ConsentController::class, 'updateVisibility'])->name('consent.visibility');
    // Doctor → Emergency access override
    Route::post('/consent/override/{patientId}', [ConsentController::class, 'override'])->name('consent.override');
    // Patient → Grant direct access to doctor
    Route::post('/consent/grant-direct', [ConsentController::class, 'grantDirectAccess'])->name('consent.grant-direct');
    // Doctor → withdraw a pending consent request
    Route::delete('/consent/request/{requestId}/withdraw', [ConsentController::class, 'withdraw'])->name('consent.withdraw');

    // ─── Smart Search Endpoint ─────────────────────────────────────
    Route::get('/search', [SearchController::class, 'search'])->name('search');

    // ─── Doctor Directory Page ─────────────────────────────────────
    Route::get('/doctors', [DashboardController::class, 'doctorDirectory'])->name('doctors.index');

    // Doctor → My All-Time Patients
    Route::get('/my-patients', [DashboardController::class, 'myPatients'])->name('doctors.my-patients');

    // ─── Notification Center ───────────────────────────────────────
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

    // ─── Support Tickets ───────────────────────────────────────────
    Route::get('/support', [\App\Http\Controllers\SupportTicketController::class, 'create'])->name('support.create');
    Route::post('/support', [\App\Http\Controllers\SupportTicketController::class, 'store'])->name('support.store');
    Route::post('/support/reply/{ticket}', [\App\Http\Controllers\SupportTicketController::class, 'reply'])->name('support.reply');

    // ─── Clinical Chat Routes ──────────────────────────────────────
    Route::get('/chat/{user}', [\App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');
    Route::get('/chat/api/messages/{user}', [\App\Http\Controllers\ChatController::class, 'fetchMessages'])->name('chat.api.messages');
    Route::post('/chat/api/messages/{user}', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.api.send');

    // ─── Admin Routes ──────────────────────────────────────────────
    Route::post('/admin/doctors/{doctor}/verify', [AdminController::class, 'verifyDoctor'])->name('admin.doctors.verify');
    Route::post('/admin/doctors/{doctor}/reject', [AdminController::class, 'rejectDoctor'])->name('admin.doctors.reject');
    Route::post('/admin/doctors/{doctor}/revoke-verification', [AdminController::class, 'revokeVerification'])->name('admin.doctors.revoke-verification');
    Route::post('/admin/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');
    Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/admin/force-revoke', [AdminController::class, 'forceRevokeAll'])->name('admin.force-revoke');
});

Route::get('/lang/{locale}', [\App\Http\Controllers\LocaleController::class, 'switch'])->name('lang.switch');

require __DIR__.'/auth.php';
