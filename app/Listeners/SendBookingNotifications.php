<?php
// app/Listeners/SendBookingNotifications.php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBookingNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(BookingCreated $event): void
    {
        // Send confirmation to member
        $this->notificationService->bookingConfirmed($event->member, $event->booking);

        // Notify instructor
        if ($event->instructor) {
            $this->notificationService->newBookingForInstructor(
                $event->instructor,
                $event->member,
                $event->class
            );
        }

        // Check class capacity and alert admin if needed
        $currentBookings = $event->class->bookings()->count();
        $capacity = $event->class->capacity;

        if ($capacity && $currentBookings >= $capacity * 0.9) {
            $this->notificationService->classCapacityAlert($event->class, $currentBookings, $capacity);
        }
    }
}
