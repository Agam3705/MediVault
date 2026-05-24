<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AccessRequest;
use App\Models\AccessGrant;
use App\Models\AuditLog;
use App\Models\Patient;
use Illuminate\Support\Facades\Mail;
use App\Mail\MediVaultMail;
use Carbon\Carbon;

class ConsentController extends Controller
{
    /**
     * Doctor submits an access request to a patient.
     */
    public function request(Request $request)
    {
        $user   = Auth::user();
        $doctor = $user->doctor;

        if (!$doctor || !$doctor->isVerified()) {
            return redirect()->back()->withErrors(['error' => 'Only verified doctors can submit access requests.']);
        }

        $request->validate([
            'patient_id' => ['required', 'string'],
            'reason'     => ['required', 'string', 'min:20', 'max:500'],
            'duration'   => ['nullable', 'integer', 'in:1,7,30'],
        ]);

        // Prevent duplicate pending requests
        $existing = AccessRequest::where('doctor_id', $doctor->id)
            ->where('patient_id', $request->patient_id)
            ->where('status', 'pending')
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'You already have a pending access request for this patient.');
        }

        AccessRequest::create([
            'doctor_id'  => $doctor->id,
            'patient_id' => $request->patient_id,
            'reason'     => $request->reason,
            'status'     => 'pending',
            'expires_at' => Carbon::now()->addHours(48), // 48hr window to respond
        ]);

        AuditLog::create([
            'user_id'          => $user->id,
            'action'           => 'Submitted clinical access request',
            'target_type'      => 'Patient',
            'target_id'        => $request->patient_id,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        $patient = Patient::findOrFail($request->patient_id);
        if ($patient->user->getSetting('notify_on_access_request', true)) {
            Mail::to($patient->user->email)->queue(new MediVaultMail(
                'emails.access_requested',
                'New Clinical Access Request from Dr. ' . $user->name,
                [
                    'patientName' => $patient->user->name,
                    'doctorName' => $user->name,
                    'specialization' => $doctor->specialization,
                    'hospital' => $doctor->hospital,
                    'reason' => $request->reason,
                ]
            ));
        }

        \App\Models\Notification::create([
            'user_id' => $patient->user_id,
            'title' => '🔒 Access Requested',
            'message' => 'Dr. ' . $user->name . ' has requested access to your medical records.',
            'type' => 'info',
        ]);

        return redirect()->route('dashboard')->with('success', 'Access request submitted. The patient has 48 hours to respond.');
    }

    /**
     * Doctor withdraws a pending access request they sent.
     */
    public function withdraw(Request $request, $requestId)
    {
        $user   = Auth::user();
        $doctor = $user->doctor;

        if (!$doctor) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $accessRequest = AccessRequest::findOrFail($requestId);

        // Only the requesting doctor can withdraw
        if ((string)$accessRequest->doctor_id !== (string)$doctor->id) {
            abort(403, 'Unauthorized.');
        }

        if ($accessRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be withdrawn.');
        }

        $accessRequest->delete();

        AuditLog::create([
            'user_id'          => $user->id,
            'action'           => 'Withdrew consent access request',
            'target_type'      => 'Patient',
            'target_id'        => $accessRequest->patient_id,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Access request successfully withdrawn.');
    }

    /**
     * Patient approves an access request — creates an AccessGrant.
     */
    public function approve(Request $request, $requestId)
    {
        $user    = Auth::user();
        $patient = $user->patient;

        $accessRequest = AccessRequest::findOrFail($requestId);

        // IDOR: Only the target patient can approve
        if ((string) $accessRequest->patient_id !== (string) $patient->id) {
            abort(403, 'You are not authorized to approve this request.');
        }

        if ($accessRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been responded to.');
        }

        // Expire the request record
        $accessRequest->update([
            'status'       => 'approved',
            'responded_at' => Carbon::now(),
        ]);

        // Create the AccessGrant (active for 30 days)
        AccessGrant::create([
            'patient_id'            => $patient->id,
            'doctor_id'             => $accessRequest->doctor_id,
            'granted_at'            => Carbon::now(),
            'expires_at'            => Carbon::now()->addDays(30),
            'is_active'             => true,
            'access_type'           => 'read-write',
            'restricted_record_ids' => [],
        ]);

        AuditLog::create([
            'user_id'          => $user->id,
            'action'           => 'Approved clinical access request',
            'target_type'      => 'AccessRequest',
            'target_id'        => $requestId,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        $doctorUser = $accessRequest->doctor->user;
        Mail::to($doctorUser->email)->queue(new MediVaultMail(
            'emails.access_decision',
            'Clinical Access Request APPROVED',
            [
                'patientName' => $user->name,
                'doctorName' => $doctorUser->name,
                'approved' => true,
                'expiryDate' => Carbon::now()->addDays(30)->format('M d, Y H:i'),
            ]
        ));

        \App\Models\Notification::create([
            'user_id' => $doctorUser->id,
            'title' => '✅ Access Request Approved',
            'message' => 'Patient ' . $user->name . ' has approved your access request.',
            'type' => 'success',
        ]);

        return redirect()->route('dashboard')->with('success', 'Access granted. The doctor can now view your medical records for 30 days.');
    }

    /**
     * Patient denies an access request.
     */
    public function deny(Request $request, $requestId)
    {
        $user    = Auth::user();
        $patient = $user->patient;

        $accessRequest = AccessRequest::findOrFail($requestId);

        if ((string) $accessRequest->patient_id !== (string) $patient->id) {
            abort(403, 'You are not authorized to deny this request.');
        }

        $accessRequest->update([
            'status'       => 'denied',
            'responded_at' => Carbon::now(),
        ]);

        AuditLog::create([
            'user_id'          => $user->id,
            'action'           => 'Denied clinical access request',
            'target_type'      => 'AccessRequest',
            'target_id'        => $requestId,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        $doctorUser = $accessRequest->doctor->user;
        Mail::to($doctorUser->email)->queue(new MediVaultMail(
            'emails.access_decision',
            'Clinical Access Request DENIED',
            [
                'patientName' => $user->name,
                'doctorName' => $doctorUser->name,
                'approved' => false,
            ]
        ));

        \App\Models\Notification::create([
            'user_id' => $doctorUser->id,
            'title' => '❌ Access Request Denied',
            'message' => 'Patient ' . $user->name . ' has denied your access request.',
            'type' => 'warning',
        ]);

        return redirect()->route('dashboard')->with('success', 'Access request denied. The doctor has been notified.');
    }

    /**
     * Patient revokes an active consent grant immediately.
     */
    public function revoke(Request $request, $grantId)
    {
        $user    = Auth::user();
        $patient = $user->patient;

        $grant = AccessGrant::findOrFail($grantId);

        if ((string) $grant->patient_id !== (string) $patient->id) {
            abort(403, 'You are not authorized to revoke this grant.');
        }

        $grant->update([
            'is_active'      => false,
            'revoked_at'     => Carbon::now(),
            'revoked_reason' => 'Revoked by patient via dashboard.',
        ]);

        AuditLog::create([
            'user_id'          => $user->id,
            'action'           => 'Revoked clinical access grant',
            'target_type'      => 'AccessGrant',
            'target_id'        => $grantId,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        \App\Models\Notification::create([
            'user_id' => $grant->doctor->user->id,
            'title' => '🚨 Access Revoked',
            'message' => 'Patient ' . $user->name . ' has revoked your access to their medical records.',
            'type' => 'warning',
        ]);

        return redirect()->route('dashboard')->with('success', 'Doctor access has been immediately revoked.');
    }

    /**
     * Patient updates granular record visibility for a doctor grant.
     */
    public function updateVisibility(Request $request, $grantId)
    {
        $user = Auth::user();
        $patient = $user->patient;
        $grant = AccessGrant::findOrFail($grantId);

        if ((string)$grant->patient_id !== (string)$patient->id) {
            abort(403, 'Unauthorized.');
        }

        $restrictedRecordIds = $request->input('restricted_record_ids', []);

        $grant->update([
            'restricted_record_ids' => $restrictedRecordIds
        ]);

        // Audit Log
        AuditLog::create([
            'user_id'          => $user->id,
            'action'           => 'Updated granular record visibility settings',
            'target_type'      => 'AccessGrant',
            'target_id'        => $grantId,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        return redirect()->back()->with('success', 'Granular record visibility updated successfully.');
    }

    /**
     * Doctor overrides standard consent in emergency.
     */
    public function override(Request $request, $patientId)
    {
        $user = Auth::user();
        $doctor = $user->doctor;

        if (!$doctor || !$doctor->isVerified()) {
            abort(403, 'Only verified doctors can perform emergency override.');
        }

        $patient = \App\Models\Patient::findOrFail($patientId);

        // Check if there's already an active emergency grant
        $existing = AccessGrant::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->where('is_active', true)
            ->where('access_type', 'emergency-override')
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($existing) {
            return redirect()->route('records.patient', $patient->id)->with('success', 'You currently have an active emergency override session.');
        }

        // Create 1-hour active grant
        $grant = AccessGrant::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'granted_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addHour(), // Exactly 1 hour
            'is_active' => true,
            'access_type' => 'emergency-override',
            'restricted_record_ids' => [],
        ]);

        // Create high-severity AuditLog
        AuditLog::create([
            'user_id'          => $user->id,
            'action'           => 'Triggered Emergency Access Override',
            'target_type'      => 'Patient',
            'target_id'        => $patient->id,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => true, // Flagged unusual/emergency activity
        ]);

        // Send In-App notification to the patient
        \App\Models\Notification::create([
            'user_id' => $patient->user_id,
            'title' => '🚨 Emergency Access Override Triggered',
            'message' => 'Dr. ' . $user->name . ' has bypassed standard consent to access your medical records due to an emergency. Access expires in 1 hour.',
            'type' => 'emergency',
        ]);

        return redirect()->route('records.patient', $patient->id)->with('success', 'Emergency override activated. Access granted for 1 hour. Patient has been notified.');
    }

    /**
     * Patient directly grants standard records access to a doctor.
     */
    public function grantDirectAccess(Request $request)
    {
        $user = Auth::user();
        $patient = $user->patient;

        if (!$patient) {
            abort(403);
        }

        $request->validate([
            'doctor_id' => ['required', 'string'],
            'duration'  => ['required', 'integer', 'in:1,7,30,365'], // Days
        ]);

        $doctor = Doctor::findOrFail($request->doctor_id);

        $expiresAt = $request->duration == 365 ? null : Carbon::now()->addDays((int)$request->duration);

        // Check if there is already an active grant
        $existing = AccessGrant::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
            })->first();

        if ($existing) {
            $existing->update(['expires_at' => $expiresAt]);
        } else {
            AccessGrant::create([
                'patient_id' => $patient->id,
                'doctor_id'  => $doctor->id,
                'granted_at' => Carbon::now(),
                'expires_at' => $expiresAt,
                'is_active'  => true,
                'access_type' => 'read-write',
                'restricted_record_ids' => [],
            ]);
        }

        // Notify doctor
        \App\Models\Notification::create([
            'user_id' => $doctor->user_id,
            'title'   => '🔒 Access Granted Directly',
            'message' => 'Patient ' . $user->name . ' has directly granted you access to their medical records.',
            'type'    => 'success',
        ]);

        // Audit Log
        AuditLog::create([
            'user_id'          => $user->id,
            'action'           => 'Directly granted clinical access to doctor',
            'target_type'      => 'Doctor',
            'target_id'        => $doctor->id,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        return redirect()->back()->with('success', 'Access successfully granted to Dr. ' . $doctor->user->name . '.');
    }
}
