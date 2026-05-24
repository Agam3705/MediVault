<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Record;
use App\Models\RecordVersion;
use App\Models\AccessRequest;
use App\Models\AccessGrant;
use App\Models\AuditLog;
use App\Models\EmergencyCard;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clean collections first since we are in a dev seeding process
        User::truncate();
        Patient::truncate();
        Doctor::truncate();
        Record::truncate();
        RecordVersion::truncate();
        AccessRequest::truncate();
        AccessGrant::truncate();
        AuditLog::truncate();
        EmergencyCard::truncate();
        UserSetting::truncate();

        // 1. Create Admins
        $adminUser = User::create([
            'name' => 'MediVault Administrator',
            'email' => 'admin@medivault.com',
            'password' => Hash::make('admin123'),
            'role' => 'Admin',
            'is_active' => true,
            'last_login_at' => Carbon::now()->subMinutes(15),
        ]);

        // 2. Create Doctors
        $doc1User = User::create([
            'name' => 'Dr. Amit Sharma',
            'email' => 'doctor.amit@medivault.com',
            'password' => Hash::make('doctor123'),
            'role' => 'Doctor',
            'is_active' => true,
            'last_login_at' => Carbon::now()->subHours(2),
        ]);

        $doc1 = Doctor::create([
            'user_id' => $doc1User->id,
            'license_number' => 'MCI-99281',
            'specialization' => 'Cardiology',
            'hospital' => 'Fortis Escorts Heart Institute, New Delhi',
            'bio' => 'Senior Interventional Cardiologist with over 15 years of experience specializing in coronary angioplasty and preventive heart care.',
            'phone' => '+91 99999 00001',
            'verified_at' => Carbon::now()->subDays(10),
            'verified_by' => $adminUser->id,
        ]);

        $doc2User = User::create([
            'name' => 'Dr. Sarah Khan',
            'email' => 'doctor.sarah@medivault.com',
            'password' => Hash::make('doctor123'),
            'role' => 'Doctor',
            'is_active' => true,
            'last_login_at' => Carbon::now()->subDays(1),
        ]);

        $doc2 = Doctor::create([
            'user_id' => $doc2User->id,
            'license_number' => 'MCI-44123',
            'specialization' => 'Pediatrics',
            'hospital' => 'Max Super Speciality Hospital, Gurgaon',
            'bio' => 'Dedicated pediatrician specializing in neonatal intensive care, pediatric immunization, and early childhood developmental screenings.',
            'phone' => '+91 99999 00002',
            'verified_at' => Carbon::now()->subDays(8),
            'verified_by' => $adminUser->id,
        ]);

        // Rajesh Gupta - Pending verification
        $doc3User = User::create([
            'name' => 'Dr. Rajesh Gupta',
            'email' => 'doctor.rajesh@medivault.com',
            'password' => Hash::make('doctor123'),
            'role' => 'Doctor',
            'is_active' => true,
            'last_login_at' => Carbon::now()->subHours(4),
        ]);

        $doc3 = Doctor::create([
            'user_id' => $doc3User->id,
            'license_number' => 'MCI-88122',
            'specialization' => 'General Medicine',
            'hospital' => 'Apollo Clinic, Bangalore',
            'bio' => 'General physician dedicated to primary diagnosis, chronic disease management, and family health counseling.',
            'phone' => '+91 99999 00003',
            'verified_at' => null, // Pending approval
            'verified_by' => null,
        ]);

        // 3. Create Patients
        $pat1User = User::create([
            'name' => 'Aarav Patel',
            'email' => 'patient.aarav@medivault.com',
            'password' => Hash::make('patient123'),
            'role' => 'Patient',
            'is_active' => true,
            'last_login_at' => Carbon::now()->subMinutes(30),
        ]);

        $pat1 = Patient::create([
            'user_id' => $pat1User->id,
            'dob' => Carbon::parse('1990-08-15'),
            'gender' => 'Male',
            'blood_group' => 'O+',
            'phone' => '+91 98765 43210',
            'emergency_contact_name' => 'Priya Patel',
            'emergency_contact_phone' => '+91 98765 43211',
            'address' => '402, Shanti Heights, Juhu, Mumbai, Maharashtra - 400049',
            'profile_photo' => null, // Empty for now
        ]);

        $pat2User = User::create([
            'name' => 'Diya Iyer',
            'email' => 'patient.diya@medivault.com',
            'password' => Hash::make('patient123'),
            'role' => 'Patient',
            'is_active' => true,
            'last_login_at' => Carbon::now()->subHours(5),
        ]);

        $pat2 = Patient::create([
            'user_id' => $pat2User->id,
            'dob' => Carbon::parse('1995-12-22'),
            'gender' => 'Female',
            'blood_group' => 'AB-',
            'phone' => '+91 91234 56789',
            'emergency_contact_name' => 'Hari Iyer',
            'emergency_contact_phone' => '+91 91234 56780',
            'address' => '12B, Maple Blocks, Indiranagar, Bangalore, Karnataka - 560038',
            'profile_photo' => null,
        ]);

        // 4. Create Medical Records for Aarav Patel
        $rec1 = Record::create([
            'patient_id' => $pat1->id,
            'created_by' => $pat1User->id,
            'type' => 'Lab Report',
            'title' => 'Comprehensive CBC & Lipid Panel Test',
            'description' => 'Annual diagnostic health checkup. Cholesterol levels are slightly elevated (Total: 220 mg/dL). Hemoglobin and platelet counts are within ideal health ranges. Recommended dietary adjustments.',
            'file_path' => 'https://res.cloudinary.com/demo/image/upload/v1373023090/sample.pdf',
            'file_type' => 'application/pdf',
            'is_critical' => false,
            'created_at' => Carbon::now()->subWeeks(4),
        ]);

        $rec2 = Record::create([
            'patient_id' => $pat1->id,
            'created_by' => $doc1User->id, // Added by Dr. Amit Sharma
            'type' => 'Prescription',
            'title' => 'Hypertension & Heart Rate Maintenance',
            'description' => 'Patient diagnosed with Stage 1 Hypertension. Prescribed Telmisartan 40mg once daily in the morning, and Amlodipine 5mg if systolic BP climbs above 140. Advised low sodium intake.',
            'file_path' => 'https://res.cloudinary.com/demo/image/upload/v1373023090/sample.jpg',
            'file_type' => 'image/jpeg',
            'is_critical' => true, // Marked as critical
            'created_at' => Carbon::now()->subWeeks(2),
        ]);

        $rec3 = Record::create([
            'patient_id' => $pat1->id,
            'created_by' => $pat1User->id,
            'type' => 'Radiology',
            'title' => 'Chest X-Ray (Post-Flu Evaluation)',
            'description' => 'Follow-up post severe acute bronchitis. Visual inspection shows lungs are completely clear of congestion. Trachea is central. Normal cardiothoracic ratio.',
            'file_path' => 'https://res.cloudinary.com/demo/image/upload/v1373023090/sample.jpg',
            'file_type' => 'image/jpeg',
            'is_critical' => false,
            'created_at' => Carbon::now()->subMonths(3),
        ]);

        // 5. Create Version History for Record 1 (Edits)
        RecordVersion::create([
            'record_id' => $rec1->id,
            'changed_by' => $pat1User->id,
            'snapshot_json' => [
                'title' => 'Initial CBC Lab Test',
                'description' => 'Blood test report showing baseline blood values.',
                'file_path' => 'https://res.cloudinary.com/demo/image/upload/v1373023090/sample.pdf',
                'is_critical' => false,
            ],
            'change_note' => 'Added detailed interpretation of lipid panel and cholesterol values.',
            'created_at' => Carbon::now()->subWeeks(3),
        ]);

        // 6. Create Access Request & Grant
        // Dr. Amit Sharma has valid, active consent
        $grant1 = AccessGrant::create([
            'patient_id' => $pat1->id,
            'doctor_id' => $doc1->id,
            'granted_at' => Carbon::now()->subDays(5),
            'expires_at' => Carbon::now()->addDays(25), // Valid for 30 days total
            'is_active' => true,
            'access_type' => 'read-write',
            'revoked_at' => null,
            'revoked_reason' => null,
            'restricted_record_ids' => [], // Can see all by default
        ]);

        // Dr. Sarah Khan has an EXPIRED consent grant (to test automatic deactivations)
        $expiredGrant = AccessGrant::create([
            'patient_id' => $pat1->id,
            'doctor_id' => $doc2->id,
            'granted_at' => Carbon::now()->subDays(10),
            'expires_at' => Carbon::now()->subDays(2), // Already expired 2 days ago
            'is_active' => true, // Still active flag, to be cleaned up
            'access_type' => 'read-only',
            'revoked_at' => null,
            'revoked_reason' => null,
            'restricted_record_ids' => [],
        ]);

        // Dr. Sarah Khan has a pending access request
        $req1 = AccessRequest::create([
            'doctor_id' => $doc2->id,
            'patient_id' => $pat1->id,
            'reason' => 'Routine pediatric follow-up consultation and vaccine record history synchronization.',
            'status' => 'pending',
            'expires_at' => Carbon::now()->addHours(48),
            'responded_at' => null,
            'created_at' => Carbon::now()->subHours(2),
        ]);

        // Expired pending access request (created 4 days ago, expired 2 days ago)
        $expiredReq = AccessRequest::create([
            'doctor_id' => $doc1->id,
            'patient_id' => $pat2->id,
            'reason' => 'Old record request that was never responded to.',
            'status' => 'pending',
            'expires_at' => Carbon::now()->subDays(2),
            'responded_at' => null,
            'created_at' => Carbon::now()->subDays(4),
        ]);

        // 7. Create Emergency QR Card for Aarav Patel
        EmergencyCard::create([
            'patient_id' => $pat1->id,
            'is_public' => true,
            'qr_token' => 'qr_token_aarav_patel_secure_hash_8712a838bc',
            'blood_group' => $pat1->blood_group,
            'allergies' => 'Penicillin, Sulfa drugs, Peanuts',
            'critical_conditions' => 'Stage 1 Essential Hypertension, Mild Asthma',
            'medications' => 'Telmisartan 40mg OD, Albuterol Inhaler as needed',
            'emergency_contact' => [
                'name' => $pat1->emergency_contact_name,
                'phone' => $pat1->emergency_contact_phone,
            ],
        ]);

        // 8. Create Audit Logs
        AuditLog::create([
            'user_id' => $pat1User->id,
            'action' => 'User logged in',
            'target_type' => 'User',
            'target_id' => $pat1User->id,
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0.0.0 Safari/537.36',
            'session_id' => 'sess_pat_aarav_001',
            'unusual_activity' => false,
            'created_at' => Carbon::now()->subMinutes(30),
        ]);

        AuditLog::create([
            'user_id' => $pat1User->id,
            'action' => 'Created medical record',
            'target_type' => 'Record',
            'target_id' => $rec1->id,
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0.0.0 Safari/537.36',
            'session_id' => 'sess_pat_aarav_001',
            'unusual_activity' => false,
            'created_at' => Carbon::now()->subWeeks(4),
        ]);

        // Dr. Amit Sharma requested access
        AuditLog::create([
            'user_id' => $doc1User->id,
            'action' => 'Submitted clinical access request',
            'target_type' => 'AccessRequest',
            'target_id' => 'req_doc_amit_01',
            'ip_address' => '10.0.5.22',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/124.0.0.0 Safari/537.36',
            'session_id' => 'sess_doc_amit_01',
            'unusual_activity' => false,
            'created_at' => Carbon::now()->subDays(6),
        ]);

        // Aarav Patel approved access
        AuditLog::create([
            'user_id' => $pat1User->id,
            'action' => 'Approved clinical access request',
            'target_type' => 'AccessGrant',
            'target_id' => $grant1->id,
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0.0.0 Safari/537.36',
            'session_id' => 'sess_pat_aarav_001',
            'unusual_activity' => false,
            'created_at' => Carbon::now()->subDays(5),
        ]);

        // Dr. Amit Sharma viewed Aarav's lab record (will display in "Who Has Seen My Records")
        AuditLog::create([
            'user_id' => $doc1User->id,
            'action' => 'Viewed medical record',
            'target_type' => 'Record',
            'target_id' => $rec1->id,
            'ip_address' => '10.0.5.22',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/124.0.0.0 Safari/537.36',
            'session_id' => 'sess_doc_amit_01',
            'unusual_activity' => false,
            'created_at' => Carbon::now()->subDays(4),
        ]);

        // 9. Sync Settings
        UserSetting::create([
            'user_id' => $pat1User->id,
            'key' => 'theme_preference',
            'value' => 'light',
        ]);

        UserSetting::create([
            'user_id' => $pat1User->id,
            'key' => 'email_notifications',
            'value' => [
                'access_request' => true,
                'record_viewed' => true,
                'card_scanned' => true,
            ],
        ]);
    }
}
