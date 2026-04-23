<?php
// app/Console/Commands/SendScheduledNotifications.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class SendScheduledNotifications extends Command
{
    protected $signature = 'notifications:send-scheduled
                            {--type=all : Type of notifications to send (all, workout, class, motivation, reports, subscriptions)}';

    protected $description = 'Send all scheduled notifications (reminders, motivation, reports)';

    public function handle()
    {
        $this->info('Starting scheduled notifications dispatch...');
        $startTime = microtime(true);

        $type = $this->option('type');

        try {
            if ($type === 'all' || $type === 'workout') {
                $this->call('notifications:workout-reminders');
                $this->info('✓ Workout reminders sent');
            }

            if ($type === 'all' || $type === 'class') {
                $this->call('notifications:class-reminders');
                $this->info('✓ Class reminders sent');
            }

            if ($type === 'all' || $type === 'motivation') {
                if (now()->hour === 6) { // Send at 6 AM only
                    $this->call('notifications:daily-motivation');
                    $this->info('✓ Daily motivation sent');
                }
            }

            if ($type === 'all' || $type === 'reports') {
                if (now()->isSunday() && now()->hour === 20) { // Sunday 8 PM
                    $this->call('notifications:weekly-reports');
                    $this->info('✓ Weekly reports sent');
                }
            }

            if ($type === 'all' || $type === 'subscriptions') {
                $this->call('notifications:check-subscriptions');
                $this->info('✓ Subscription checks completed');
            }

            if ($type === 'all') {
                $this->call('notifications:streak-reminders');
                $this->info('✓ Streak reminders sent');
            }

            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $this->info("All scheduled notifications dispatched in {$duration}ms");

            Log::info('Scheduled notifications dispatched', [
                'type' => $type,
                'duration_ms' => $duration
            ]);

        } catch (\Exception $e) {
            $this->error('Failed to send scheduled notifications: ' . $e->getMessage());
            Log::error('Scheduled notifications failed', [
                'type' => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return 1;
        }

        return 0;
    }
}
