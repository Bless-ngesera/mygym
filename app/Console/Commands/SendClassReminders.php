<?php
// app/Console/Commands/SendClassReminders.php

namespace App\Console\Commands;

use App\Models\ScheduledClass;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendClassReminders extends Command
{
    protected $signature = 'notifications:class-reminders
                            {--hours=24 : Hours before class to send reminder (24 or 1)}
                            {--dry-run : Simulate without sending}';

    protected $description = 'Send class reminders to members and instructors';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $hoursBefore = (int) $this->option('hours');

        if (!in_array($hoursBefore, [1, 24])) {
            $this->error('Hours must be either 1 or 24');
            return 1;
        }

        $this->info("Sending {$hoursBefore}-hour class reminders...");
        $dryRun = $this->option('dry-run');

        // Find classes starting in exactly {$hoursBefore} hours
        $targetTime = now()->addHours($hoursBefore);
        $timeWindow = $targetTime->copy()->subMinutes(30);

        $classes = ScheduledClass::whereBetween('date_time', [
            $timeWindow,
            $targetTime
        ])->with(['bookings.user', 'instructor'])->get();

        $this->info("Found {$classes->count()} classes to remind");

        $memberReminders = 0;
        $instructorReminders = 0;
        $errors = [];

        foreach ($classes as $class) {
            // Send reminders to members
            foreach ($class->bookings as $booking) {
                if (!$booking->user) continue;

                try {
                    if (!$dryRun) {
                        $this->notificationService->classReminder(
                            $booking->user,
                            $class,
                            $hoursBefore
                        );
                        $memberReminders++;
                    }

                    $this->line("Member reminder: {$booking->user->name} - {$class->name}");

                } catch (\Exception $e) {
                    $errors[] = [
                        'type' => 'member',
                        'user_id' => $booking->user->id,
                        'class_id' => $class->id,
                        'error' => $e->getMessage()
                    ];
                }
            }

            // Send reminder to instructor
            if ($class->instructor) {
                try {
                    if (!$dryRun) {
                        $this->notificationService->classReminder(
                            $class->instructor,
                            $class,
                            $hoursBefore
                        );
                        $instructorReminders++;
                    }

                    $this->line("Instructor reminder: {$class->instructor->name} - {$class->name}");

                } catch (\Exception $e) {
                    $errors[] = [
                        'type' => 'instructor',
                        'user_id' => $class->instructor->id,
                        'class_id' => $class->id,
                        'error' => $e->getMessage()
                    ];
                }
            }

            // Log for analytics
            Log::info('Class reminders sent', [
                'class_id' => $class->id,
                'class_name' => $class->name,
                'hours_before' => $hoursBefore,
                'member_reminders' => $class->bookings->count(),
                'dry_run' => $dryRun
            ]);
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Classes', $classes->count()],
                ['Member Reminders', $memberReminders],
                ['Instructor Reminders', $instructorReminders],
                ['Total Reminders', $memberReminders + $instructorReminders],
                ['Errors', count($errors)],
                ['Dry Run', $dryRun ? 'Yes' : 'No']
            ]
        );

        if (!empty($errors)) {
            Log::warning('Class reminder errors', ['errors' => $errors]);
        }

        return 0;
    }
}
