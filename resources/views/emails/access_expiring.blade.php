@extends('emails.base')

@section('content')
    <span class="badge" style="background-color: #FFF3E0; color: #E65100;">Expiry Warning</span>
    <h2>Patient Access Grant Expiring Soon</h2>
    <p>Dr. {{ $doctorName }},</p>
    <p>This is a notification that your authorized access to the health records of <strong>{{ $patientName }}</strong> will expire in <strong>3 days</strong>.</p>
    
    <div class="card">
        <strong>Grant Expiry Time:</strong> {{ $expiryDate }}
    </div>

    <p>If you require ongoing clinical access to monitor or record notes for this patient, please initiate a new access request.</p>
    
    <div style="text-align: center;">
        <a href="{{ route('dashboard') }}" class="button">Request Access Renewal</a>
    </div>
@endsection
