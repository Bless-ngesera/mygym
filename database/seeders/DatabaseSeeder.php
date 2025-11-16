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
            PlansTableSeeder::class,
            InstructorsTableSeeder::class,
            MembersTableSeeder::class,
            PaymentsTableSeeder::class,
        ]);
    }
}
