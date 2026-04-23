<?php
// app/Events/PaymentProcessed.php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $amount;
    public $status;
    public $reference;
    public $receiptId;
    public $paymentMethod;
    public $error;

    /**
     * Create a new event instance.
     */
    public function __construct(
        User $user,
        float $amount,
        string $status,
        string $reference,
        ?int $receiptId = null,
        ?string $paymentMethod = null,
        ?string $error = null
    ) {
        $this->user = $user;
        $this->amount = $amount;
        $this->status = $status;
        $this->reference = $reference;
        $this->receiptId = $receiptId;
        $this->paymentMethod = $paymentMethod;
        $this->error = $error;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('user.' . $this->user->id),
        ];

        if ($this->status === 'failed') {
            $channels[] = new PrivateChannel('admin.notifications');
        }

        if ($this->amount > 1000000) { // Large payments notify admins
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
            'amount' => $this->amount,
            'amount_formatted' => 'UGX ' . number_format($this->amount, 0),
            'status' => $this->status,
            'reference' => $this->reference,
            'receipt_id' => $this->receiptId,
            'payment_method' => $this->paymentMethod,
            'error' => $this->error,
            'processed_at' => now()->toIso8601String(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'payment.processed';
    }
}
