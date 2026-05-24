@extends('emails.base')

@section('content')
    <span class="badge">Consent Request</span>
    <h2>New Clinical Access Request</h2>
    <p>Hello {{ $patientName }},</p>
    <p>A medical practitioner has requested authorization to access your health vault records.</p>
    
    <div class="card">
        <strong>Practitioner:</strong> Dr. {{ $doctorName }}<br>
        <strong>Specialization:</strong> {{ $specialization }}<br>
        <strong>Affiliated Hospital:</strong> {{ $hospital }}<br>
        <strong>Requested Reason:</strong> "{{ $reason }}"
    </div>

    <p>Please review and respond to this request from your dashboard consent panel:</p>
    
    <div style="text-align: center;">
        <a href="{{ route('dashboard') }}" class="button">Go to Dashboard Consent Panel</a>
    </div>
@endsection
