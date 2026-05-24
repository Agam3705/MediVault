<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Doctor;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\MediVaultMail;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Approve (verify) a doctor's license.
     */
    public function verifyDoctor(Request $request, $doctorId)
    {
        $admin  = Auth::user();
        $doctor = Doctor::findOrFail($doctorId);

        if ($doctor->isVerified()) {
            return redirect()->back()->with('error', 'This doctor is already verified.');
        }

        $doctor->update([
            'verified_at' => Carbon::now(),
            'verified_by' => $admin->id,
        ]);

        AuditLog::create([
            'user_id'          => $admin->id,
            'action'           => 'Admin approved doctor license verification',
            'target_type'      => 'Doctor',
            'target_id'        => $doctorId,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        Mail::to($doctor->user->email)->queue(new MediVaultMail(
            'emails.doctor_verified',
            'Your Practitioner License is Approved!',
            [
                'doctorName' => $doctor->user->name,
                'licenseNumber' => $doctor->license_number,
                'hospital' => $doctor->hospital,
            ]
        ));

        return redirect()->route('dashboard')->with('success', 'Dr. ' . $doctor->user->name . ' has been verified and can now access patient records.');
    }

    /**
     * Reject a doctor's license application.
     */
    public function rejectDoctor(Request $request, $doctorId)
    {
        $admin  = Auth::user();
        $doctor = Doctor::findOrFail($doctorId);

        // Mark user as inactive so they see a rejection message
        $doctor->user->update(['is_active' => false]);

        AuditLog::create([
            'user_id'          => $admin->id,
            'action'           => 'Admin rejected doctor license verification',
            'target_type'      => 'Doctor',
            'target_id'        => $doctorId,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Doctor application rejected and account suspended.');
    }

    /**
     * Force-revoke all doctor access grants (Administrative kill-switch).
     */
    public function forceRevokeAll(Request $request)
    {
        $admin = Auth::user();

        // Mark all active grants as inactive
        $count = \App\Models\AccessGrant::where('is_active', true)->count();
        \App\Models\AccessGrant::where('is_active', true)->update([
            'is_active' => false,
            'revoked_at' => Carbon::now(),
            'revoked_reason' => 'Administrative override: Force revoked by Administrator.'
        ]);

        // Audit Log
        AuditLog::create([
            'user_id'          => $admin->id,
            'action'           => 'Admin triggered global force-revocation of all doctor access grants',
            'target_type'      => 'System',
            'target_id'        => $admin->id,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => true, // Flagged severity
        ]);

        return redirect()->route('dashboard')->with('success', 'Administrative security action executed. Immediately revoked ' . $count . ' active consent grants across the platform.');
    }

    /**
     * Revoke verification status of a doctor.
     */
    public function revokeVerification(Request $request, $doctorId)
    {
        $admin = Auth::user();
        $doctor = Doctor::findOrFail($doctorId);

        if (!$doctor->isVerified()) {
            return redirect()->back()->with('error', 'This doctor is not verified.');
        }

        $doctor->update([
            'verified_at' => null,
            'verified_by' => null,
        ]);

        AuditLog::create([
            'user_id'          => $admin->id,
            'action'           => 'Admin revoked doctor verification status',
            'target_type'      => 'Doctor',
            'target_id'        => $doctorId,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        \App\Models\Notification::create([
            'user_id' => $doctor->user_id,
            'title' => '⚠️ Verification Revoked',
            'message' => 'Your medical practitioner verification has been revoked by the administrator.',
            'type' => 'warning',
        ]);

        return redirect()->back()->with('success', 'Dr. ' . $doctor->user->name . '\'s verification status has been revoked.');
    }

    /**
     * Suspend/Unsuspend user account.
     */
    public function toggleUserStatus(Request $request, $userId)
    {
        $admin = Auth::user();
        $user = \App\Models\User::findOrFail($userId);

        if ((string)$admin->id === (string)$user->id) {
            return redirect()->back()->with('error', 'You cannot suspend your own account.');
        }

        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);

        AuditLog::create([
            'user_id'          => $admin->id,
            'action'           => $newStatus ? 'Admin unsuspended user account' : 'Admin suspended user account',
            'target_type'      => 'User',
            'target_id'        => $user->id,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        return redirect()->back()->with('success', 'User account ' . $user->name . ' is now ' . ($newStatus ? 'Active' : 'Suspended') . '.');
    }

    /**
     * Permanently delete a user account and associated profiles.
     */
    public function deleteUser(Request $request, $userId)
    {
        $admin = Auth::user();
        $user = \App\Models\User::findOrFail($userId);

        if ((string)$admin->id === (string)$user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;

        // Delete associated records, grants, requests, etc
        if ($user->role === 'Patient' && $user->patient) {
            $patient = $user->patient;
            // Delete patient records
            \App\Models\Record::where('patient_id', $patient->id)->forceDelete();
            // Delete patient grants & requests
            \App\Models\AccessGrant::where('patient_id', $patient->id)->delete();
            \App\Models\AccessRequest::where('patient_id', $patient->id)->delete();
            // Delete emergency card
            \App\Models\EmergencyCard::where('patient_id', $patient->id)->delete();
            // Delete patient
            $patient->delete();
        } elseif ($user->role === 'Doctor' && $user->doctor) {
            $doctor = $user->doctor;
            // Delete doctor grants & requests
            \App\Models\AccessGrant::where('doctor_id', $doctor->id)->delete();
            \App\Models\AccessRequest::where('doctor_id', $doctor->id)->delete();
            // Delete doctor
            $doctor->delete();
        }

        // Delete user settings, sessions, messages
        \App\Models\UserSetting::where('user_id', $user->id)->delete();
        \App\Models\SessionsTracker::where('user_id', $user->id)->delete();
        \App\Models\Message::where('sender_id', $user->id)->orWhere('receiver_id', $user->id)->delete();
        \App\Models\Notification::where('user_id', $user->id)->delete();

        // Delete User
        $user->delete();

        AuditLog::create([
            'user_id'          => $admin->id,
            'action'           => 'Admin permanently deleted user account and related records',
            'target_type'      => 'User',
            'target_id'        => $userId,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        return redirect()->back()->with('success', 'User account ' . $userName . ' and all associated profiles/records have been permanently deleted.');
    }
}
