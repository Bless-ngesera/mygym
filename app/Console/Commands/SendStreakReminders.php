<?php
// app/Console/Commands/SendStreakReminders.php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SendStreakReminders extends Command
{
    protected $signature = 'notifications:streak-reminders
                            {--dry-run : Simulate without sending}';

    protected $description = 'Send reminders to users about to break their streaks';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('Checking for streak reminders...');
        $dryRun = $this->option('dry-run');

        // Check if attendances table exists
        if (!Schema::hasTable('attendances')) {
            $this->warn('Attendances table not found. Skipping streak reminders.');
            return 0;
        }

        // Find users who haven't checked in today but have checked in yesterday
        $usersWithStreaks = User::whereHas('attendances', function($query) {
            $query->whereDate('created_at', now()->subDay());
        })->whereDoesntHave('attendances', function($query) {
            $query->whereDate('created_at', today());
        })->get();

        $this->info("Found {$usersWithStreaks->count()} users at risk of breaking their streaks");

        $remindersSent = 0;
        $errors = [];

        foreach ($usersWithStreaks as $user) {
            $streakCount = $this->getCurrentStreak($user);

            if ($streakCount >= 3) {
                try {
                    if (!$dryRun) {
                        $this->notificationService->sendToUser($user, [
                            'type' => 'streak_reminder',
                            'title' => '🔥 Don\'t Break Your Streak!',
                            'message' => "You have a {$streakCount}-day streak! Check in today to keep it going.",
                            'priority' => 'high',
                            'action_url' => route('member.dashboard'),
                            'data' => ['current_streak' => $streakCount]
                        ]);
                        $remindersSent++;
                    }

                    $this->line("Streak reminder sent to: {$user->name} (streak: {$streakCount} days)");

                    Log::info('Streak reminder sent', [
                        'user_id' => $user->id,
                        'current_streak' => $streakCount,
                        'dry_run' => $dryRun
                    ]);

                } catch (\Exception $e) {
                    $errors[] = [
                        'user_id' => $user->id,
                        'streak' => $streakCount,
                        'error' => $e->getMessage()
                    ];
                    $this->error("Failed for {$user->name}: " . $e->getMessage());
                }
            }
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Users at Risk', $usersWithStreaks->count()],
                ['Reminders Sent', $remindersSent],
                ['Errors', count($errors)],
                ['Dry Run', $dryRun ? 'Yes' : 'No']
            ]
        );

        if (!empty($errors)) {
            Log::warning('Streak reminder errors', ['errors' => $errors]);
        }

        return 0;
    }

    private function getCurrentStreak($user): int
    {
        $attendances = DB::table('attendances')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $streak = 0;
        $currentDate = now()->startOfDay();

        foreach ($attendances as $attendance) {
            $attendanceDate = \Carbon\Carbon::parse($attendance->created_at)->startOfDay();

            if ($attendanceDate->eq($currentDate)) {
                $streak++;
                $currentDate->subDay();
            } elseif ($attendanceDate->eq($currentDate->copy()->addDay())) {
                $streak++;
                $currentDate = $attendanceDate->copy()->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}
