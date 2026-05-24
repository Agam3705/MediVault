<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', 'string', 'in:Patient,Doctor'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($request->role === 'Doctor') {
            $rules['phone'] = ['required', 'string', 'max:20'];
        }

        $request->validate($rules);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
            'last_login_at' => now(),
        ]);

        // Auto-generate the role-based profile document
        if ($user->role === 'Patient') {
            \App\Models\Patient::create([
                'user_id' => $user->id,
                'dob' => null,
                'gender' => null,
                'blood_group' => null,
                'phone' => null,
                'emergency_contact_name' => null,
                'emergency_contact_phone' => null,
                'address' => null,
                'profile_photo' => null,
            ]);
        } elseif ($user->role === 'Doctor') {
            \App\Models\Doctor::create([
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
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => 'User registered and created profile',
            'target_type' => 'User',
            'target_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'unusual_activity' => false,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
