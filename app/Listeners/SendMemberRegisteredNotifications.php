<?php
// app/Listeners/SendMemberRegisteredNotifications.php

namespace App\Listeners;

use App\Events\MemberRegistered;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMemberRegisteredNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(MemberRegistered $event): void
    {
        // Send welcome notification to new member
        $this->notificationService->sendToUser($event->member, [
            'type' => 'welcome',
            'title' => '🎉 Welcome to MyGym!',
            'message' => "Thank you for joining {$event->member->name}! Get started by exploring our classes and setting your fitness goals.",
            'priority' => 'high',
            'action_url' => route('member.dashboard'),
            'data' => ['member_id' => $event->member->id]
        ]);

        // Notify all admins about new member
        $this->notificationService->newMemberRegistered($event->member);

        // If there's an available instructor, suggest assignment
        $availableInstructor = \App\Models\User::where('role', 'instructor')
            ->where('status', 'active')
            ->inRandomOrder()
            ->first();

        if ($availableInstructor) {
            $this->notificationService->sendToAdmins([
                'type' => 'suggest_instructor',
                'title' => '💡 Instructor Suggestion',
                'message' => "New member {$event->member->name} can be assigned to instructor {$availableInstructor->name}",
                'priority' => 'low',
                'action_url' => route('admin.members.edit', $event->member->id),
                'data' => [
                    'member_id' => $event->member->id,
                    'suggested_instructor_id' => $availableInstructor->id
                ]
            ]);
        }
    }
}
