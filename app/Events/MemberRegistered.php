<?php
// app/Events/MemberRegistered.php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberRegistered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $member;
    public $registrationMethod;

    /**
     * Create a new event instance.
     */
    public function __construct(User $member, string $registrationMethod = 'web')
    {
        $this->member = $member;
        $this->registrationMethod = $registrationMethod;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
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
            'member_phone' => $this->member->phone,
            'registration_method' => $this->registrationMethod,
            'registered_at' => $this->member->created_at->toIso8601String(),
        ];
    }
}
