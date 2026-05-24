@extends('emails.base')

@section('content')
    <span class="badge" style="background-color: #FFEBEE; color: #C62828;">Security Warning</span>
    <h2>Multiple Failed Login Attempts</h2>
    <p>Hello,</p>
    <p>We detected 3 or more consecutive failed login attempts on your MediVault account.</p>
    
    <div class="card">
        <strong>Attempted IP:</strong> {{ $ipAddress }}<br>
        <strong>Timestamps:</strong> {{ $timestamp }}
    </div>

    <p>If this was not you, your account might be target of a brute-force attack. You can temporarily lock login access to your account to protect your records:</p>
    
    <div style="text-align: center;">
        <a href="{{ route('profile.edit') }}" class="button" style="background-color: #C62828;">Review Account Security</a>
    </div>
@endsection
