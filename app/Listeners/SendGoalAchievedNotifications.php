<?php
// app/Listeners/SendGoalAchievedNotifications.php

namespace App\Listeners;

use App\Events\GoalAchieved;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendGoalAchievedNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(GoalAchieved $event): void
    {
        // Send notification to member
        $this->notificationService->goalAchieved($event->user, $event->goal);

        // Notify admins of significant achievement
        $this->notificationService->sendToAdmins([
            'type' => 'member_goal_achieved',
            'title' => '🏆 Member Goal Achieved!',
            'message' => "{$event->user->name} achieved their goal: {$event->goal->title}",
            'priority' => 'medium',
            'action_url' => route('admin.members.edit', $event->user->id),
            'data' => [
                'member_id' => $event->user->id,
                'goal_id' => $event->goal->id,
                'goal_title' => $event->goal->title
            ]
        ]);

        // If member has an instructor, notify them too
        if ($event->user->instructor_id) {
            $this->notificationService->sendToUser($event->user->instructor, [
                'type' => 'member_goal_achieved',
                'title' => '🎯 Your Member Achieved a Goal!',
                'message' => "Congratulations! {$event->user->name} achieved their goal: {$event->goal->title}",
                'priority' => 'high',
                'action_url' => route('instructor.members.progress', $event->user->id),
                'data' => [
                    'member_id' => $event->user->id,
                    'goal_id' => $event->goal->id,
                    'goal_title' => $event->goal->title
                ]
            ]);
        }
    }
}
