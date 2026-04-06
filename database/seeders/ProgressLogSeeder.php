<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ProgressLog;
use Carbon\Carbon;

class ProgressLogSeeder extends Seeder
{
    public function run()
    {
        $member = User::where('role', 'member')->first();

        if (!$member) {
            $this->command->error('No member found!');
            return;
        }

        $startWeight = 85;

        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weight = $startWeight - ($i * 0.1);

            ProgressLog::updateOrCreate(
                [
                    'user_id' => $member->id,
                    'date' => $date,
                ],
                [
                    'weight_kg' => round($weight, 1),
                    'body_fat_percentage' => round(25 - ($i * 0.05), 1),
                    'chest_cm' => rand(90, 105),
                    'waist_cm' => rand(75, 90),
                    'hips_cm' => rand(95, 110),
                    'biceps_cm' => rand(30, 40),
                    'thighs_cm' => rand(50, 65),
                    'notes' => $i % 7 == 0 ? 'Weekly progress check' : null,
                ]
            );
        }

        $this->command->info('Progress logs seeded successfully!');
    }
}
