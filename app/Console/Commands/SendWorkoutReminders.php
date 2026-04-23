<?php
// app/Console/Commands/SendWorkoutReminders.php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Workout;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendWorkoutReminders extends Command
{
    protected $signature = 'notifications:workout-reminders
                            {--hours=2 : Hours before workout to send reminder}
                            {--dry-run : Simulate without sending}';

    protected $description = 'Send workout reminders to members';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('Sending workout reminders...');
        $hoursBefore = (int) $this->option('hours');
        $dryRun = $this->option('dry-run');

        // Find workouts scheduled for today in the next {$hoursBefore} hours
        $targetDate = now()->toDateString();
        $targetTime = now()->addHours($hoursBefore);

        // Get workouts with status 'scheduled' for today
        $workouts = Workout::where('date', $targetDate)
            ->where('status', 'scheduled')
            ->with('user')
            ->get();

        // Filter workouts that are within the time window
        $workouts = $workouts->filter(function ($workout) use ($targetTime) {
            // If you have time tracking, add logic here
            return true;
        });

        $this->info("Found {$workouts->count()} workouts to remind");

        $sentCount = 0;
        $errors = [];

        foreach ($workouts as $workout) {
            if (!$workout->user) {
                continue;
            }

            try {
                if (!$dryRun) {
                    $this->notificationService->workoutReminder($workout->user, $workout);
                    $sentCount++;
                }

                $this->line("Reminder for: {$workout->user->name} - {$workout->title}");

                Log::info('Workout reminder sent', [
                    'user_id' => $workout->user->id,
                    'workout_id' => $workout->id,
                    'workout_title' => $workout->title,
                    'workout_date' => $workout->date,
                    'dry_run' => $dryRun
                ]);

            } catch (\Exception $e) {
                $errors[] = [
                    'user_id' => $workout->user->id,
                    'workout_id' => $workout->id,
                    'error' => $e->getMessage()
                ];
                $this->error("Failed for {$workout->user->name}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Workouts', $workouts->count()],
                ['Reminders Sent', $sentCount],
                ['Errors', count($errors)],
                ['Dry Run', $dryRun ? 'Yes' : 'No']
            ]
        );

        return 0;
    }
}
