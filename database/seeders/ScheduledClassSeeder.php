<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScheduledClass;
use App\Models\Instructor;
use App\Models\ClassType;
use Carbon\Carbon;

class ScheduledClassSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = Instructor::all();
        $classTypes  = ClassType::all();

        if ($instructors->isEmpty() || $classTypes->isEmpty()) {
            $this->command->warn('Skipping ScheduledClassSeeder: no instructors or class types found.');
            return;
        }

        $slots  = [];
        $start  = Carbon::tomorrow()->setTime(6, 0, 0); // start at 6am tomorrow

        // Generate 20 unique 2-hour slots across 5 days
        for ($day = 0; $day < 5; $day++) {
            for ($hour = 0; $hour < 4; $hour++) {
                $slots[] = $start->copy()->addDays($day)->addHours($hour * 3);
            }
        }

        foreach ($slots as $dateTime) {
            ScheduledClass::create([
                'instructor_id' => $instructors->random()->id,
                'class_type_id' => $classTypes->random()->id,
                'date_time'     => $dateTime,
                'price'         => fake()->randomElement([10, 15, 20, 25, 30]),
            ]);
        }

        $this->command->info('Seeded ' . count($slots) . ' scheduled classes.');
    }
}
