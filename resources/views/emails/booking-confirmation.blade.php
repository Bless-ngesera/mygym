<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
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
        .content {
            padding: 30px;
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
        .receipt-number {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
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
            <h2>Hello {{ $booking->user->name }}!</h2>
            <p>Thank you for booking with <strong>MyGym</strong>. We're excited to have you join us!</p>

            <div class="details">
                <div class="detail-row">
                    <strong>Class:</strong>
                    <span>{{ $booking->scheduledClass->classType->name }}</span>
                </div>
                <div class="detail-row">
                    <strong>Date:</strong>
                    <span>{{ $booking->scheduledClass->date_time->format('l, F j, Y') }}</span>
                </div>
                <div class="detail-row">
                    <strong>Time:</strong>
                    <span>{{ $booking->scheduledClass->date_time->format('g:i A') }}</span>
                </div>
                <div class="detail-row">
                    <strong>Instructor:</strong>
                    <span>{{ $booking->scheduledClass->instructor->name ?? 'TBA' }}</span>
                </div>
                <div class="detail-row">
                    <strong>Duration:</strong>
                    <span>{{ $booking->scheduledClass->classType->minutes }} minutes</span>
                </div>
                <div class="detail-row">
                    <strong>Price:</strong>
                    <span>UGX {{ number_format($booking->scheduledClass->price, 0) }}</span>
                </div>
            </div>

            @if(isset($receipt))
            <div class="receipt-number">
                <p>Receipt Number</p>
                <h3>{{ $receipt->reference_number }}</h3>
                <p>Amount Paid: <strong>UGX {{ number_format($receipt->amount, 0) }}</strong></p>
            </div>
            @endif

            <div style="text-align: center;">
                <a href="{{ route('member.bookings') }}" class="button">View My Bookings</a>
            </div>

            <div style="background: #fff3e0; padding: 15px; border-radius: 8px; margin-top: 20px;">
                <p style="margin: 0;"><strong>💡 Tip:</strong> Arrive 10 minutes early to get settled. Free parking available!</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} MyGym. All rights reserved.</p>
            <p>Ggaba Road, Kampala, Uganda | +256 700 123 456</p>
            <p><small>This is an automated message, please do not reply.</small></p>
        </div>
    </div>
</body>
</html>
