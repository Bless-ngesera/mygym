<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Existing seeders (keep these)
            UserSeeder::class,
            ClassTypeSeeder::class,
            PlansTableSeeder::class,
            InstructorsTableSeeder::class,
            MembersTableSeeder::class,
            PaymentsTableSeeder::class,
            ScheduledClassSeeder::class,

            // NEW SEEDERS for the dashboard features
            ExerciseSeeder::class,           // Exercises library
            WorkoutSeeder::class,            // Member workouts
            WorkoutExerciseSeeder::class,    // Pivot table for workout exercises
            ProgressLogSeeder::class,        // Weight and body measurements
            AttendanceSeeder::class,         // Gym check-in/check-out records
            SubscriptionSeeder::class,       // Member subscriptions (using MemberSubscription model)
            PaymentSeeder::class,            // Payment records
            NotificationSeeder::class,       // User notifications
            MessageSeeder::class,            // Messages between members and instructors
            GoalSeeder::class,               // Fitness goals
            NutritionLogSeeder::class,       // Daily nutrition tracking
        ]);
    }
}
