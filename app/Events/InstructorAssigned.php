<?php
// app/Events/InstructorAssigned.php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InstructorAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $member;
    public $instructor;
    public $assignedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(User $member, User $instructor, ?string $assignedBy = null)
    {
        $this->member = $member;
        $this->instructor = $instructor;
        $this->assignedBy = $assignedBy;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->member->id),
            new PrivateChannel('user.' . $this->instructor->id),
            new PrivateChannel('admin.notifications'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'member_id' => $this->member->id,
            'member_name' => $this->member->name,
            'member_email' => $this->member->email,
            'instructor_id' => $this->instructor->id,
            'instructor_name' => $this->instructor->name,
            'instructor_email' => $this->instructor->email,
            'assigned_by' => $this->assignedBy,
            'assigned_at' => now()->toIso8601String(),
        ];
    }
}
