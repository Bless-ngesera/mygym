<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Workout;
use App\Models\Exercise;
use Carbon\Carbon;

class WorkoutExerciseSeeder extends Seeder
{
    public function run()
    {
        $todayWorkout = Workout::whereDate('date', Carbon::today())->first();
        $exercises = Exercise::all();

        if (!$todayWorkout || $exercises->isEmpty()) {
            $this->command->error('No workout or exercises found!');
            return;
        }

        // Attach exercises to today's workout
        $exerciseIds = $exercises->pluck('id')->take(5)->toArray();

        foreach ($exerciseIds as $exerciseId) {
            $todayWorkout->exercises()->syncWithoutDetaching([
                $exerciseId => [
                    'sets' => 3,
                    'reps' => 10,
                    'rest_seconds' => 60,
                    'weight_kg' => rand(10, 30),
                    'completed' => false,
                ]
            ]);
        }

        // Attach exercises to past workouts
        $pastWorkouts = Workout::where('status', 'completed')->get();

        foreach ($pastWorkouts as $workout) {
            $randomExercises = $exercises->random(rand(4, 7))->pluck('id')->toArray();

            foreach ($randomExercises as $exerciseId) {
                $workout->exercises()->syncWithoutDetaching([
                    $exerciseId => [
                        'sets' => rand(3, 5),
                        'reps' => rand(8, 15),
                        'rest_seconds' => rand(30, 90),
                        'weight_kg' => rand(10, 50),
                        'completed' => true,
                    ]
                ]);
            }
        }

        $this->command->info('Workout exercises seeded successfully!');
    }
}
