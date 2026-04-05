<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Class Reminder</title>
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
        .content {
            padding: 30px;
        }
        .checklist {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 8px;
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
            <h1>⏰ Class Reminder</h1>
            <p>Your class starts soon!</p>
        </div>

        <div class="content">
            <h2>Hello {{ $booking->user->name }}!</h2>
            <p>This is a friendly reminder that your class starts in <strong>{{ $booking->scheduledClass->date_time->diffForHumans() }}</strong>.</p>

            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <p><strong>Class:</strong> {{ $booking->scheduledClass->classType->name }}</p>
                <p><strong>Date:</strong> {{ $booking->scheduledClass->date_time->format('l, F j, Y') }}</p>
                <p><strong>Time:</strong> {{ $booking->scheduledClass->date_time->format('g:i A') }}</p>
                <p><strong>Instructor:</strong> {{ $booking->scheduledClass->instructor->name ?? 'TBA' }}</p>
            </div>

            <div class="checklist">
                <p><strong>✅ Don't forget to bring:</strong></p>
                <ul>
                    <li>Comfortable workout clothes</li>
                    <li>Water bottle</li>
                    <li>Towel</li>
                </ul>
            </div>

            <p>See you at the gym! 💪</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} MyGym. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
