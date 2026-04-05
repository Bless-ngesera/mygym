<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - MyGym</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .welcome-text {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
        }
        .detail-value {
            color: #333;
            font-weight: 500;
        }
        .receipt-number {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .receipt-number p {
            margin: 0;
            font-size: 12px;
            color: #2e7d32;
        }
        .receipt-number h3 {
            margin: 5px 0 0;
            color: #1b5e20;
            font-family: monospace;
            font-size: 18px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
        }
        .tip-box {
            background: #fff3e0;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #ff9800;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .footer p {
            margin: 5px 0;
        }
        .social-links {
            margin: 10px 0;
        }
        .social-links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 10px;
        }
        @media (max-width: 480px) {
            .container {
                margin: 10px;
            }
            .header {
                padding: 20px;
            }
            .content {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Booking Confirmed!</h1>
            <p>Your class has been successfully booked</p>
        </div>

        <div class="content">
            <div class="welcome-text">
                <h2>Hello {{ $user->name }}! 👋</h2>
                <p>Thank you for booking with <strong>MyGym</strong>. We're excited to have you join us!</p>
            </div>

            <div class="details">
                <div class="detail-row">
                    <span class="detail-label">📋 Class:</span>
                    <span class="detail-value">{{ $class->classType->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">📅 Date:</span>
                    <span class="detail-value">{{ $class->date_time->format('l, F j, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">⏰ Time:</span>
                    <span class="detail-value">{{ $class->date_time->format('g:i A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">👨‍🏫 Instructor:</span>
                    <span class="detail-value">{{ $class->instructor->name ?? 'TBA' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">⏱️ Duration:</span>
                    <span class="detail-value">{{ $class->classType->minutes }} minutes</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">💰 Price:</span>
                    <span class="detail-value">UGX {{ number_format($class->price, 0) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">📍 Location:</span>
                    <span class="detail-value">MyGym, Ggaba Road, Kampala</span>
                </div>
            </div>

            @if(isset($receipt))
            <div class="receipt-number">
                <p>🧾 Receipt Number</p>
                <h3>{{ $receipt->reference_number }}</h3>
                <p>Amount Paid: <strong>UGX {{ number_format($receipt->amount, 0) }}</strong></p>
                <p style="margin-top: 5px;">Payment Method: <strong>{{ $receipt->payment_method }}</strong></p>
            </div>
            @endif

            <div style="text-align: center;">
                <a href="{{ route('member.bookings') }}" class="button">📋 View My Bookings</a>
            </div>

            <div class="tip-box">
                <p style="margin: 0;"><strong>💡 Pro Tip:</strong></p>
                <ul style="margin: 8px 0 0 20px;">
                    <li>Arrive 10 minutes early to get settled</li>
                    <li>Bring your water bottle and towel</li>
                    <li>Free parking available on-site</li>
                    <li>Lockers available for your belongings</li>
                </ul>
            </div>

            <div style="margin-top: 20px; text-align: center;">
                <p style="font-size: 12px; color: #888;">
                    Need to cancel? You can do so up to 2 hours before class start time.
                </p>
            </div>
        </div>

        <div class="footer">
            <div class="social-links">
                <a href="#">Facebook</a> |
                <a href="#">Instagram</a> |
                <a href="#">Twitter</a>
            </div>
            <p>&copy; {{ date('Y') }} MyGym. All rights reserved.</p>
            <p>Ggaba Road, Kampala, Uganda | +256 700 123 456</p>
            <p><small>This is an automated message, please do not reply.</small></p>
        </div>
    </div>
</body>
</html>
