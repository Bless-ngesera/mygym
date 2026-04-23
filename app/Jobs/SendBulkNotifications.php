<?php
// app/Jobs/SendBulkNotifications.php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBulkNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $userIds;
    protected $notificationData;

    public function __construct(array $userIds, array $notificationData)
    {
        $this->userIds = $userIds;
        $this->notificationData = $notificationData;
    }

    public function handle(NotificationService $notificationService)
    {
        $users = User::whereIn('id', $this->userIds)->get();

        foreach ($users as $user) {
            $notificationService->sendToUser($user, $this->notificationData);
        }
    }
}
