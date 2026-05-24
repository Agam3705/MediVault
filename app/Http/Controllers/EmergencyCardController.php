<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmergencyCard;
use App\Models\Patient;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\MediVaultMail;

class EmergencyCardController extends Controller
{
    /**
     * Public view of an emergency card (scanned by hospital responders).
     * No authentication required — this is the whole point of QR scanning.
     */
    public function publicView(Request $request, $qrToken)
    {
        $card = EmergencyCard::where('qr_token', $qrToken)->firstOrFail();

        if (!$card->is_public) {
            abort(404, 'This emergency card has been disabled by the patient.');
        }

        $patient = Patient::with('user')->findOrFail($card->patient_id);

        // Log the QR scan
        AuditLog::create([
            'user_id'          => null,
            'action'           => 'Scanned emergency QR card',
            'target_type'      => 'EmergencyCard',
            'target_id'        => $card->id,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => null,
            'unusual_activity' => false,
        ]);

        if ($patient->user->getSetting('notify_on_qr_scan', true)) {
            Mail::to($patient->user->email)->queue(new MediVaultMail(
                'emails.emergency_card_scanned',
                'ALERT: Emergency QR Card Scanned',
                [
                    'patientName' => $patient->user->name,
                    'ipAddress' => $request->ip(),
                    'timestamp' => now()->format('M d, Y H:i:s'),
                ]
            ));
        }

        return view('emergency.public-card', [
            'card'    => $card,
            'patient' => $patient,
        ]);
    }
}
