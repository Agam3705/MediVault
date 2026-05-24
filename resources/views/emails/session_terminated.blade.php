@extends('emails.base')

@section('content')
    <span class="badge">Session Terminated</span>
    <h2>Remote Session Logged Out</h2>
    <p>Hello,</p>
    <p>An active session on your MediVault account was terminated remotely.</p>
    
    <div class="card">
        <strong>Terminated Device:</strong> {{ $device }}<br>
        <strong>Terminated IP:</strong> {{ $ipAddress }}<br>
        <strong>Timestamp:</strong> {{ $timestamp }}
    </div>

    <p>If you did not authorize this action, please update your account password immediately to prevent unauthorized access.</p>
    
    <div style="text-align: center;">
        <a href="{{ route('profile.edit') }}" class="button">Change Account Password</a>
    </div>
@endsection
