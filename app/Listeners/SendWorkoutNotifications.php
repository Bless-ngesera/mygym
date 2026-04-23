<?php
// app/Listeners/SendWorkoutNotifications.php

namespace App\Listeners;

use App\Events\WorkoutCompleted;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWorkoutNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(WorkoutCompleted $event): void
    {
        // Send workout completion notification
        $this->notificationService->workoutCompleted($event->user, $event->workout);

        // Check and update goal progress
        $this->checkGoalProgress($event->user);

        // If user has an instructor, notify them of milestone
        if ($event->user->instructor_id) {
            $workoutCount = $event->user->workouts()->where('completed', true)->count();

            if ($workoutCount % 10 === 0) { // Every 10 workouts
                $this->notificationService->sendToUser($event->user->instructor, [
                    'type' => 'member_workout_milestone',
                    'title' => '🎯 Member Workout Milestone!',
                    'message' => "Congratulations! {$event->user->name} has completed {$workoutCount} workouts!",
                    'priority' => 'medium',
                    'action_url' => route('instructor.members.progress', $event->user->id),
                    'data' => [
                        'member_id' => $event->user->id,
                        'workout_count' => $workoutCount
                    ]
                ]);
            }
        }
    }

    private function checkGoalProgress($user): void
    {
        $goals = $user->goals()->where('completed', false)->get();

        foreach ($goals as $goal) {
            $percentage = $goal->progressPercentage();
            $milestones = [25, 50, 75];

            if (in_array($percentage, $milestones) && !$goal->last_notified_at?->isToday()) {
                $this->notificationService->sendToUser($user, [
                    'type' => 'goal_progress',
                    'title' => '🎯 Goal Progress Update',
                    'message' => "You're {$percentage}% of the way to your goal: {$goal->title}",
                    'priority' => 'medium',
                    'action_url' => route('member.dashboard'),
                    'data' => ['goal_id' => $goal->id, 'percentage' => $percentage]
                ]);

                $goal->update(['last_notified_at' => now()]);
            }
        }
    }
}
