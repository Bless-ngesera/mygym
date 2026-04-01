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
            UserSeeder::class,          // ← add
            ClassTypeSeeder::class,     // ← add
            PlansTableSeeder::class,
            InstructorsTableSeeder::class,
            MembersTableSeeder::class,
            PaymentsTableSeeder::class,
            ScheduledClassSeeder::class, // ← add (after classes it depends on)
        ]);
    }
}
