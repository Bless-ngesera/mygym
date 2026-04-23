<?php
// app/Console/Commands/ProcessPendingPushNotifications.php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ProcessPendingPushNotifications extends Command
{
    protected $signature = 'notifications:process-push
                            {--limit=100 : Number of notifications to process}
                            {--dry-run : Simulate without sending}';

    protected $description = 'Process pending push notifications that haven\'t been delivered';

    public function handle()
    {
        $this->info('Processing pending push notifications...');
        $limit = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');

        // Check if channels column exists
        if (!Schema::hasColumn('notifications', 'channels')) {
            $this->warn('Notifications table does not have channels column. Run migration to add it.');
            return 0;
        }

        // Find notifications that should have been pushed but weren't delivered
        $pendingNotifications = Notification::whereNotNull('channels')
            ->whereJsonContains('channels', 'push')
            ->whereNull('delivered_at')
            ->where('created_at', '>', now()->subHours(24))
            ->with('user')
            ->limit($limit)
            ->get();

        $this->info("Found {$pendingNotifications->count()} pending push notifications");

        $processed = 0;
        $errors = [];

        foreach ($pendingNotifications as $notification) {
            if (!$notification->user) {
                continue;
            }

            try {
                if (!$dryRun) {
                    $this->sendPush($notification);
                    $notification->markAsDelivered();
                    $processed++;
                }

                $this->line("Push notification processed for: {$notification->user->name} - {$notification->title}");

            } catch (\Exception $e) {
                $errors[] = [
                    'notification_id' => $notification->id,
                    'user_id' => $notification->user_id,
                    'error' => $e->getMessage()
                ];
                $this->error("Failed: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Found Pending', $pendingNotifications->count()],
                ['Processed', $processed],
                ['Errors', count($errors)],
                ['Limit', $limit],
                ['Dry Run', $dryRun ? 'Yes' : 'No']
            ]
        );

        return 0;
    }

    private function sendPush($notification)
    {
        Log::info("Push notification would be sent", [
            'user_id' => $notification->user_id,
            'title' => $notification->title,
            'action_url' => $notification->action_url
        ]);
    }
}
