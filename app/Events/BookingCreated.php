<?php
// app/Events/BookingCreated.php

namespace App\Events;

use App\Models\User;
use App\Models\Booking;
use App\Models\ScheduledClass;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $member;
    public $class;
    public $instructor;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->member = $booking->user;
        $this->class = $booking->class;
        $this->instructor = $booking->class->instructor;
    }

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('user.' . $this->member->id),
        ];

        if ($this->instructor) {
            $channels[] = new PrivateChannel('user.' . $this->instructor->id);
        }

        return $channels;
    }

    public function broadcastWith(): array
    {
        return [
            'booking_id' => $this->booking->id,
            'member_id' => $this->member->id,
            'member_name' => $this->member->name,
            'class_id' => $this->class->id,
            'class_name' => $this->class->name,
            'class_time' => $this->class->date_time->toIso8601String(),
            'instructor_id' => $this->instructor?->id,
            'instructor_name' => $this->instructor?->name,
        ];
    }
}
