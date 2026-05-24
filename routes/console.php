<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\AccessRequest;
use App\Models\AccessGrant;
use App\Mail\MediVaultMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── Automated Cron Tasks & Expiration Schedulers ────────────────────
Schedule::call(function () {
    // 1. Auto-expire pending requests older than 48 hours
    AccessRequest::where('status', 'pending')
        ->where('created_at', '<=', Carbon::now()->subHours(48))
        ->update(['status' => 'expired']);

    // 2. Disable expired access grants
    AccessGrant::where('is_active', true)
        ->whereNotNull('expires_at')
        ->where('expires_at', '<', Carbon::now())
        ->update([
            'is_active' => false,
            'revoked_at' => Carbon::now(),
            'revoked_reason' => 'System schedule auto-expiry: Authorized access window lapsed.'
        ]);

    // 3. Email warning to doctors exactly 3 days before grant expiry
    $threeDaysLaterStart = Carbon::now()->addDays(3)->startOfDay();
    $threeDaysLaterEnd = Carbon::now()->addDays(3)->endOfDay();
    
    $expiringGrants = AccessGrant::where('is_active', true)
        ->whereNotNull('expires_at')
        ->where('expires_at', '>=', $threeDaysLaterStart)
        ->where('expires_at', '<=', $threeDaysLaterEnd)
        ->get();

    foreach ($expiringGrants as $grant) {
        if ($grant->doctor && $grant->doctor->user) {
            Mail::to($grant->doctor->user->email)->queue(new MediVaultMail(
                'emails.access_expiring',
                'ALERT: Patient Record Access Expiring in 3 Days',
                [
                    'doctorName' => $grant->doctor->user->name,
                    'patientName' => $grant->patient->user->name ?? 'Patient',
                    'expiryDate' => $grant->expires_at->format('M d, Y H:i'),
                ]
            ));
        }
    }
})->everyMinute();
