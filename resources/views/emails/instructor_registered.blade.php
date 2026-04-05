<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Instructor!</title>
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
        .features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 30px 0;
        }
        .feature {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏋️ Welcome to MyGym!</h1>
            <p>You're now part of our instructor team</p>
        </div>

        <div class="content">
            <h2>Hello {{ $instructor->name }}!</h2>
            <p>Congratulations on becoming a MyGym instructor! We're thrilled to have you on board.</p>

            <div class="features">
                <div class="feature">
                    <h3>📅 Schedule Classes</h3>
                    <p>Create and manage your own class schedule</p>
                </div>
                <div class="feature">
                    <h3>👥 Track Students</h3>
                    <p>See who's attending your classes</p>
                </div>
                <div class="feature">
                    <h3>💰 View Earnings</h3>
                    <p>Track your earnings and payments</p>
                </div>
                <div class="feature">
                    <h3>📊 Analytics</h3>
                    <p>Monitor your class performance</p>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('instructor.dashboard') }}" class="button">Go to Dashboard</a>
            </div>

            <div style="background: #f0fdf4; padding: 15px; border-radius: 8px; margin-top: 20px;">
                <p><strong>📝 Next Steps:</strong></p>
                <ul>
                    <li>Set up your profile</li>
                    <li>Schedule your first class</li>
                    <li>Review the instructor guidelines</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} MyGym. All rights reserved.</p>
            <p>Need help? Contact us at instructors@mygym.com</p>
        </div>
    </div>
</body>
</html>
