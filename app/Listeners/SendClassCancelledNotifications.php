<?php
// app/Listeners/SendClassCancelledNotifications.php

namespace App\Listeners;

use App\Events\ClassCancelled;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendClassCancelledNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(ClassCancelled $event): void
    {
        // Notify all members who booked this class
        foreach ($event->bookings as $booking) {
            $this->notificationService->sendToUser($booking->user, [
                'type' => 'class_cancelled',
                'title' => '❌ Class Cancelled',
                'message' => "The {$event->class->name} class on " . $event->class->date_time->format('M d, h:i A') . " has been cancelled. Your booking has been refunded.",
                'priority' => 'critical',
                'action_url' => route('member.bookings.index'),
                'data' => [
                    'class_id' => $event->class->id,
                    'class_name' => $event->class->name,
                    'class_time' => $event->class->date_time->toIso8601String()
                ]
            ]);
        }

        // Notify the instructor
        if ($event->instructor) {
            $this->notificationService->sendToUser($event->instructor, [
                'type' => 'class_cancelled',
                'title' => '❌ Your Class Has Been Cancelled',
                'message' => "Your {$event->class->name} class scheduled for " . $event->class->date_time->format('M d, h:i A') . " has been cancelled.",
                'priority' => 'high',
                'action_url' => route('instructor.schedule.index'),
                'data' => ['class_id' => $event->class->id]
            ]);
        }

        // Notify admins
        $this->notificationService->sendToAdmins([
            'type' => 'class_cancelled',
            'title' => '⚠️ Class Cancelled',
            'message' => "Class {$event->class->name} by {$event->instructor?->name} has been cancelled. Affected members: {$event->bookings->count()}",
            'priority' => 'high',
            'action_url' => route('admin.reports.classes'),
            'data' => [
                'class_id' => $event->class->id,
                'affected_members' => $event->bookings->count()
            ]
        ]);
    }
}
