<?php
// app/Console/Commands/SendDailyMotivation.php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDailyMotivation extends Command
{
    protected $signature = 'notifications:daily-motivation
                            {--role=member : Role to send motivation to (member, instructor, all)}
                            {--dry-run : Simulate without sending}';

    protected $description = 'Send daily motivational quotes to users';

    protected $notificationService;

    protected $quotes = [
        'fitness' => [
            "The only bad workout is the one that didn't happen.",
            "Your body can stand almost anything. It's your mind you have to convince.",
            "Don't limit your challenges. Challenge your limits.",
            "Success starts with self-discipline.",
            "Make your sweat your masterpiece.",
            "Small progress is still progress.",
            "You didn't come this far to only come this far.",
            "The pain you feel today will be the strength you feel tomorrow.",
            "Motivation is what gets you started. Habit is what keeps you going.",
            "Strive for progress, not perfection.",
        ],
        'mindset' => [
            "Believe you can and you're halfway there.",
            "Your only limit is your mind.",
            "The secret of getting ahead is getting started.",
            "It's not about being the best. It's about being better than you were yesterday.",
            "The harder you work for something, the greater you'll feel when you achieve it.",
        ],
        'recovery' => [
            "Rest when you're weary. Refresh and renew yourself.",
            "Recovery is just as important as the workout.",
            "Listen to your body. It knows what it needs.",
            "Progress happens outside your comfort zone, but recovery happens within it.",
        ]
    ];

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('Sending daily motivation...');
        $role = $this->option('role');
        $dryRun = $this->option('dry-run');

        // Build user query - removed is_active condition
        $query = User::query(); // Changed from User::query()

        if ($role !== 'all') {
            $query->where('role', $role);
        }

        $users = $query->get();

        $this->info("Found {$users->count()} users to send motivation to");

        $sentCount = 0;
        $errors = [];

        // Select a random quote for today
        $allQuotes = array_merge(...array_values($this->quotes));
        $quote = $allQuotes[array_rand($allQuotes)];

        foreach ($users as $user) {
            try {
                if (!$dryRun) {
                    $this->notificationService->dailyMotivation($user);
                    $sentCount++;
                }

                $this->line("Motivation sent to: {$user->name} ({$user->email})");

            } catch (\Exception $e) {
                $errors[] = [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ];
                $this->error("Failed for {$user->name}: " . $e->getMessage());
            }
        }

        // Log the quote used
        Log::info('Daily motivation sent', [
            'quote' => $quote,
            'recipients' => $sentCount,
            'role' => $role,
            'dry_run' => $dryRun
        ]);

        $this->newLine();
        $this->line("📝 Quote of the day:");
        $this->line("   \"{$quote}\"");
        $this->newLine();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Users', $users->count()],
                ['Motivation Sent', $sentCount],
                ['Errors', count($errors)],
                ['Dry Run', $dryRun ? 'Yes' : 'No']
            ]
        );

        if (!empty($errors)) {
            Log::warning('Daily motivation errors', ['errors' => $errors]);
        }

        return 0;
    }
}
