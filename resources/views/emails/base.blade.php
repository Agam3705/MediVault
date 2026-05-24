<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>MediVault Notification</title>
    <style>
        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #FDFBF7;
            margin: 0;
            padding: 0;
            color: #3E2723;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #D7CCC8;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(62,39,35,0.05);
        }
        .header {
            background: linear-gradient(135deg, #5D4037, #3E2723);
            padding: 25px 20px;
            text-align: center;
            color: #FFF8E1;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .content {
            padding: 30px 25px;
            line-height: 1.6;
        }
        .content h2 {
            margin-top: 0;
            color: #3E2723;
            font-size: 18px;
            font-weight: 700;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            background-color: #EFEBE9;
            color: #5D4037;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .card {
            background-color: #FDFBF7;
            border: 1px solid #EFEBE9;
            border-radius: 12px;
            padding: 15px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3E2723;
            color: #FFF8E1 !important;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 14px;
            margin: 15px 0;
            text-align: center;
        }
        .button-secondary {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ffffff;
            color: #5D4037 !important;
            border: 1px solid #D7CCC8;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 13px;
            margin: 15px 5px;
            text-align: center;
        }
        .footer {
            background-color: #F5F2EB;
            padding: 15px 20px;
            text-align: center;
            font-size: 11px;
            color: #8D6E63;
            border-top: 1px solid #EFEBE9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MediVault Secure</h1>
        </div>
        <div class="content">
            @yield('content')
        </div>
        <div class="footer">
            This is an automated security notification from your MediVault Clinical Consent dashboard.<br>
            &copy; {{ date('Y') }} MediVault Inc. All rights reserved.
        </div>
    </div>
</body>
</html>
