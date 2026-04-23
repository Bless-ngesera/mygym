<?php
// app/Listeners/SendSubscriptionNotifications.php

namespace App\Listeners;

use App\Events\SubscriptionExpiring;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSubscriptionNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(SubscriptionExpiring $event): void
    {
        // Send notification to user
        $this->notificationService->subscriptionExpiring($event->user, $event->daysLeft);

        // For critical expiry (3 days or less), send additional reminders
        if ($event->daysLeft <= 3) {
            // Send SMS reminder if user has phone number
            if ($event->user->phone) {
                // TODO: Implement SMS notification
                Log::info("SMS reminder would be sent to {$event->user->phone} about subscription expiry");
            }

            // Notify admins about expiring subscription
            $this->notificationService->sendToAdmins([
                'type' => 'subscription_expiring_critical',
                'title' => '⚠️ Subscription Expiring Soon',
                'message' => "{$event->user->name}'s subscription expires in {$event->daysLeft} days",
                'priority' => 'high',
                'action_url' => route('admin.members.edit', $event->user->id),
                'data' => [
                    'user_id' => $event->user->id,
                    'days_left' => $event->daysLeft,
                    'plan_name' => $event->user->subscription->plan_name ?? 'Unknown'
                ]
            ]);
        }

        // If subscription already expired, send expiry notification
        if ($event->daysLeft <= 0) {
            $this->notificationService->sendToUser($event->user, [
                'type' => 'subscription_expired',
                'title' => '❌ Membership Expired',
                'message' => "Your membership has expired. Renew now to continue enjoying our services!",
                'priority' => 'critical',
                'action_url' => route('plans.index'),
                'data' => ['user_id' => $event->user->id]
            ]);
        }
    }
}
