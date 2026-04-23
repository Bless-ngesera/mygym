<?php
// app/Events/MemberCheckedIn.php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberCheckedIn implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $member;
    public $streakCount;
    public $checkInTime;

    /**
     * Create a new event instance.
     */
    public function __construct(User $member, int $streakCount)
    {
        $this->member = $member;
        $this->streakCount = $streakCount;
        $this->checkInTime = now();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->member->id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->member->id,
            'name' => $this->member->name,
            'streak_count' => $this->streakCount,
            'check_in_time' => $this->checkInTime->toIso8601String(),
            'message' => "{$this->member->name} just checked in! Streak: {$this->streakCount} days"
        ];
    }
}
