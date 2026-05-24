<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Record;
use App\Models\AccessGrant;
use App\Models\AccessRequest;
use App\Models\AuditLog;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Render the role-specific dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'Admin') {
            return $this->adminDashboard($request);
        } elseif ($user->role === 'Doctor') {
            return $this->doctorDashboard($request);
        } else {
            return $this->patientDashboard($request);
        }
    }

    /**
     * Patient Dashboard View.
     */
    protected function patientDashboard(Request $request)
    {
        $user = Auth::user();
        $patient = $user->patient;

        // In case the user registered but patient document failed to create
        if (!$patient) {
            $patient = Patient::create(['user_id' => $user->id]);
        }

        // Fetch recent records
        $records = Record::where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch active consents granted to doctors
        $activeGrants = AccessGrant::where('patient_id', $patient->id)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', Carbon::now());
            })
            ->with('doctor.user')
            ->get();

        // Fetch pending requests from doctors
        $pendingRequests = AccessRequest::where('patient_id', $patient->id)
            ->where('status', 'pending')
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', Carbon::now());
            })
            ->with('doctor.user')
            ->get();

        // Audit log filters (patient sees logs related to their records)
        $auditSearch  = $request->input('audit_search');
        $auditAction  = $request->input('audit_action');
        $auditUnusual = $request->input('audit_unusual');

        $recordIds = $records->pluck('id')->toArray();
        $logsQuery = AuditLog::where(function($query) use ($recordIds, $patient) {
                $query->whereIn('target_id', $recordIds)
                      ->orWhere('target_id', $patient->id);
            })
            ->orderBy('created_at', 'desc')
            ->with('user');

        if ($auditSearch) {
            $logsQuery->where(function($q) use ($auditSearch) {
                $q->where('action', 'LIKE', "%{$auditSearch}%")
                  ->orWhere('ip_address', 'LIKE', "%{$auditSearch}%");
            });
        }
        if ($auditAction) {
            $logsQuery->where('action', $auditAction);
        }
        if ($auditUnusual === '1') {
            $logsQuery->where('unusual_activity', true);
        }

        $accessLogs = $logsQuery->take(50)->get();

        // Distinct actions for the filter dropdown
        $allPatientAuditActions = AuditLog::whereIn('action', [
            'Viewed medical record', 'Downloaded clinical record', 'Scanned emergency QR card'
        ])->pluck('action')->unique()->sort()->values();

        // Login sessions for inline display
        $loginSessions = \App\Models\SessionsTracker::where('user_id', $user->id)
            ->orderBy('last_active_at', 'desc')
            ->take(10)
            ->get();

        // Emergency Card setup
        $emergencyCard = \App\Models\EmergencyCard::where('patient_id', $patient->id)->first();

        // 3. Simple Search capability for verified doctors
        $doctorSearch = $request->input('doctor_search');
        $doctorResults = [];
        if (!empty($doctorSearch)) {
            // Find verified doctors by user name, specialization, or hospital
            $matchingDocUsers = User::where('role', 'Doctor')
                ->where('name', 'LIKE', "%{$doctorSearch}%")
                ->get();
            
            $docUserIds = $matchingDocUsers->pluck('id')->toArray();
            
            $doctorResults = Doctor::where(function($q) use ($docUserIds, $doctorSearch) {
                $q->whereIn('user_id', $docUserIds)
                  ->orWhere('specialization', 'LIKE', "%{$doctorSearch}%")
                  ->orWhere('hospital', 'LIKE', "%{$doctorSearch}%");
            })
            ->whereNotNull('verified_at')
            ->with('user')
            ->get();
        }

        return view('dashboard.patient', [
            'user'                   => $user,
            'patient'                => $patient,
            'records'                => $records,
            'activeGrants'           => $activeGrants,
            'pendingRequests'        => $pendingRequests,
            'accessLogs'             => $accessLogs,
            'emergencyCard'          => $emergencyCard,
            'completeness'           => $patient->completeness_score,
            'doctorResults'          => $doctorResults,
            'doctorSearch'           => $doctorSearch,
            'loginSessions'          => $loginSessions,
            'auditSearch'            => $auditSearch,
            'auditAction'            => $auditAction,
            'auditUnusual'           => $auditUnusual,
            'allPatientAuditActions' => $allPatientAuditActions,
        ]);
    }

    /**
     * Doctor Dashboard View.
     */
    protected function doctorDashboard(Request $request)
    {
        $user = Auth::user();
        $doctor = $user->doctor;

        if (!$doctor) {
            $doctor = Doctor::create(['user_id' => $user->id]);
        }

        // If unverified, render the pending verification screen
        if (!$doctor->isVerified()) {
            return view('dashboard.doctor-pending', [
                'user' => $user,
                'doctor' => $doctor,
            ]);
        }

        // Verified Doctor Logic:
        // 1. Fetch active patient cases where grant is active and not expired
        $activeCases = AccessGrant::where('doctor_id', $doctor->id)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', Carbon::now());
            })
            ->with('patient.user')
            ->get();

        // 2. Fetch pending access requests sent by this doctor
        $sentRequests = AccessRequest::where('doctor_id', $doctor->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->with('patient.user')
            ->get();

        // 3. Filtered Search capability for patients
        $searchQuery = $request->input('search');
        $bloodGroupFilter = $request->input('blood_group');
        $genderFilter = $request->input('gender');
        $ageRange = $request->input('age_range');
        $minAge = $request->input('min_age');
        $maxAge = $request->input('max_age');
        
        if ($ageRange) {
            $parts = explode('-', $ageRange);
            if (count($parts) === 2) {
                $minAge = $parts[0];
                $maxAge = $parts[1];
            }
        }
        
        $searchResults = [];
        
        if ($searchQuery || $bloodGroupFilter || $genderFilter || ($minAge !== null && $minAge !== '') || ($maxAge !== null && $maxAge !== '')) {
            $patientQuery = Patient::query();
            
            if ($bloodGroupFilter) {
                $patientQuery->where('blood_group', $bloodGroupFilter);
            }
            
            if ($genderFilter) {
                $patientQuery->where('gender', $genderFilter);
            }
            
            if ($minAge !== null && $minAge !== '') {
                $patientQuery->where('dob', '<=', now()->subYears((int)$minAge));
            }
            
            if ($maxAge !== null && $maxAge !== '') {
                $patientQuery->where('dob', '>=', now()->subYears((int)$maxAge + 1));
            }
            
            if ($searchQuery) {
                $matchingUserIds = User::where('role', 'Patient')
                    ->where(function($q) use ($searchQuery) {
                        $q->where('name', 'LIKE', "%{$searchQuery}%")
                          ->orWhere('email', 'LIKE', "%{$searchQuery}%");
                    })
                    ->pluck('id')
                    ->toArray();
                
                $patientQuery->where(function($q) use ($matchingUserIds, $searchQuery) {
                    $q->whereIn('user_id', $matchingUserIds)
                      ->orWhere('phone', 'LIKE', "%{$searchQuery}%")
                      ->orWhere('address', 'LIKE', "%{$searchQuery}%");
                });
            }
            
            $searchResults = $patientQuery->with('user')->get();
        }

        // Doctor audit log filters (doctor sees their OWN actions)
        $docAuditSearch  = $request->input('doc_audit_search');
        $docAuditAction  = $request->input('doc_audit_action');
        $docAuditUnusual = $request->input('doc_audit_unusual');

        $docLogsQuery = AuditLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->with('user');

        if ($docAuditSearch) {
            $docLogsQuery->where(function($q) use ($docAuditSearch) {
                $q->where('action', 'LIKE', "%{$docAuditSearch}%")
                  ->orWhere('ip_address', 'LIKE', "%{$docAuditSearch}%");
            });
        }
        if ($docAuditAction) {
            $docLogsQuery->where('action', $docAuditAction);
        }
        if ($docAuditUnusual === '1') {
            $docLogsQuery->where('unusual_activity', true);
        }

        $doctorAuditLogs = $docLogsQuery->take(50)->get();

        // Distinct actions for the doctor filter dropdown
        $allDoctorAuditActions = AuditLog::where('user_id', $user->id)
            ->pluck('action')->unique()->sort()->values();

        // Login sessions for inline display
        $doctorLoginSessions = \App\Models\SessionsTracker::where('user_id', $user->id)
            ->orderBy('last_active_at', 'desc')
            ->take(10)
            ->get();

        return view('dashboard.doctor', [
            'user'                  => $user,
            'doctor'                => $doctor,
            'activeCases'           => $activeCases,
            'sentRequests'          => $sentRequests,
            'searchResults'         => $searchResults,
            'searchQuery'           => $searchQuery,
            'bloodGroupFilter'      => $bloodGroupFilter,
            'genderFilter'          => $genderFilter,
            'minAge'                => $minAge,
            'maxAge'                => $maxAge,
            'ageRange'              => $ageRange,
            'doctorAuditLogs'       => $doctorAuditLogs,
            'allDoctorAuditActions' => $allDoctorAuditActions,
            'doctorLoginSessions'   => $doctorLoginSessions,
            'docAuditSearch'        => $docAuditSearch,
            'docAuditAction'        => $docAuditAction,
            'docAuditUnusual'       => $docAuditUnusual,
        ]);
    }

    /**
     * Doctor: My All-Time Patient List (regardless of current access).
     */
    public function myPatients(Request $request)
    {
        $user   = Auth::user();
        $doctor = $user->doctor;

        if (!$doctor || !$doctor->isVerified()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized.');
        }

        $search = $request->input('search');
        $status = $request->input('status');

        // Fetch ALL grants ever made to this doctor (active + expired + revoked)
        $allGrants = AccessGrant::where('doctor_id', $doctor->id)
            ->with('patient.user')
            ->orderBy('granted_at', 'desc')
            ->get();

        $activeCount   = 0;
        $expiredCount  = 0;
        $allPatients   = collect();
        $seenPatientIds = [];

        foreach ($allGrants as $grant) {
            if (!$grant->patient || !$grant->patient->user) continue;

            $patientId = (string)$grant->patient->id;

            // De-duplicate: if already seen, skip to avoid duplicates
            if (in_array($patientId, $seenPatientIds)) continue;
            $seenPatientIds[] = $patientId;

            $isActive = $grant->is_active
                && is_null($grant->revoked_at)
                && ($grant->expires_at === null || $grant->expires_at->isFuture());

            if ($isActive) $activeCount++;
            else $expiredCount++;

            $allPatients->push([
                'patient'   => $grant->patient,
                'grant'     => $grant,
                'is_active' => $isActive,
            ]);
        }

        // Apply search filter
        if ($search) {
            $allPatients = $allPatients->filter(function($entry) use ($search) {
                return str_contains(strtolower($entry['patient']->user->name), strtolower($search))
                    || str_contains(strtolower($entry['patient']->user->email), strtolower($search));
            })->values();
        }

        // Apply status filter
        if ($status === 'active') {
            $allPatients = $allPatients->filter(fn($e) => $e['is_active'])->values();
        } elseif ($status === 'expired') {
            $allPatients = $allPatients->filter(fn($e) => !$e['is_active'])->values();
        }

        // Count records viewed by this doctor across all patients
        $totalRecordsViewed = AuditLog::where('user_id', $user->id)
            ->where('action', 'Viewed medical record')
            ->count();

        return view('doctors.patients', [
            'doctor'             => $doctor,
            'allPatients'        => $allPatients,
            'totalPatients'      => count($seenPatientIds),
            'activeCount'        => $activeCount,
            'expiredCount'       => $expiredCount,
            'totalRecordsViewed' => $totalRecordsViewed,
            'search'             => $search,
            'status'             => $status,
        ]);
    }

    /**
     * Admin Dashboard View.
     */
    protected function adminDashboard(Request $request)
    {
        $user = Auth::user();

        // 1. Get statistics
        $stats = [
            'total_users' => User::count(),
            'total_patients' => Patient::count(),
            'total_doctors' => Doctor::count(),
            'verified_doctors' => Doctor::whereNotNull('verified_at')->count(),
            'pending_doctors' => Doctor::whereNull('verified_at')->count(),
            'total_records' => Record::count(),
            'total_audits' => AuditLog::count(),
        ];

        // 2. Get pending verification doctor queue
        $pendingDoctors = Doctor::whereNull('verified_at')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Get recent security audit logs — WITH filters
        $auditSearch   = $request->input('audit_search');
        $auditAction   = $request->input('audit_action');
        $auditRole     = $request->input('audit_role');
        $auditUnusual  = $request->input('audit_unusual');

        $logsQuery = AuditLog::orderBy('created_at', 'desc')->with('user');

        if ($auditSearch) {
            $matchingUserIds = User::where('name', 'LIKE', "%{$auditSearch}%")->pluck('id')->toArray();
            $logsQuery->where(function($q) use ($auditSearch, $matchingUserIds) {
                $q->whereIn('user_id', $matchingUserIds)
                  ->orWhere('action', 'LIKE', "%{$auditSearch}%")
                  ->orWhere('ip_address', 'LIKE', "%{$auditSearch}%");
            });
        }

        if ($auditAction) {
            $logsQuery->where('action', $auditAction);
        }

        if ($auditRole) {
            $usersWithRole = User::where('role', $auditRole)->pluck('id')->toArray();
            $logsQuery->whereIn('user_id', $usersWithRole);
        }

        if ($auditUnusual === '1') {
            $logsQuery->where('unusual_activity', true);
        }

        $recentLogs = $logsQuery->take(50)->get();

        // 4. Get support tickets
        $pendingTickets = \App\Models\SupportTicket::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $resolvedTickets = \App\Models\SupportTicket::where('status', 'resolved')
            ->with('user')
            ->orderBy('replied_at', 'desc')
            ->take(15)
            ->get();

        // 5. Get approved doctors & all users for administrative directories
        $approvedDoctors = Doctor::whereNotNull('verified_at')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $allUsers = User::with(['patient', 'doctor'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 6. Extra overview stats
        $activeGrantsCount = AccessGrant::where('is_active', true)
            ->where(function($q) { $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now()); })
            ->count();
        $pendingTicketsCount = \App\Models\SupportTicket::where('status', 'pending')->count();
        $unusualActivityCount = AuditLog::where('unusual_activity', true)->count();
        $recentRegistrations = User::orderBy('created_at', 'desc')->take(5)->get();

        // All distinct audit actions for filter dropdown
        $allAuditActions = AuditLog::pluck('action')->unique()->sort()->values();

        return view('dashboard.admin', [
            'user'                => $user,
            'stats'               => $stats,
            'pendingDoctors'      => $pendingDoctors,
            'recentLogs'          => $recentLogs,
            'pendingTickets'      => $pendingTickets,
            'resolvedTickets'     => $resolvedTickets,
            'approvedDoctors'     => $approvedDoctors,
            'allUsers'            => $allUsers,
            'activeGrantsCount'   => $activeGrantsCount,
            'pendingTicketsCount' => $pendingTicketsCount,
            'unusualActivityCount'=> $unusualActivityCount,
            'recentRegistrations' => $recentRegistrations,
            'allAuditActions'     => $allAuditActions,
            'auditSearch'         => $auditSearch,
            'auditAction'         => $auditAction,
            'auditRole'           => $auditRole,
            'auditUnusual'        => $auditUnusual,
        ]);
    }

    /**
     * Patient Doctor Directory View.
     */
    public function doctorDirectory(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'Patient') {
            abort(403, 'Unauthorized.');
        }

        $patient = $user->patient;
        if (!$patient) {
            $patient = Patient::create(['user_id' => $user->id]);
        }

        // 1. Simple Search capability for verified doctors
        $doctorSearch = $request->input('doctor_search');
        $doctorResults = [];
        if (!empty($doctorSearch)) {
            $matchingDocUsers = User::where('role', 'Doctor')
                ->where('name', 'LIKE', "%{$doctorSearch}%")
                ->get();
            
            $docUserIds = $matchingDocUsers->pluck('id')->toArray();
            
            $doctorResults = Doctor::where(function($q) use ($docUserIds, $doctorSearch) {
                $q->whereIn('user_id', $docUserIds)
                  ->orWhere('specialization', 'LIKE', "%{$doctorSearch}%")
                  ->orWhere('hospital', 'LIKE', "%{$doctorSearch}%");
            })
            ->whereNotNull('verified_at')
            ->with('user')
            ->get();
        }

        // 2. Fetch doctors in contact (active grants, pending requests, or existing chat threads)
        $activeGrants = AccessGrant::where('patient_id', $patient->id)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', Carbon::now());
            })
            ->pluck('doctor_id')
            ->toArray();

        $pendingRequests = AccessRequest::where('patient_id', $patient->id)
            ->where('status', 'pending')
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', Carbon::now());
            })
            ->pluck('doctor_id')
            ->toArray();

        // Doctors who have messages with this patient user
        $userIdsWithChat = \App\Models\Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->get()
            ->map(function($msg) use ($user) {
                return $msg->sender_id === $user->id ? $msg->receiver_id : $msg->sender_id;
            })
            ->unique()
            ->toArray();
        
        $chatDoctorIds = Doctor::whereIn('user_id', $userIdsWithChat)
            ->pluck('id')
            ->toArray();

        $contactDoctorIds = array_unique(array_merge($activeGrants, $pendingRequests, $chatDoctorIds));

        $contactDoctors = Doctor::whereIn('id', $contactDoctorIds)
            ->with('user')
            ->get();

        return view('doctors.index', [
            'user' => $user,
            'patient' => $patient,
            'doctorSearch' => $doctorSearch,
            'doctorResults' => $doctorResults,
            'contactDoctors' => $contactDoctors,
            'activeGrants' => AccessGrant::where('patient_id', $patient->id)->where('is_active', true)->get(),
            'pendingRequestsList' => AccessRequest::where('patient_id', $patient->id)->where('status', 'pending')->get(),
        ]);
    }
}
