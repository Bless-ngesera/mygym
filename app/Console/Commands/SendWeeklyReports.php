<?php
// app/Console/Commands/SendWeeklyReports.php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SendWeeklyReports extends Command
{
    protected $signature = 'notifications:weekly-reports
                            {--role=member : Role to send reports to}
                            {--dry-run : Simulate without sending}';

    protected $description = 'Send weekly progress reports to users';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('Generating and sending weekly reports...');
        $role = $this->option('role');
        $dryRun = $this->option('dry-run');

        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $this->info("Reporting period: {$startOfWeek->format('M d')} - {$endOfWeek->format('M d, Y')}");

        // Build user query
        $query = User::query();

        if ($role !== 'all') {
            $query->where('role', $role);
        }

        $users = $query->get();

        $this->info("Found {$users->count()} users to send reports to");

        $sentCount = 0;
        $errors = [];
        $reportData = [];

        foreach ($users as $user) {
            try {
                $stats = $this->calculateUserStats($user, $startOfWeek, $endOfWeek);

                if (!$dryRun) {
                    $this->notificationService->weeklyReport(
                        $user,
                        $stats['workouts_completed'],
                        $stats['checkins']
                    );
                    $sentCount++;
                }

                $reportData[] = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'workouts' => $stats['workouts_completed'],
                    'checkins' => $stats['checkins'],
                    'calories' => $stats['calories_burned']
                ];

                $this->line("Report sent to: {$user->name} - {$stats['workouts_completed']} workouts, {$stats['checkins']} check-ins");

                Log::info('Weekly report sent', [
                    'user_id' => $user->id,
                    'user_role' => $user->role,
                    'stats' => $stats,
                    'dry_run' => $dryRun
                ]);

            } catch (\Exception $e) {
                $errors[] = [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ];
                $this->error("Failed for {$user->name}: " . $e->getMessage());
            }
        }

        // Generate summary
        $totalWorkouts = array_sum(array_column($reportData, 'workouts'));
        $totalCheckins = array_sum(array_column($reportData, 'checkins'));
        $totalCalories = array_sum(array_column($reportData, 'calories'));

        $this->newLine();
        $this->info("📊 Weekly Summary:");
        $this->table(
            ['Metric', 'Total'],
            [
                ['Total Workouts', $totalWorkouts],
                ['Total Check-ins', $totalCheckins],
                ['Total Calories Burned', number_format($totalCalories)],
                ['Reports Sent', $sentCount],
                ['Errors', count($errors)]
            ]
        );

        // Display top performers
        usort($reportData, function($a, $b) {
            return $b['workouts'] <=> $a['workouts'];
        });

        $topPerformers = array_slice($reportData, 0, 5);

        if (!empty($topPerformers)) {
            $this->newLine();
            $this->info("🏆 Top Performers This Week:");
            $this->table(
                ['User', 'Workouts', 'Check-ins', 'Calories'],
                array_map(function($p) {
                    return [
                        $p['name'],
                        $p['workouts'],
                        $p['checkins'],
                        number_format($p['calories'])
                    ];
                }, $topPerformers)
            );
        }

        Log::info('Weekly reports completed', [
            'role' => $role,
            'users_processed' => $users->count(),
            'reports_sent' => $sentCount,
            'total_workouts' => $totalWorkouts,
            'total_checkins' => $totalCheckins,
            'errors' => count($errors),
            'dry_run' => $dryRun
        ]);

        return 0;
    }

    private function calculateUserStats($user, $startOfWeek, $endOfWeek): array
    {
        // Workouts completed this week (status = 'completed')
        $workoutsCompleted = DB::table('workouts')
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
            ->count();

        // Check-ins this week (from attendances table)
        $checkins = 0;
        if (Schema::hasTable('attendances')) {
            $checkins = DB::table('attendances')
                ->where('user_id', $user->id)
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->count();
        }

        // Calories burned
        $caloriesBurned = DB::table('workouts')
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
            ->sum('calories_burn');

        return [
            'workouts_completed' => $workoutsCompleted,
            'checkins' => $checkins,
            'calories_burned' => $caloriesBurned
        ];
    }
}
