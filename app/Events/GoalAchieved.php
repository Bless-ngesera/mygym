<?php
// app/Events/GoalAchieved.php

namespace App\Events;

use App\Models\User;
use App\Models\Goal;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GoalAchieved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $goal;
    public $achievedAt;

    public function __construct(User $user, Goal $goal)
    {
        $this->user = $user;
        $this->goal = $goal;
        $this->achievedAt = now();
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->user->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->user->id,
            'goal_id' => $this->goal->id,
            'goal_title' => $this->goal->title,
            'goal_type' => $this->goal->type,
            'achieved_at' => $this->achievedAt->toIso8601String(),
        ];
    }
}
