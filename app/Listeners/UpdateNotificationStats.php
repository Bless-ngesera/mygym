<?php
// app/Listeners/UpdateNotificationStats.php

namespace App\Listeners;

use App\Events\NotificationRead;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateNotificationStats implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(NotificationRead $event): void
    {
        $user = $event->user;
        $notification = $event->notification;

        // Log the read activity for analytics
        Log::info("Notification read by user {$user->id}", [
            'notification_id' => $notification->id,
            'notification_type' => $notification->type,
            'read_at' => $event->readAt,
            'time_to_read' => $notification->created_at->diffInSeconds($event->readAt)
        ]);

        // Update user's last activity timestamp (optional)
        $user->update([
            'last_notification_read_at' => now()
        ]);

        // If this was a critical notification, track for reporting
        if ($notification->priority === 'critical') {
            // Store in a separate analytics table if needed
            \DB::table('notification_analytics')->insert([
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'type' => $notification->type,
                'priority' => $notification->priority,
                'read_at' => $event->readAt,
                'time_to_read_seconds' => $notification->created_at->diffInSeconds($event->readAt),
                'created_at' => now(),
            ]);
        }

        // Broadcast to admin channel for critical notification tracking
        if ($notification->priority === 'critical') {
            broadcast(new \App\Events\CriticalNotificationRead($user, $notification))->toOthers();
        }
    }
}
