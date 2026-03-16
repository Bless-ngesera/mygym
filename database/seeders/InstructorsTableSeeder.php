<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Instructor;

class InstructorsTableSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks so truncate works even if payments reference instructors
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Instructor::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $list = [
            [
                'name'       => 'Aisha Nakato',
                'email'      => 'aisha@example.com',
                'specialty'  => 'Yoga',
                'experience' => 4,
                'phone'      => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'John Kato',
                'email'      => 'john@example.com',
                'specialty'  => 'Strength',
                'experience' => 6,
                'phone'      => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Grace Tumusiime',
                'email'      => 'grace@example.com',
                'specialty'  => 'Cardio',
                'experience' => 3,
                'phone'      => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Instructor::insert($list);
    }
}
