<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\NutritionLog;
use Carbon\Carbon;

class NutritionLogSeeder extends Seeder
{
    public function run()
    {
        $member = User::where('role', 'member')->first();

        if (!$member) {
            $this->command->error('No member found!');
            return;
        }

        // Last 7 days nutrition logs
        for ($i = 7; $i >= 1; $i--) {
            NutritionLog::updateOrCreate(
                [
                    'user_id' => $member->id,
                    'date' => Carbon::today()->subDays($i),
                ],
                [
                    'calories' => rand(1800, 2500),
                    'protein_grams' => rand(100, 160),
                    'carbs_grams' => rand(180, 250),
                    'fat_grams' => rand(50, 80),
                    'fiber_grams' => rand(20, 35),
                    'sugar_grams' => rand(40, 70),
                    'water_ml' => rand(1500, 3000),
                    'notes' => $i == 3 ? 'Felt great today!' : null,
                ]
            );
        }

        // Today's nutrition (empty, ready for logging)
        NutritionLog::updateOrCreate(
            [
                'user_id' => $member->id,
                'date' => Carbon::today(),
            ],
            [
                'calories' => 0,
                'protein_grams' => 0,
                'carbs_grams' => 0,
                'fat_grams' => 0,
                'fiber_grams' => 0,
                'sugar_grams' => 0,
                'water_ml' => 0,
            ]
        );

        $this->command->info('Nutrition logs seeded successfully!');
    }
}
