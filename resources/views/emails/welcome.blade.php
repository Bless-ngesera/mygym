{{-- resources/views/emails/welcome.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Welcome to MyGym</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; text-align: center; }
        .button { background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to MyGym, {{ $user->name }}! 🎉</h1>
        </div>
        <div style="padding: 30px;">
            <p>Thank you for joining our fitness community! We're excited to help you achieve your health and fitness goals.</p>

            <h3>Quick Start Guide:</h3>
            <ul>
                <li>📅 <strong>Book Your First Class</strong> - Browse our schedule and join a class that fits your schedule</li>
                <li>💪 <strong>Set Your Goals</strong> - Define what you want to achieve</li>
                <li>🏋️ <strong>Track Workouts</strong> - Log your progress and celebrate milestones</li>
                <li>🥗 <strong>Monitor Nutrition</strong> - Keep track of your daily intake</li>
            </ul>

            <div style="text-align: center;">
                <a href="{{ $dashboardUrl }}" class="button">Go to Dashboard</a>
                <a href="{{ $classesUrl }}" class="button" style="background: #28a745;">Browse Classes</a>
            </div>

            <p style="margin-top: 30px;">Need help? Our support team is here for you 24/7.</p>
        </div>
    </div>
</body>
</html>
