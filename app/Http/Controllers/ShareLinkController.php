<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Record;
use App\Models\ShareLink;
use App\Models\AuditLog;
use Carbon\Carbon;

class ShareLinkController extends Controller
{
    /**
     * Generate a time-limited share link for a medical record.
     */
    public function generate(Request $request, $recordId)
    {
        $user = Auth::user();
        $record = Record::findOrFail($recordId);

        // IDOR: Check that patient owns this record
        $patient = $user->patient;
        if (!$patient || (string) $record->patient_id !== (string) $patient->id) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'expires_in' => ['required', 'string', 'in:1h,24h,7d'],
            'password' => ['nullable', 'string', 'min:4'],
        ]);

        $expiresAt = match($request->expires_in) {
            '1h' => Carbon::now()->addHour(),
            '24h' => Carbon::now()->addDay(),
            '7d' => Carbon::now()->addDays(7),
        };

        $token = Str::random(32);
        
        $share = ShareLink::create([
            'user_id' => $user->id,
            'record_id' => $record->id,
            'token' => $token,
            'password' => $request->password ? Hash::make($request->password) : null,
            'expires_at' => $expiresAt,
            'access_count' => 0,
        ]);

        // Audit Log
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Generated shareable link',
            'target_type' => 'Record',
            'target_id' => $record->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'unusual_activity' => false,
        ]);

        $url = route('share.view', $token);

        return redirect()->back()->with('success', 'Expiring share link generated: ' . $url);
    }

    /**
     * View shared clinical document page.
     */
    public function viewShared(Request $request, $token)
    {
        $share = ShareLink::where('token', $token)->firstOrFail();

        if ($share->isExpired()) {
            abort(403, 'This secure sharing link has expired.');
        }

        $record = $share->record;

        // If password is required
        if ($share->password) {
            if ($request->isMethod('post')) {
                $request->validate([
                    'password' => 'required|string',
                ]);

                if (Hash::check($request->password, $share->password)) {
                    session(['share_auth_' . $share->id => true]);
                } else {
                    return view('records.shared-password', [
                        'token' => $token,
                        'error' => 'Incorrect password. Access denied.'
                    ]);
                }
            }

            if (!session('share_auth_' . $share->id)) {
                return view('records.shared-password', [
                    'token' => $token
                ]);
            }
        }

        // Increment count
        $share->increment('access_count');

        // Audit Log (system log, no authenticated user required)
        AuditLog::create([
            'user_id' => $share->user_id, // Owner's ID for tracking
            'action' => 'Accessed shared link',
            'target_type' => 'Record',
            'target_id' => $record->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'unusual_activity' => false,
        ]);

        return view('records.shared-view', [
            'record' => $record,
            'share' => $share,
        ]);
    }
}
