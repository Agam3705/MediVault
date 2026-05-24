<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Medical Card — {{ $patient->user->name }}</title>
    <meta name="description" content="Emergency medical information card for hospital responders.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #FDFBF7 0%, #F5F2EB 50%, #EFEBE9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(62, 39, 35, 0.1), 0 4px 20px rgba(62, 39, 35, 0.05);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #D32F2F 0%, #B71C1C 100%);
            color: white;
            padding: 24px 28px;
            text-align: center;
            position: relative;
        }
        .card-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 4px;
            background: repeating-linear-gradient(90deg, transparent, transparent 10px, rgba(255,255,255,0.3) 10px, rgba(255,255,255,0.3) 20px);
        }
        .card-header .badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 8px;
            border: 1px solid rgba(255,255,255,0.25);
        }
        .card-header h1 {
            font-size: 22px;
            font-weight: 800;
            margin: 0;
        }
        .card-header p {
            font-size: 12px;
            opacity: 0.85;
            margin-top: 4px;
        }
        .card-body {
            padding: 28px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #EFEBE9;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label {
            font-size: 10px;
            font-weight: 700;
            color: #8D6E63;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            flex-shrink: 0;
        }
        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #3E2723;
            text-align: right;
            max-width: 60%;
        }
        .info-value.blood {
            font-size: 22px;
            font-weight: 800;
            color: #D32F2F;
            background: #FFEBEE;
            padding: 4px 14px;
            border-radius: 12px;
            border: 2px solid #FFCDD2;
        }
        .emergency-contact {
            background: #FFF3E0;
            border: 1px solid #FFE0B2;
            border-radius: 16px;
            padding: 16px;
            margin-top: 16px;
        }
        .emergency-contact h3 {
            font-size: 11px;
            font-weight: 700;
            color: #E65100;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
        }
        .emergency-contact .name { font-size: 15px; font-weight: 700; color: #3E2723; }
        .emergency-contact .phone { font-size: 18px; font-weight: 800; color: #E65100; margin-top: 4px; }
        .emergency-contact .phone a { color: inherit; text-decoration: none; }
        .card-footer {
            background: #FDFBF7;
            border-top: 1px solid #EFEBE9;
            padding: 16px 28px;
            text-align: center;
        }
        .card-footer p {
            font-size: 10px;
            color: #8D6E63;
            font-weight: 500;
        }
        .card-footer .brand {
            font-weight: 800;
            color: #5D4037;
        }
        @media print {
            body { background: white; padding: 0; }
            .card { box-shadow: none; border: 2px solid #D32F2F; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <div class="badge">🏥 Emergency Medical Info</div>
            <h1>{{ $patient->user->name }}</h1>
            <p>Age: {{ $patient->dob ? $patient->dob->age . ' years' : 'Not provided' }} &bull; Gender: {{ $patient->gender ?? 'Not provided' }}</p>
        </div>

        <div class="card-body">
            <div class="info-row">
                <span class="info-label">Blood Type</span>
                <span class="info-value blood">{{ $card->blood_group ?? $patient->blood_group ?? 'Unknown' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Known Allergies</span>
                <span class="info-value">{{ $card->allergies ?: 'None declared' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Current Medications</span>
                <span class="info-value">{{ $card->medications ?: 'None declared' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Medical Conditions</span>
                <span class="info-value">{{ $card->conditions ?: 'None declared' }}</span>
            </div>

            @if($card->notes)
            <div class="info-row">
                <span class="info-label">Special Notes</span>
                <span class="info-value">{{ $card->notes }}</span>
            </div>
            @endif

            @if($patient->emergency_contact_name || $patient->emergency_contact_phone)
            <div class="emergency-contact">
                <h3>📞 Emergency Contact</h3>
                <div class="name">{{ $patient->emergency_contact_name ?? 'Not provided' }}</div>
                @if($patient->emergency_contact_phone)
                <div class="phone"><a href="tel:{{ $patient->emergency_contact_phone }}">{{ $patient->emergency_contact_phone }}</a></div>
                @endif
            </div>
            @endif
        </div>

        <div class="card-footer">
            <p>Powered by <span class="brand">MediVault</span> &bull; Secure Patient Medical Records</p>
            <p style="margin-top: 4px;">This card was accessed at {{ now()->format('M d, Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>
