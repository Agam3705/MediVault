<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\EmergencyCard;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Fetch or create profile association
        $patient = null;
        $doctor = null;
        $emergencyCard = null;

        if ($user->role === 'Patient') {
            $patient = $user->patient ?? Patient::create(['user_id' => $user->id]);
            $emergencyCard = EmergencyCard::where('patient_id', $patient->id)->first();
        } elseif ($user->role === 'Doctor') {
            $doctor = $user->doctor ?? Doctor::create(['user_id' => $user->id]);
        }

        return view('profile.edit', [
            'user' => $user,
            'patient' => $patient,
            'doctor' => $doctor,
            'emergencyCard' => $emergencyCard,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update notification preferences settings
        $user->setSetting('notify_on_access_request', $request->boolean('notify_on_access_request', true));
        $user->setSetting('notify_on_record_viewed', $request->boolean('notify_on_record_viewed', true));
        $user->setSetting('notify_on_record_uploaded', $request->boolean('notify_on_record_uploaded', true));
        $user->setSetting('notify_on_qr_scan', $request->boolean('notify_on_qr_scan', true));

        // Update Role-Specific Profiles
        if ($user->role === 'Patient') {
            $patient = $user->patient ?? Patient::create(['user_id' => $user->id]);
            
            $patientData = $request->validate([
                'dob' => ['nullable', 'date'],
                'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
                'blood_group' => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
                'phone' => ['nullable', 'string', 'max:20'],
                'emergency_contact_name' => ['nullable', 'string', 'max:255'],
                'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:500'],
                'profile_photo_file' => ['nullable', 'image', 'max:2048'],
            ]);

            // Handle Cloudinary upload for avatar
            if ($request->hasFile('profile_photo_file')) {
                try {
                    $uploaded = cloudinary()->upload($request->file('profile_photo_file')->getRealPath(), [
                        'folder' => 'medivault/avatars',
                        'transformation' => [
                            'width' => 300,
                            'height' => 300,
                            'crop' => 'fill',
                            'gravity' => 'face'
                        ]
                    ]);
                    $patientData['profile_photo'] = $uploaded->getSecurePath();
                } catch (\Exception $e) {
                    // Fallback local storage
                    $path = $request->file('profile_photo_file')->store('avatars', 'public');
                    $patientData['profile_photo'] = '/storage/' . $path;
                }
            }

            $patient->update($patientData);

            // Handle Emergency Card updating / sync
            $emergencyData = $request->validate([
                'is_public' => ['nullable', 'boolean'],
                'allergies' => ['nullable', 'string', 'max:1000'],
                'medications' => ['nullable', 'string', 'max:1000'],
                'conditions' => ['nullable', 'string', 'max:1000'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ]);

            $emergencyCard = EmergencyCard::where('patient_id', $patient->id)->first();
            if (!$emergencyCard) {
                $emergencyCard = new EmergencyCard();
                $emergencyCard->patient_id = $patient->id;
                $emergencyCard->qr_token = Str::random(32);
            }

            $emergencyCard->is_public = $request->boolean('is_public');
            $emergencyCard->blood_group = $patient->blood_group;
            $emergencyCard->allergies = $emergencyData['allergies'] ?? '';
            $emergencyCard->medications = $emergencyData['medications'] ?? '';
            $emergencyCard->conditions = $emergencyData['conditions'] ?? '';
            $emergencyCard->notes = $emergencyData['notes'] ?? '';
            $emergencyCard->save();

        } elseif ($user->role === 'Doctor') {
            $doctor = $user->doctor ?? Doctor::create(['user_id' => $user->id]);

            $doctorData = $request->validate([
                'license_number' => ['required', 'string', 'max:100'],
                'specialization' => ['required', 'string', 'max:255'],
                'hospital' => ['required', 'string', 'max:255'],
                'bio' => ['nullable', 'string', 'max:1000'],
            ]);

            $doctor->update($doctorData);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
