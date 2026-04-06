<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Workout;
use Carbon\Carbon;

class WorkoutSeeder extends Seeder
{
    public function run()
    {
        // Get the first member user
        $member = User::where('role', 'member')->first();

        if (!$member) {
            $this->command->error('No member found! Please create a member first.');
            return;
        }

        // Today's workout
        Workout::updateOrCreate(
            [
                'user_id' => $member->id,
                'date' => Carbon::today(),
            ],
            [
                'title' => 'Upper Body Strength',
                'description' => 'Focus on chest, shoulders, and triceps',
                'date' => Carbon::today(),
                'status' => 'pending',
            ]
        );

        // Past workouts (last 14 days)
        $workoutTitles = [
            'Full Body Blast',
            'Leg Day Destroyer',
            'Push Day Power',
            'Pull Day Strength',
            'Cardio Crusher',
            'HIIT Session',
            'Core Stability',
            'Arm Day',
            'Back Builder',
            'Chest Focus'
        ];

        for ($i = 1; $i <= 10; $i++) {
            Workout::updateOrCreate(
                [
                    'user_id' => $member->id,
                    'date' => Carbon::today()->subDays($i),
                ],
                [
                    'title' => $workoutTitles[array_rand($workoutTitles)],
                    'description' => 'Completed workout session',
                    'date' => Carbon::today()->subDays($i),
                    'status' => 'completed',
                    'completed_at' => Carbon::today()->subDays($i)->setTime(10, 30),
                    'duration_minutes' => rand(45, 75),
                ]
            );
        }

        // Upcoming workouts (next 7 days)
        for ($i = 1; $i <= 5; $i++) {
            Workout::updateOrCreate(
                [
                    'user_id' => $member->id,
                    'date' => Carbon::today()->addDays($i),
                ],
                [
                    'title' => $workoutTitles[array_rand($workoutTitles)],
                    'description' => 'Scheduled workout - get ready!',
                    'date' => Carbon::today()->addDays($i),
                    'status' => 'pending',
                ]
            );
        }

        $this->command->info('Workouts seeded successfully!');
    }
}
