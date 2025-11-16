<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Instructor;

class InstructorsTableSeeder extends Seeder
{
    public function run()
    {
        Instructor::truncate();
        $list = [
            ['name'=>'Aisha Nakato','email'=>'aisha@example.com','specialty'=>'Yoga','experience'=>4],
            ['name'=>'John Kato','email'=>'john@example.com','specialty'=>'Strength','experience'=>6],
            ['name'=>'Grace Tumusiime','email'=>'grace@example.com','specialty'=>'Cardio','experience'=>3],
        ];
        foreach ($list as $row) {
            Instructor::create(array_merge($row, ['phone'=>null]));
        }
    }
}
