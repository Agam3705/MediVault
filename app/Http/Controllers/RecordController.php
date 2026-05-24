<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Record;
use App\Models\RecordVersion;
use App\Models\Patient;
use App\Models\AccessGrant;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\MediVaultMail;
use Carbon\Carbon;

class RecordController extends Controller
{
    /**
     * Show form to create a new medical record.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $patient = null;
        $activeGrants = null;

        if ($user->role === 'Doctor') {
            $patientId = $request->query('patient_id');
            if ($patientId) {
                $patient = Patient::findOrFail($patientId);
                
                // Confirm active grant exists
                $doctor = $user->doctor;
                $hasGrant = AccessGrant::where('doctor_id', $doctor->id)
                    ->where('patient_id', $patient->id)
                    ->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
                    })->exists();

                if (!$hasGrant) {
                    abort(403, 'You do not have active consent for this patient.');
                }
            } else {
                // Fetch all patients they have active cases for
                $activeGrants = AccessGrant::where('doctor_id', $user->doctor->id)
                    ->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
                    })
                    ->with('patient.user')
                    ->get();
            }
        }

        return view('records.create', [
            'patient' => $patient,
            'activeGrants' => $activeGrants,
        ]);
    }

    /**
     * Store a new medical record.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'type'        => ['required', 'string', 'in:Lab Report,Prescription,Radiology,Vaccination,Discharge Summary,Other'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'file'        => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'is_critical' => ['nullable', 'boolean'],
        ]);

        $patient = $user->role === 'Patient'
            ? $user->patient
            : Patient::findOrFail($request->patient_id);

        // IDOR: Doctors can only add records to patients they have active consent for
        if ($user->role === 'Doctor') {
            $doctor = $user->doctor;
            $hasGrant = AccessGrant::where('doctor_id', $doctor->id)
                ->where('patient_id', $patient->id)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
                })->exists();

            if (!$hasGrant) {
                return redirect()->back()->withErrors(['error' => 'You do not have authorized access to add records for this patient.']);
            }
        }

        // Handle file upload (Cloudinary or local fallback)
        $filePath = null;
        $fileType = null;
        if ($request->hasFile('file')) {
            try {
                $uploaded = cloudinary()->upload($request->file('file')->getRealPath(), [
                    'folder' => 'medivault/records',
                    'resource_type' => 'auto',
                ]);
                $filePath = $uploaded->getSecurePath();
                $fileType = $request->file('file')->getMimeType();
            } catch (\Exception $e) {
                // Fallback: store locally
                $storedPath = $request->file('file')->store('records', 'local');
                $filePath   = $storedPath;
                $fileType   = $request->file('file')->getMimeType();
            }
        }

        $record = Record::create([
            'patient_id'  => $patient->id,
            'created_by'  => $user->id,
            'type'        => $request->type,
            'title'       => $request->title,
            'description' => $request->description,
            'file_path'   => $filePath,
            'file_type'   => $fileType,
            'is_critical' => $request->boolean('is_critical'),
        ]);

        // Audit log
        AuditLog::create([
            'user_id'          => $user->id,
            'action'           => 'Created medical record',
            'target_type'      => 'Record',
            'target_id'        => $record->id,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        if ($patient->user->getSetting('notify_on_record_uploaded', true)) {
            Mail::to($patient->user->email)->queue(new MediVaultMail(
                'emails.record_uploaded',
                'New Medical Record Uploaded: ' . $record->title,
                [
                    'patientName' => $patient->user->name,
                    'recordTitle' => $record->title,
                    'uploaderName' => $user->name,
                    'timestamp' => now()->format('M d, Y H:i:s'),
                ]
            ));
        }

        \App\Models\Notification::create([
            'user_id' => $patient->user_id,
            'title' => '📄 New Document Uploaded',
            'message' => $user->role === 'Doctor' 
                ? 'Dr. ' . $user->name . ' uploaded a new medical record: ' . $record->title
                : 'You uploaded a new medical record: ' . $record->title,
            'type' => 'success',
        ]);

        return redirect()->route('dashboard')->with('success', 'Medical record "' . $record->title . '" has been saved successfully.');
    }

    /**
     * View all records of a patient (for authorized Doctors).
     */
    public function patientRecords(Request $request, $patientId)
    {
        $user   = Auth::user();
        $doctor = $user->doctor;

        $patient = Patient::findOrFail($patientId);

        // IDOR Prevention: Confirm active grant exists
        $grant = AccessGrant::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
            })->first();

        if (!$grant) {
            abort(403, 'You do not have authorized clinical access to this patient\'s records.');
        }

        // Fetch records, filtering out any the patient has restricted from this doctor
        $restrictedIds = $grant->restricted_record_ids ?? [];

        $records = Record::where('patient_id', $patient->id)
            ->whereNotIn('id', $restrictedIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // Immutable Audit: Log this clinical access event
        AuditLog::create([
            'user_id'          => $user->id,
            'action'           => 'Viewed medical record',
            'target_type'      => 'Record',
            'target_id'        => $patient->id,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        if ($patient->user->getSetting('notify_on_record_viewed', true)) {
            Mail::to($patient->user->email)->queue(new MediVaultMail(
                'emails.record_viewed',
                'Your Medical Vault was accessed by Dr. ' . $user->name,
                [
                    'patientName' => $patient->user->name,
                    'doctorName' => $user->name,
                    'specialization' => $doctor->specialization,
                    'recordTitle' => 'Complete Medical File Vault Index',
                    'timestamp' => now()->format('M d, Y H:i:s'),
                ]
            ));
        }

        \App\Models\Notification::create([
            'user_id' => $patient->user_id,
            'title' => '🔍 Vault Accessed',
            'message' => 'Dr. ' . $user->name . ' accessed your medical record vault.',
            'type' => 'info',
        ]);

        return view('records.patient', [
            'patient' => $patient,
            'records' => $records,
            'grant'   => $grant,
        ]);
    }

    /**
     * Soft-delete a medical record (patients only can delete their own records).
     */
    public function destroy(Request $request, $recordId)
    {
        $user   = Auth::user();
        $record = Record::findOrFail($recordId);

        // IDOR: only the patient who owns the record can delete it
        $patient = $user->patient;
        if (!$patient || (string) $record->patient_id !== (string) $patient->id) {
            abort(403, 'You are not authorized to delete this record.');
        }

        // Save a version snapshot before deletion
        RecordVersion::create([
            'record_id'    => $record->id,
            'changed_by'   => $user->id,
            'snapshot_json' => $record->toArray(),
            'change_note'  => 'Record soft-deleted by patient.',
        ]);

        $record->delete(); // Soft delete (SoftDeletes trait)

        AuditLog::create([
            'user_id'          => $user->id,
            'action'           => 'Soft-deleted medical record',
            'target_type'      => 'Record',
            'target_id'        => $record->id,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Record "' . $record->title . '" has been moved to the deleted archive. It can be recovered.');
    }

    /**
     * Show edit form for record.
     */
    public function edit($recordId)
    {
        $user = Auth::user();
        $record = Record::findOrFail($recordId);

        if ($user->role === 'Patient') {
            $patient = $user->patient;
            if (!$patient || (string)$record->patient_id !== (string)$patient->id) {
                abort(403, 'Unauthorized.');
            }
        } elseif ($user->role === 'Doctor') {
            $doctor = $user->doctor;
            $hasGrant = AccessGrant::where('doctor_id', $doctor->id)
                ->where('patient_id', $record->patient_id)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
                })->exists();

            if (!$hasGrant) {
                abort(403, 'You do not have active consent for this patient.');
            }
        } else {
            abort(403, 'Unauthorized.');
        }

        return view('records.edit', [
            'record' => $record
        ]);
    }

    /**
     * Update a medical record and capture a snapshot in record_versions.
     */
    public function update(Request $request, $recordId)
    {
        $user = Auth::user();
        $record = Record::findOrFail($recordId);

        $changeNote = '';
        if ($user->role === 'Patient') {
            $patient = $user->patient;
            if (!$patient || (string)$record->patient_id !== (string)$patient->id) {
                abort(403, 'Unauthorized.');
            }
            $changeNote = 'Record modified by patient.';
        } elseif ($user->role === 'Doctor') {
            $doctor = $user->doctor;
            $hasGrant = AccessGrant::where('doctor_id', $doctor->id)
                ->where('patient_id', $record->patient_id)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
                })->exists();

            if (!$hasGrant) {
                abort(403, 'You do not have active consent for this patient.');
            }
            $changeNote = 'Record modified by Dr. ' . $user->name;
        } else {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', 'string', 'in:Lab Report,Prescription,Radiology,Vaccination,Discharge Summary,Other'],
            'is_critical' => ['nullable', 'boolean'],
        ]);

        // Save a version snapshot before updating
        RecordVersion::create([
            'record_id' => $record->id,
            'changed_by' => $user->id,
            'snapshot_json' => $record->toArray(),
            'change_note' => $changeNote,
        ]);

        $record->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'is_critical' => $request->boolean('is_critical'),
        ]);

        // Audit Log
        AuditLog::create([
            'user_id'          => $user->id,
            'action'           => 'Updated medical record',
            'target_type'      => 'Record',
            'target_id'        => $record->id,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        if ($user->role === 'Doctor') {
            return redirect()->route('records.patient', $record->patient_id)->with('success', 'Medical record updated and snapshot saved to version history.');
        }

        return redirect()->route('dashboard')->with('success', 'Medical record updated and snapshot saved to version history.');
    }

    /**
     * Show version history list for a record.
     */
    public function versions($recordId)
    {
        $user = Auth::user();
        $record = Record::findOrFail($recordId);

        $patient = $user->patient;
        if (!$patient || (string)$record->patient_id !== (string)$patient->id) {
            abort(403, 'Unauthorized.');
        }

        $versions = RecordVersion::where('record_id', $record->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('records.versions', [
            'record' => $record,
            'versions' => $versions
        ]);
    }
}
