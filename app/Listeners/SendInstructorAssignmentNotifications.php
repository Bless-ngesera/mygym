<?php
// app/Listeners/SendInstructorAssignmentNotifications.php

namespace App\Listeners;

use App\Events\InstructorAssigned;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendInstructorAssignmentNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(InstructorAssigned $event): void
    {
        // Notify member about new instructor
        $this->notificationService->instructorAssigned($event->member, $event->instructor);

        // Notify instructor about new member
        $this->notificationService->memberAssignedToInstructor($event->instructor, $event->member);

        // Notify admins about assignment
        $this->notificationService->sendToAdmins([
            'type' => 'instructor_assigned',
            'title' => '👥 Instructor-Member Assignment',
            'message' => "{$event->member->name} has been assigned to instructor {$event->instructor->name}",
            'priority' => 'low',
            'action_url' => route('admin.members.edit', $event->member->id),
            'data' => [
                'member_id' => $event->member->id,
                'instructor_id' => $event->instructor->id
            ]
        ]);
    }
}
