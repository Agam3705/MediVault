@extends('emails.base')

@section('content')
    <span class="badge">Record Added</span>
    <h2>New Medical Record Uploaded</h2>
    <p>Hello {{ $patientName }},</p>
    <p>A new document has been uploaded to your MediVault clinical profile.</p>
    
    <div class="card">
        <strong>Document Title:</strong> {{ $recordTitle }}<br>
        <strong>Uploaded By:</strong> {{ $uploaderName }}<br>
        <strong>Timestamp:</strong> {{ $timestamp }}
    </div>

    <p>You can securely log in to review the details and configure file access visibility permissions.</p>
    
    <div style="text-align: center;">
        <a href="{{ route('dashboard') }}" class="button">View Medical Vault</a>
    </div>
@endsection
