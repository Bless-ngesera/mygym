<?php
// app/Listeners/SendEmailNotification.php

namespace App\Listeners;

use App\Events\NotificationSent;
use App\Models\NotificationSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmailNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(NotificationSent $event): void
    {
        $notification = $event->notification;
        $user = $notification->user;

        // Check if user has email notifications enabled
        $settings = NotificationSettings::where('user_id', $user->id)->first();

        if (!$settings || !$settings->email_enabled) {
            return;
        }

        // Only send email for critical and high priority notifications
        if (!in_array($notification->priority, ['critical', 'high'])) {
            return;
        }

        try {
            Mail::send('emails.notification', [
                'user' => $user,
                'notification' => $notification,
                'settings' => $settings
            ], function ($message) use ($user, $notification) {
                $message->to($user->email)
                        ->subject($notification->title)
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info("Email notification sent to user {$user->id}: {$notification->title}");
        } catch (\Exception $e) {
            Log::error("Failed to send email notification: " . $e->getMessage());
        }
    }
}
