<?php
// app/Events/NotificationRead.php

namespace App\Events;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $user;
    public $readAt;

    /**
     * Create a new event instance.
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
        $this->user = $notification->user;
        $this->readAt = $notification->read_at ?? now();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->user->id),
            new PrivateChannel('notifications.' . $this->user->id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'notification_id' => $this->notification->id,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'notification_type' => $this->notification->type,
            'notification_title' => $this->notification->title,
            'read_at' => $this->readAt->toIso8601String(),
            'was_already_read' => $this->notification->read_at !== null,
            'unread_count' => $this->getUnreadCount(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'notification.read';
    }

    /**
     * Get the user's current unread notification count.
     */
    private function getUnreadCount(): int
    {
        return Notification::where('user_id', $this->user->id)
            ->where('read', false)
            ->count();
    }
}
