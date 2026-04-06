<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exercise;

class ExerciseSeeder extends Seeder
{
    public function run()
    {
        $exercises = [
            ['name' => 'Bench Press', 'muscle_group' => 'Chest', 'difficulty' => 'intermediate', 'description' => 'Lie on bench, lower bar to chest, press up'],
            ['name' => 'Incline Bench Press', 'muscle_group' => 'Chest', 'difficulty' => 'intermediate', 'description' => 'Upper chest focus on incline bench'],
            ['name' => 'Decline Bench Press', 'muscle_group' => 'Chest', 'difficulty' => 'intermediate', 'description' => 'Lower chest focus on decline bench'],
            ['name' => 'Dumbbell Flyes', 'muscle_group' => 'Chest', 'difficulty' => 'beginner', 'description' => 'Open arms with dumbbells, squeeze chest'],
            ['name' => 'Push Ups', 'muscle_group' => 'Chest', 'difficulty' => 'beginner', 'description' => 'Bodyweight chest exercise'],
            ['name' => 'Squat', 'muscle_group' => 'Legs', 'difficulty' => 'intermediate', 'description' => 'Lower body with barbell on shoulders'],
            ['name' => 'Front Squat', 'muscle_group' => 'Legs', 'difficulty' => 'advanced', 'description' => 'Barbell held in front of shoulders'],
            ['name' => 'Leg Press', 'muscle_group' => 'Legs', 'difficulty' => 'beginner', 'description' => 'Push platform away with legs'],
            ['name' => 'Lunges', 'muscle_group' => 'Legs', 'difficulty' => 'beginner', 'description' => 'Step forward, lower hips'],
            ['name' => 'Deadlift', 'muscle_group' => 'Back', 'difficulty' => 'advanced', 'description' => 'Lift barbell from floor to standing'],
            ['name' => 'Romanian Deadlift', 'muscle_group' => 'Back', 'difficulty' => 'intermediate', 'description' => 'Hinge at hips with slight knee bend'],
            ['name' => 'Pull Ups', 'muscle_group' => 'Back', 'difficulty' => 'advanced', 'description' => 'Pull body up to bar'],
            ['name' => 'Lat Pulldown', 'muscle_group' => 'Back', 'difficulty' => 'intermediate', 'description' => 'Pull bar down to chest'],
            ['name' => 'Seated Rows', 'muscle_group' => 'Back', 'difficulty' => 'beginner', 'description' => 'Pull handle toward chest'],
            ['name' => 'Shoulder Press', 'muscle_group' => 'Shoulders', 'difficulty' => 'intermediate', 'description' => 'Press dumbbells overhead'],
            ['name' => 'Lateral Raises', 'muscle_group' => 'Shoulders', 'difficulty' => 'beginner', 'description' => 'Raise arms to sides'],
            ['name' => 'Front Raises', 'muscle_group' => 'Shoulders', 'difficulty' => 'beginner', 'description' => 'Raise arms to front'],
            ['name' => 'Bicep Curls', 'muscle_group' => 'Arms', 'difficulty' => 'beginner', 'description' => 'Curl dumbbells toward shoulders'],
            ['name' => 'Hammer Curls', 'muscle_group' => 'Arms', 'difficulty' => 'beginner', 'description' => 'Palms facing each other curl'],
            ['name' => 'Tricep Extensions', 'muscle_group' => 'Arms', 'difficulty' => 'beginner', 'description' => 'Extend arms overhead with dumbbell'],
            ['name' => 'Tricep Pushdowns', 'muscle_group' => 'Arms', 'difficulty' => 'beginner', 'description' => 'Push cable attachment down'],
            ['name' => 'Plank', 'muscle_group' => 'Core', 'difficulty' => 'beginner', 'description' => 'Hold body in straight line'],
            ['name' => 'Russian Twists', 'muscle_group' => 'Core', 'difficulty' => 'intermediate', 'description' => 'Twist torso side to side'],
            ['name' => 'Leg Raises', 'muscle_group' => 'Core', 'difficulty' => 'intermediate', 'description' => 'Raise legs while lying on back'],
            ['name' => 'Burpees', 'muscle_group' => 'Full Body', 'difficulty' => 'advanced', 'description' => 'Squat, jump back, jump up'],
            ['name' => 'Mountain Climbers', 'muscle_group' => 'Cardio', 'difficulty' => 'intermediate', 'description' => 'Alternate knees to chest in plank'],
            ['name' => 'Jumping Jacks', 'muscle_group' => 'Cardio', 'difficulty' => 'beginner', 'description' => 'Jump with arms and legs out'],
            ['name' => 'High Knees', 'muscle_group' => 'Cardio', 'difficulty' => 'intermediate', 'description' => 'Run in place lifting knees high'],
        ];

        foreach ($exercises as $exercise) {
            Exercise::updateOrCreate(
                ['name' => $exercise['name']],
                $exercise
            );
        }

        $this->command->info('Exercises seeded successfully!');
    }
}
