<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Record;
use App\Models\AuditLog;

class RecycleBinController extends Controller
{
    /**
     * Display a listing of soft-deleted records.
     */
    public function index()
    {
        $user = Auth::user();
        $patient = $user->patient;

        if (!$patient) {
            abort(403, 'Unauthorized.');
        }

        $deletedRecords = Record::onlyTrashed()
            ->where('patient_id', $patient->id)
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('records.recycle-bin', [
            'records' => $deletedRecords
        ]);
    }

    /**
     * Restore a soft-deleted record.
     */
    public function restore(Request $request, $recordId)
    {
        $user = Auth::user();
        $patient = $user->patient;

        if (!$patient) {
            abort(403, 'Unauthorized.');
        }

        $record = Record::onlyTrashed()
            ->where('patient_id', $patient->id)
            ->where('id', $recordId)
            ->firstOrFail();

        $record->restore();

        // Audit Log
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Restored medical record from recycle bin',
            'target_type' => 'Record',
            'target_id' => $record->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'unusual_activity' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Medical record "' . $record->title . '" has been restored successfully.');
    }

    /**
     * Permanently delete a record and its versions.
     */
    public function forceDelete(Request $request, $recordId)
    {
        $user = Auth::user();
        $patient = $user->patient;

        if (!$patient) {
            abort(403, 'Unauthorized.');
        }

        $record = Record::onlyTrashed()
            ->where('patient_id', $patient->id)
            ->where('id', $recordId)
            ->firstOrFail();

        $title = $record->title;

        // Delete all versions first
        $record->versions()->delete();

        // Permanently delete the record
        $record->forceDelete();

        // Audit Log
        AuditLog::create([
            'user_id'          => $user->id,
            'action'           => 'Permanently deleted medical record and history',
            'target_type'      => 'Record',
            'target_id'        => $recordId,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'session_id'       => session()->getId(),
            'unusual_activity' => false,
        ]);

        return redirect()->route('recycle-bin.index')->with('success', 'Medical record "' . $title . '" and all its versions have been permanently deleted.');
    }
}
