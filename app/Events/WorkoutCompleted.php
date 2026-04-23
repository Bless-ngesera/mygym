<?php
// app/Events/WorkoutCompleted.php

namespace App\Events;

use App\Models\User;
use App\Models\Workout;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkoutCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $workout;
    public $completedAt;

    public function __construct(User $user, Workout $workout)
    {
        $this->user = $user;
        $this->workout = $workout;
        $this->completedAt = now();
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
            'workout_id' => $this->workout->id,
            'workout_title' => $this->workout->title,
            'workout_duration' => $this->workout->duration,
            'calories_burned' => $this->workout->calories_burn,
            'completed_at' => $this->completedAt->toIso8601String(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'workout.completed';
    }
}
