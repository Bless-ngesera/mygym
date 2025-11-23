<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to MyGym</title>
    <style>
        body {
            background-color: #eef2f7;
            font-family: "Segoe UI", Roboto, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .card {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 40px;
            text-align: center;
        }
        .card h1 {
            font-size: 24px;
            color: #4f46e5;
            margin-bottom: 10px;
        }
        .card h2 {
            font-size: 20px;
            margin: 0;
        }
        .card p {
            font-size: 16px;
            line-height: 1.6;
            margin: 20px 0;
        }
        .card ul {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        .card ul li {
            margin: 8px 0;
            font-size: 15px;
        }
        .button {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 24px;
            background-color: #4f46e5;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
        }
        .footer {
            margin-top: 40px;
            font-size: 13px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>MyGym</h1>
        <h2>Welcome to MyGym, Instructor {{ $instructor->name }} 🎉</h2>

        <p>We’re excited to have you join our team of instructors at <strong>MyGym</strong>.</p>
        <p>You have been successfully registered as an instructor. You can now log in and start managing your classes, connecting with members, and sharing your expertise.</p>

        <p>You can now:</p>
        <ul>
            <li>📅 Manage your class schedule</li>
            <li>👥 Connect with members</li>
            <li>📈 Track your instructor activity</li>
            <li>💬 Share your expertise</li>
        </ul>

        <a href="{{ route('login') }}" class="button">Log In to Your Account</a>



        <p class="footer">
            Thanks,<br>
            The MyGym Team<br><br>
            &copy; {{ date('Y') }} MyGym. All rights reserved.
        </p>
    </div>
</body>
</html>
