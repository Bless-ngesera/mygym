<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Instructor;
use App\Models\ClassType;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScheduledClass>
 */
class ScheduledClassFactory extends Factory
{
    public function definition(): array
    {
        return [
            'instructor_id' => Instructor::inRandomOrder()->first()?->id
                               ?? Instructor::factory()->create()->id,
            'class_type_id' => ClassType::inRandomOrder()->first()?->id
                               ?? ClassType::factory()->create()->id,
            'date_time'     => $this->faker->unique()->dateTimeBetween('+1 day', '+60 days')
                                    ->setTime(
                                        $this->faker->randomElement([6,8,10,12,14,16,18,20]),
                                        0, 0
                                    ),
            'price'         => $this->faker->randomElement([10, 15, 20, 25, 30]),
        ];
    }
}
