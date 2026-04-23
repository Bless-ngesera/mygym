<?php
// app/Listeners/SendCheckInNotifications.php

namespace App\Listeners;

use App\Events\MemberCheckedIn;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCheckInNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(MemberCheckedIn $event): void
    {
        // Send check-in confirmation to member
        $this->notificationService->checkInConfirmation($event->member, $event->streakCount);

        // Check for streak milestones
        $milestones = [3, 7, 14, 30, 60, 100];
        if (in_array($event->streakCount, $milestones)) {
            $this->notificationService->streakMilestone($event->member, $event->streakCount);
        }

        // For major milestones, notify admins
        if ($event->streakCount === 100) {
            $this->notificationService->sendToAdmins([
                'type' => 'member_streak_milestone',
                'title' => '🎉 Century Streak Achieved!',
                'message' => "{$event->member->name} has achieved a 100-day check-in streak!",
                'priority' => 'high',
                'action_url' => route('admin.members.edit', $event->member->id),
                'data' => ['member_id' => $event->member->id, 'streak' => $event->streakCount]
            ]);
        }
    }
}
