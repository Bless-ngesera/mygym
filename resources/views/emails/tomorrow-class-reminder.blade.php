<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Reminder - Tomorrow</title>
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
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
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
        .class-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .checklist {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⏰ Class Tomorrow!</h1>
            <p>Your class is scheduled for tomorrow</p>
        </div>

        <div class="content">
            <h2>Hello {{ $booking->user->name }}!</h2>
            <p>This is a friendly reminder that you have a class tomorrow:</p>

            <div class="class-details">
                <p><strong>📋 Class:</strong> {{ $booking->scheduledClass->classType->name }}</p>
                <p><strong>📅 Date:</strong> {{ $booking->scheduledClass->date_time->format('l, F j, Y') }}</p>
                <p><strong>⏰ Time:</strong> {{ $booking->scheduledClass->date_time->format('g:i A') }}</p>
                <p><strong>👨‍🏫 Instructor:</strong> {{ $booking->scheduledClass->instructor->name ?? 'TBA' }}</p>
                <p><strong>📍 Location:</strong> MyGym, Ggaba Road, Kampala</p>
            </div>

            <div class="checklist">
                <p><strong>✅ Preparation Checklist:</strong></p>
                <ul>
                    <li>Get a good night's sleep</li>
                    <li>Stay hydrated</li>
                    <li>Pack your gym bag tonight</li>
                    <li>Arrive 10 minutes early</li>
                </ul>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('member.bookings') }}" class="button">View My Bookings</a>
            </div>

            <div style="background: #fff3e0; padding: 15px; border-radius: 8px; margin-top: 20px;">
                <p style="margin: 0;"><strong>💡 Tip:</strong> Can't make it? Please cancel at least 2 hours before the class to avoid charges.</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} MyGym. All rights reserved.</p>
            <p>Ggaba Road, Kampala, Uganda | +256 700 123 456</p>
            <p><small>This is an automated reminder, please do not reply.</small></p>
        </div>
    </div>
</body>
</html>
