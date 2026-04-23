<?php
// app/Listeners/SendPushNotification.php

namespace App\Listeners;

use App\Events\NotificationSent;
use App\Models\NotificationSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPushNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(NotificationSent $event): void
    {
        $notification = $event->notification;
        $user = $notification->user;

        // Check if user has push notifications enabled
        $settings = NotificationSettings::where('user_id', $user->id)->first();

        if (!$settings || !$settings->push_enabled) {
            return;
        }

        // Only send push for critical, high, and medium priority
        if (!in_array($notification->priority, ['critical', 'high', 'medium'])) {
            return;
        }

        // TODO: Implement your push notification service here
        // Examples: OneSignal, Firebase Cloud Messaging, Pusher, etc.

        // Example with OneSignal (if installed):
        /*
        OneSignal::sendNotificationToUser(
            $notification->title,
            $user->device_token,
            $notification->action_url,
            [
                'notification_id' => $notification->id,
                'type' => $notification->type,
                'priority' => $notification->priority
            ]
        );
        */

        // Example with Pusher (Laravel WebSockets):
        /*
        broadcast(new \App\Events\NewNotification($user->id, $notification));
        */

        Log::info("Push notification would be sent to user {$user->id}: {$notification->title}");

        // Mark as delivered
        $notification->markAsDelivered();
    }
}
