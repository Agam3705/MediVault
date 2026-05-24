<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SessionsTracker;
use Jenssegers\Agent\Agent; // Safe fallback or custom parser if not installed

class TrackActiveSessions
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check if user is suspended
            if ($user->is_active === false) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->withErrors(['email' => 'Your account has been suspended by the administrator.']);
            }

            $sessionId = session()->getId();

            // Check if this session is terminated
            $sessionRecord = SessionsTracker::where('user_id', $user->id)
                ->where('session_token', $sessionId)
                ->first();

            if ($sessionRecord && !$sessionRecord->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->withErrors(['email' => 'Your session was remotely terminated.']);
            }

            // Detect device
            $userAgentString = $request->userAgent() ?? 'Unknown Browser';
            $device = 'Desktop/Laptop';
            if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgentString)) {
                $device = 'Mobile Device';
            } elseif (preg_match('/(ipad|playbook|silk)/i', $userAgentString)) {
                $device = 'Tablet Device';
            }

            if ($sessionRecord) {
                $sessionRecord->update([
                    'last_active_at' => now(),
                    'ip_address' => $request->ip(),
                ]);
            } else {
                SessionsTracker::create([
                    'user_id' => $user->id,
                    'session_token' => $sessionId,
                    'ip_address' => $request->ip(),
                    'device' => $device,
                    'logged_in_at' => now(),
                    'last_active_at' => now(),
                    'is_active' => true,
                ]);
            }
        }

        return $next($request);
    }
}
