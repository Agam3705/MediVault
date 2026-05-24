<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\SocialAccount;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Google authentication failed. Please try again.']);
        }

        // Check if user already exists
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Update last login
            $user->update(['last_login_at' => now()]);

            // Link social account if not already linked
            $socialAccount = SocialAccount::where('provider', 'google')
                ->where('provider_user_id', $googleUser->getId())
                ->first();

            if (!$socialAccount) {
                SocialAccount::create([
                    'user_id' => $user->id,
                    'provider' => 'google',
                    'provider_user_id' => $googleUser->getId(),
                    'avatar_url' => $googleUser->getAvatar(),
                ]);
            }

            // Log in the user
            Auth::login($user);

            // Audit log
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'Logged in via Google OAuth',
                'target_type' => 'User',
                'target_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId(),
                'unusual_activity' => false,
            ]);

            return redirect()->route('dashboard');
        }

        // If user does not exist, save Google info in session and redirect to role selection
        session([
            'social_user' => [
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'avatar' => $googleUser->getAvatar(),
                'id' => $googleUser->getId(),
            ]
        ]);

        return redirect()->route('auth.social-register');
    }

    /**
     * Show social registration role selection view.
     */
    public function showSocialRegister()
    {
        if (!session()->has('social_user')) {
            return redirect()->route('login');
        }

        return view('auth.social-register', [
            'socialUser' => session('social_user')
        ]);
    }

    /**
     * Store social registration role selection.
     */
    public function storeSocialRegister(Request $request)
    {
        if (!session()->has('social_user')) {
            return redirect()->route('login');
        }

        $rules = [
            'role' => ['required', 'string', 'in:Patient,Doctor'],
        ];

        if ($request->role === 'Doctor') {
            $rules['phone'] = ['required', 'string', 'max:20'];
        }

        $request->validate($rules);

        $socialUser = session('social_user');

        // Create User
        $user = User::create([
            'name' => $socialUser['name'],
            'email' => $socialUser['email'],
            'password' => Hash::make(Str::random(24)), // Random secure password for social login
            'role' => $request->role,
            'is_active' => true,
            'last_login_at' => now(),
        ]);

        // Create Social Account Link
        SocialAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => $socialUser['id'],
            'avatar_url' => $socialUser['avatar'],
        ]);

        // Create Profile Document
        if ($user->role === 'Patient') {
            Patient::create([
                'user_id' => $user->id,
                'dob' => null,
                'gender' => null,
                'blood_group' => null,
                'phone' => null,
                'emergency_contact_name' => null,
                'emergency_contact_phone' => null,
                'address' => null,
                'profile_photo' => $socialUser['avatar'] ?? null,
            ]);
        } elseif ($user->role === 'Doctor') {
            Doctor::create([
                'user_id' => $user->id,
                'license_number' => null,
                'specialization' => null,
                'hospital' => null,
                'bio' => null,
                'phone' => $request->phone,
                'verified_at' => null,
                'verified_by' => null,
            ]);
        }

        // Immutable Audit Log
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Registered via Google OAuth',
            'target_type' => 'User',
            'target_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'unusual_activity' => false,
        ]);

        // Clean session
        session()->forget('social_user');

        // Log in the user
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
