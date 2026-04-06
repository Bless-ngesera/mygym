<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Goal;
use Carbon\Carbon;

class GoalSeeder extends Seeder
{
    public function run()
    {
        $member = User::where('role', 'member')->first();

        if (!$member) {
            $this->command->error('No member found!');
            return;
        }

        $goals = [
            ['title' => 'Lose 5 kg', 'type' => 'weight', 'target_value' => 80, 'current_value' => 83, 'deadline' => Carbon::today()->addMonths(2), 'unit' => 'kg'],
            ['title' => 'Complete 20 Workouts', 'type' => 'workouts', 'target_value' => 20, 'current_value' => 12, 'deadline' => Carbon::today()->addMonths(1), 'unit' => 'workouts'],
            ['title' => 'Attend 15 Classes', 'type' => 'attendance', 'target_value' => 15, 'current_value' => 8, 'deadline' => Carbon::today()->addMonths(1), 'unit' => 'sessions'],
            ['title' => 'Bench Press 100kg', 'type' => 'strength', 'target_value' => 100, 'current_value' => 75, 'deadline' => Carbon::today()->addMonths(3), 'unit' => 'kg'],
        ];

        foreach ($goals as $goal) {
            Goal::updateOrCreate(
                [
                    'user_id' => $member->id,
                    'title' => $goal['title'],
                ],
                [
                    'type' => $goal['type'],
                    'target_value' => $goal['target_value'],
                    'current_value' => $goal['current_value'],
                    'deadline' => $goal['deadline'],
                    'unit' => $goal['unit'],
                    'status' => 'active',
                ]
            );
        }

        $this->command->info('Goals seeded successfully!');
    }
}
