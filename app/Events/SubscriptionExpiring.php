<?php
// app/Events/SubscriptionExpiring.php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiring implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $daysLeft;
    public $planName;
    public $expiryDate;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, int $daysLeft, ?string $planName = null, ?string $expiryDate = null)
    {
        $this->user = $user;
        $this->daysLeft = $daysLeft;
        $this->planName = $planName ?? $user->subscription?->plan_name ?? 'Current Plan';
        $this->expiryDate = $expiryDate ?? $user->subscription?->end_date?->toIso8601String();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('user.' . $this->user->id),
        ];

        // Notify admins for critical expirations
        if ($this->daysLeft <= 7) {
            $channels[] = new PrivateChannel('admin.notifications');
        }

        return $channels;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'days_left' => $this->daysLeft,
            'plan_name' => $this->planName,
            'expiry_date' => $this->expiryDate,
            'priority' => $this->daysLeft <= 3 ? 'critical' : ($this->daysLeft <= 7 ? 'high' : 'medium'),
            'notification_sent_at' => now()->toIso8601String(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'subscription.expiring';
    }
}
