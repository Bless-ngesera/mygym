<?php
// app/Console/Commands/CheckExpiringSubscriptions.php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiringSubscriptions extends Command
{
    protected $signature = 'notifications:check-subscriptions
                            {--days=30 : Check subscriptions expiring within X days}
                            {--dry-run : Simulate without sending}';

    protected $description = 'Check for expiring subscriptions and send reminders';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('Checking for expiring subscriptions...');
        $daysThreshold = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        // Find users with expiring subscriptions
        $expiringUsers = User::whereHas('subscription', function($query) use ($daysThreshold) {
            $query->where('end_date', '<=', now()->addDays($daysThreshold))
                  ->where('end_date', '>', now())
                  ->where('status', 'active');
        })->with('subscription')->get();

        $this->info("Found {$expiringUsers->count()} users with expiring subscriptions");

        $remindersSent = [];
        $errors = [];

        foreach ($expiringUsers as $user) {
            $daysLeft = now()->diffInDays($user->subscription->end_date, false);
            $milestones = [30, 14, 7, 3, 1];

            // Check if we should send a reminder today
            if (in_array($daysLeft, $milestones)) {
                try {
                    if (!$dryRun) {
                        $this->notificationService->subscriptionExpiring($user, $daysLeft);
                    }

                    $remindersSent[] = [
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'days_left' => $daysLeft,
                        'plan_name' => $user->subscription->plan_name,
                        'expiry_date' => $user->subscription->end_date->format('Y-m-d')
                    ];

                    $this->line("Reminder sent to: {$user->name} - {$daysLeft} days left");

                    // Log the reminder
                    Log::info('Subscription expiry reminder sent', [
                        'user_id' => $user->id,
                        'days_left' => $daysLeft,
                        'plan_name' => $user->subscription->plan_name,
                        'expiry_date' => $user->subscription->end_date,
                        'dry_run' => $dryRun
                    ]);

                } catch (\Exception $e) {
                    $errors[] = [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'days_left' => $daysLeft,
                        'error' => $e->getMessage()
                    ];
                    $this->error("Failed for {$user->name}: " . $e->getMessage());
                }
            }
        }

        // Summary
        $this->newLine();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Expiring Subscriptions', $expiringUsers->count()],
                ['Reminders Sent', count($remindersSent)],
                ['Errors', count($errors)],
                ['Days Threshold', $daysThreshold],
                ['Dry Run', $dryRun ? 'Yes' : 'No']
            ]
        );

        if (!empty($remindersSent)) {
            $this->newLine();
            $this->info("📋 Reminders Sent:");
            $this->table(
                ['User', 'Days Left', 'Plan', 'Expiry Date'],
                array_map(function($r) {
                    return [
                        $r['name'],
                        $r['days_left'],
                        $r['plan_name'],
                        $r['expiry_date']
                    ];
                }, $remindersSent)
            );
        }

        // Critical: Notify admins about expiring subscriptions (7 days or less)
        $criticalExpirations = array_filter($remindersSent, function($r) {
            return $r['days_left'] <= 7;
        });

        if (!empty($criticalExpirations) && !$dryRun) {
            $this->notificationService->sendToAdmins([
                'type' => 'critical_subscriptions_expiring',
                'title' => '⚠️ Multiple Subscriptions Expiring Soon',
                'message' => count($criticalExpirations) . ' subscriptions will expire within 7 days',
                'priority' => 'high',
                'action_url' => route('admin.members.index'),
                'data' => ['expiring_count' => count($criticalExpirations)]
            ]);
        }

        if (!empty($errors)) {
            Log::warning('Subscription check errors', ['errors' => $errors]);
        }

        return 0;
    }
}
