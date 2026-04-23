<?php
// app/Listeners/SendBookingCancellationNotifications.php

namespace App\Listeners;

use App\Events\BookingCancelled;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBookingCancellationNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(BookingCancelled $event): void
    {
        // Notify member of cancellation
        $this->notificationService->sendToUser($event->member, [
            'type' => 'booking_cancelled',
            'title' => '❌ Booking Cancelled',
            'message' => "Your booking for {$event->class->name} on " . $event->class->date_time->format('M d, h:i A') . " has been cancelled.",
            'priority' => 'medium',
            'action_url' => route('member.bookings.index'),
            'data' => [
                'booking_id' => $event->booking->id,
                'class_id' => $event->class->id,
                'cancelled_by' => $event->cancelledBy
            ]
        ]);

        // Notify instructor if cancellation was by member
        if ($event->instructor && $event->cancelledBy !== $event->instructor->name) {
            $this->notificationService->sendToUser($event->instructor, [
                'type' => 'booking_cancelled',
                'title' => '📅 Booking Cancelled',
                'message' => "{$event->member->name} cancelled their booking for your {$event->class->name} class.",
                'priority' => 'medium',
                'action_url' => route('instructor.schedule.show', $event->class->id),
                'data' => [
                    'booking_id' => $event->booking->id,
                    'member_id' => $event->member->id,
                    'class_id' => $event->class->id
                ]
            ]);
        }

        // Check if class now has availability and notify waitlist (if implemented)
        $currentBookings = $event->class->bookings()->count();
        $capacity = $event->class->capacity;

        if ($capacity && $currentBookings < $capacity) {
            $this->notificationService->sendToAdmins([
                'type' => 'class_availability',
                'title' => '📢 Class Spot Available',
                'message' => "{$event->class->name} now has an available spot. Capacity: {$currentBookings}/{$capacity}",
                'priority' => 'low',
                'action_url' => route('admin.classes.show', $event->class->id),
                'data' => ['class_id' => $event->class->id]
            ]);
        }
    }
}
