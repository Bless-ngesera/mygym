<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ClassType;

class ClassTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classTypes = [
            [
                'name' => 'Yoga',
                'description' => 'Relaxing and stretching exercises to improve flexibility and reduce stress. Perfect for all fitness levels.',
                'minutes' => 60
            ],
            [
                'name' => 'Dance Fitness',
                'description' => 'High-energy dance workout that combines fun music with easy-to-follow moves. Burn calories while having fun!',
                'minutes' => 45
            ],
            [
                'name' => 'Pilates',
                'description' => 'Core-strengthening exercises that improve posture, balance, and flexibility. Low-impact but highly effective.',
                'minutes' => 60
            ],
            [
                'name' => 'Boxing',
                'description' => 'High-intensity cardio workout combining boxing techniques with strength training. Build endurance and release stress.',
                'minutes' => 50
            ],
            [
                'name' => 'Zumba',
                'description' => 'Latin-inspired dance fitness that feels like a party while burning calories. Fun and energetic!',
                'minutes' => 55
            ],
            [
                'name' => 'Spin',
                'description' => 'Intense indoor cycling workout that builds endurance and burns calories. Adjustable resistance for all levels.',
                'minutes' => 45
            ],
            [
                'name' => 'HIIT',
                'description' => 'High-Intensity Interval Training combining short bursts of intense exercise with recovery periods.',
                'minutes' => 30
            ],
            [
                'name' => 'Strength Training',
                'description' => 'Weight lifting and resistance exercises to build muscle, increase metabolism, and improve bone density.',
                'minutes' => 60
            ],
            [
                'name' => 'CrossFit',
                'description' => 'Functional fitness combining elements of cardio, weightlifting, and gymnastics. Challenging and rewarding.',
                'minutes' => 60
            ],
            [
                'name' => 'Meditation',
                'description' => 'Mindfulness and relaxation techniques to reduce stress and improve mental clarity. Perfect for recovery days.',
                'minutes' => 30
            ],
        ];

        foreach ($classTypes as $type) {
            ClassType::create($type);
        }
    }
}
