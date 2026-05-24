<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Record;
use App\Models\AccessGrant;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $user = Auth::user();

        if (empty($query)) {
            return response()->json([]);
        }

        $results = [];

        if ($user->role === 'Patient') {
            $patient = $user->patient;

            // Search records
            $records = Record::where('patient_id', $patient->id)
                ->where(function($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('type', 'LIKE', "%{$query}%");
                })
                ->take(5)
                ->get();

            foreach ($records as $record) {
                $results[] = [
                    'title' => $record->title,
                    'category' => 'Medical Records',
                    'url' => $record->file_path,
                    'icon' => $record->type === 'Lab Report' ? '🩸' : ($record->type === 'Prescription' ? '💊' : '📄'),
                    'detail' => $record->type . ' - ' . ($record->is_critical ? 'Critical' : 'Normal')
                ];
            }

            // Search authorized doctors
            $activeGrants = AccessGrant::where('patient_id', $patient->id)
                ->where('is_active', true)
                ->get();
            
            $doctorUserIds = [];
            foreach ($activeGrants as $grant) {
                if ($grant->doctor && $grant->doctor->user) {
                    $doctorUserIds[] = $grant->doctor->user_id;
                }
            }

            $doctors = User::whereIn('_id', $doctorUserIds)
                ->where('name', 'LIKE', "%{$query}%")
                ->take(3)
                ->get();

            foreach ($doctors as $doc) {
                $results[] = [
                    'title' => $doc->name,
                    'category' => 'Authorized Doctors',
                    'url' => route('dashboard'),
                    'icon' => '👨‍⚕️',
                    'detail' => $doc->doctor->specialization . ' @ ' . $doc->doctor->hospital
                ];
            }

            // Navigation short-cuts
            $navs = [
                ['title' => 'Emergency Card details', 'detail' => 'Open QR Emergency Card settings', 'url' => route('profile.edit'), 'icon' => '🎴'],
                ['title' => 'Profile Settings', 'detail' => 'Update contact, passwords, demographics', 'url' => route('profile.edit'), 'icon' => '👤'],
                ['title' => 'Recycle Bin', 'detail' => 'View soft-deleted records archive', 'url' => route('recycle-bin.index'), 'icon' => '🗑️'],
            ];

            foreach ($navs as $nav) {
                if (stripos($nav['title'], $query) !== false || stripos($nav['detail'], $query) !== false) {
                    $results[] = [
                        'title' => $nav['title'],
                        'category' => 'Shortcuts',
                        'url' => $nav['url'],
                        'icon' => $nav['icon'],
                        'detail' => $nav['detail']
                    ];
                }
            }
        } elseif ($user->role === 'Doctor') {
            $doctor = $user->doctor;

            // Search for patients that the doctor has access to
            $grants = AccessGrant::where('doctor_id', $doctor->id)
                ->where('is_active', true)
                ->with('patient.user')
                ->get();

            foreach ($grants as $grant) {
                if ($grant->patient && $grant->patient->user) {
                    $patientUser = $grant->patient->user;
                    if (stripos($patientUser->name, $query) !== false || stripos($patientUser->email, $query) !== false) {
                        $results[] = [
                            'title' => $patientUser->name,
                            'category' => 'My Patients',
                            'url' => route('records.patient', $grant->patient->id),
                            'icon' => '👤',
                            'detail' => 'View medical chart - Authorized access'
                        ];
                    }
                }
            }

            // Global search for any patient (by email or user_id)
            $globalPatients = User::where('role', 'Patient')
                ->where(function($q) use ($query) {
                    $q->where('email', 'LIKE', "%{$query}%")
                      ->orWhere('name', 'LIKE', "%{$query}%");
                })
                ->take(5)
                ->get();

            foreach ($globalPatients as $gp) {
                // Ensure not already in active cases
                $alreadyAdded = false;
                foreach ($results as $res) {
                    if ($res['title'] === $gp->name && $res['category'] === 'My Patients') {
                        $alreadyAdded = true;
                    }
                }

                if (!$alreadyAdded && $gp->patient) {
                    $results[] = [
                        'title' => $gp->name,
                        'category' => 'Global Patient Directory',
                        'url' => route('dashboard') . '?search=' . urlencode($gp->email),
                        'icon' => '🔍',
                        'detail' => 'Request Clinical Access - Email: ' . $gp->email
                    ];
                }
            }
        }

        return response()->json($results);
    }
}
