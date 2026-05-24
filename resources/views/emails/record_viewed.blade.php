@extends('emails.base')

@section('content')
    <span class="badge">Security Alert</span>
    <h2>Medical Record Accessed</h2>
    <p>Hello {{ $patientName }},</p>
    <p>This is a real-time security notification that a medical record in your vault has been viewed.</p>
    
    <div class="card">
        <strong>Document Title:</strong> {{ $recordTitle }}<br>
        <strong>Accessed By:</strong> Dr. {{ $doctorName }} ({{ $specialization }})<br>
        <strong>Timestamp:</strong> {{ $timestamp }}
    </div>

    <p>If you did not authorize this clinical check or if it was outside your active session, please review your active sharing settings instantly.</p>
    
    <div style="text-align: center;">
        <a href="{{ route('dashboard') }}" class="button">Review Access Controls</a>
    </div>
@endsection
