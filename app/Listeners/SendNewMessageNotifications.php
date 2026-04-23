<?php
// app/Listeners/SendNewMessageNotifications.php

namespace App\Listeners;

use App\Events\NewMessageSent;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNewMessageNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(NewMessageSent $event): void
    {
        // Send notification to receiver
        $this->notificationService->newMessage(
            $event->receiver,
            $event->sender,
            $event->message->message
        );
    }
}
