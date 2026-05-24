@extends('emails.base')

@section('content')
    <span class="badge" style="background-color: #FFEBEE; color: #C62828;">Critical Notice</span>
    <h2>Emergency QR Card Scanned</h2>
    <p>Hello {{ $patientName }},</p>
    <p>Your public emergency card was scanned or accessed. This is typically done by emergency responders or medical professionals during an incident.</p>
    
    <div class="card">
        <strong>IP Address:</strong> {{ $ipAddress }}<br>
        <strong>Accessed At:</strong> {{ $timestamp }}
    </div>

    <p>If you did not authorize this scan or if it was done in error, you can instantly cycle your QR token inside your dashboard settings to invalidate the old card.</p>
    
    <div style="text-align: center;">
        <a href="{{ route('dashboard') }}" class="button">Invalidate/Change QR Token</a>
    </div>
@endsection
