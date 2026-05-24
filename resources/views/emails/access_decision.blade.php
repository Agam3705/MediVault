@extends('emails.base')

@section('content')
    <span class="badge">Consent Decision</span>
    <h2>Consent Request Update</h2>
    <p>Dr. {{ $doctorName }},</p>
    <p>Your request to access the health records of <strong>{{ $patientName }}</strong> has been processed.</p>
    
    <div class="card">
        <strong>Status:</strong> 
        <span style="font-weight: bold; color: {{ $approved ? '#2E7D32' : '#C62828' }};">
            {{ $approved ? 'APPROVED' : 'DENIED' }}
        </span>
        @if($approved)
            <br><strong>Access Expiry Date:</strong> {{ $expiryDate }}
        @endif
    </div>

    @if($approved)
        <p>You can now securely view the patient's records under your Patient List dashboard.</p>
        <div style="text-align: center;">
            <a href="{{ route('dashboard') }}" class="button">View Patient List</a>
        </div>
    @else
        <p>If you believe this decision is in error, please contact the patient directly to clarify credentials.</p>
    @endif
@endsection
