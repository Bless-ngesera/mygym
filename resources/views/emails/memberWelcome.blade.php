<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to MyGym!</title>
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
            padding: 40px 30px;
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
            <h1>Welcome to MyGym! 🎉</h1>
            <p>Your fitness journey starts here</p>
        </div>

        <div class="content">
            <h2>Hello {{ $user->name }}!</h2>
            <p>Thank you for joining <strong>MyGym</strong>! We're thrilled to have you as part of our fitness community.</p>

            <div class="features">
                <div class="feature">
                    <h3>📅 Book Classes</h3>
                    <p>Browse and book fitness classes</p>
                </div>
                <div class="feature">
                    <h3>💪 Track Progress</h3>
                    <p>Monitor your fitness journey</p>
                </div>
                <div class="feature">
                    <h3>🧾 Digital Receipts</h3>
                    <p>Get receipts for all bookings</p>
                </div>
                <div class="feature">
                    <h3>👥 Community</h3>
                    <p>Join our fitness community</p>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('member.classes') }}" class="button">Explore Classes</a>
            </div>

            <div style="background: #f0fdf4; padding: 15px; border-radius: 8px; margin-top: 20px;">
                <p><strong>🎁 Special Offer:</strong> Book your first 3 classes and get 20% off!</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} MyGym. All rights reserved.</p>
            <p>Ggaba Road, Kampala, Uganda | +256 700 123 456</p>
        </div>
    </div>
</body>
</html>
