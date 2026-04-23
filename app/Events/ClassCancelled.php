<?php
// app/Events/ClassCancelled.php

namespace App\Events;

use App\Models\ScheduledClass;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClassCancelled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $class;
    public $instructor;
    public $bookings;
    public $cancelledBy;
    public $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(ScheduledClass $class, string $cancelledBy, ?string $reason = null)
    {
        $this->class = $class;
        $this->instructor = $class->instructor;
        $this->bookings = $class->bookings;
        $this->cancelledBy = $cancelledBy;
        $this->reason = $reason;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('admin.notifications'),
        ];

        if ($this->instructor) {
            $channels[] = new PrivateChannel('user.' . $this->instructor->id);
        }

        // Add channels for all members who booked this class
        foreach ($this->bookings as $booking) {
            $channels[] = new PrivateChannel('user.' . $booking->user_id);
        }

        return $channels;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'class_id' => $this->class->id,
            'class_name' => $this->class->name,
            'class_time' => $this->class->date_time->toIso8601String(),
            'instructor_id' => $this->instructor?->id,
            'instructor_name' => $this->instructor?->name,
            'cancelled_by' => $this->cancelledBy,
            'reason' => $this->reason,
            'affected_members_count' => $this->bookings->count(),
            'cancelled_at' => now()->toIso8601String(),
        ];
    }
}
