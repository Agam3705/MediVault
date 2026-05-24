<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SessionsTracker;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\MediVaultMail;

class SessionController extends Controller
{
    /**
     * Display a list of active login sessions.
     */
    public function index()
    {
        $user = Auth::user();

        // Refresh session statuses
        $sessions = SessionsTracker::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('last_active_at', 'desc')
            ->get();

        return view('profile.sessions', [
            'sessions' => $sessions,
            'currentSessionToken' => session()->getId()
        ]);
    }

    /**
     * Terminate / revoke a session remotely.
     */
    public function revoke(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        $user = Auth::user();
        $session = SessionsTracker::where('user_id', $user->id)
            ->where('id', $request->session_id)
            ->firstOrFail();

        $session->update([
            'is_active' => false
        ]);

        // Audit Log
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Remotely terminated user session',
            'target_type' => 'User',
            'target_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'unusual_activity' => false,
        ]);

        Mail::to($user->email)->queue(new MediVaultMail(
            'emails.session_terminated',
            'SECURITY ALERT: Remote Login Session Terminated',
            [
                'device' => $session->device,
                'ipAddress' => $session->ip_address,
                'timestamp' => now()->format('M d, Y H:i:s'),
            ]
        ));

        // If the user terminated their current session, log them out immediately
        if ($session->session_token === session()->getId()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/')->with('success', 'Your current session has been terminated.');
        }

        return redirect()->back()->with('success', 'Remote session terminated successfully.');
    }
}
