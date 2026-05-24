@extends('emails.base')

@section('content')
    <span class="badge" style="background-color: #E8F5E9; color: #2E7D32;">Verified</span>
    <h2>Practitioner Verification Approved</h2>
    <p>Dr. {{ $doctorName }},</p>
    <p>We are pleased to inform you that your clinical registration credentials and license registration have been approved by the platform administrators.</p>
    
    <div class="card">
        <strong>Registered License Number:</strong> {{ $licenseNumber }}<br>
        <strong>Hospital Affiliation:</strong> {{ $hospital }}<br>
        <strong>Status:</strong> Active & Verified
    </div>

    <p>You can now search for patients, request record access consent, and write medical notes.</p>
    
    <div style="text-align: center;">
        <a href="{{ route('dashboard') }}" class="button">Access Practitioner Portal</a>
    </div>
@endsection
