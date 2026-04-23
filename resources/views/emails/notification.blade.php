{{-- resources/views/emails/notification.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $notification->title }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
        .button { display: inline-block; background: #667eea; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #999; }
        .priority-critical { border-left: 4px solid #dc3545; }
        .priority-high { border-left: 4px solid #fd7e14; }
        .priority-medium { border-left: 4px solid #007bff; }
        .priority-low { border-left: 4px solid #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>MyGym Notification</h2>
        </div>
        <div class="content priority-{{ $notification->priority }}">
            <h3>{{ $notification->title }}</h3>
            <p>{{ $notification->message }}</p>
            @if($notification->action_url)
                <a href="{{ url($notification->action_url) }}" class="button">View Details →</a>
            @endif
            <hr>
            <small>Received: {{ $notification->created_at->format('F j, Y \a\t g:i A') }}</small>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} MyGym. All rights reserved.</p>
            <p>
                <a href="{{ route('notifications.settings') }}">Notification Settings</a> |
                <a href="{{ url('/') }}">Visit Website</a>
            </p>
        </div>
    </div>
</body>
</html>
