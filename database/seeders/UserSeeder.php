<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Import the Hash facade

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define a consistent test password for named users
        $testPassword = Hash::make('password'); 

        // Bless (Default Member)
        User::factory()->create([
            'name' => 'Bless',
            'email' => 'bless@example.com',
            'password' => $testPassword, // Explicitly set password
        ]);

        
        // Sam (Default Member)
        User::factory()->create([
            'name' => 'Sam',
            'email' => 'sam@example.com',
            'password' => $testPassword, // Explicitly set password
        ]);

        
        // Eze (Instructor)
        User::factory()->create([
            'name' => 'Eze',
            'email' => 'eze@example.com',
            'role' => 'instructor',
            'password' => $testPassword, // Explicitly set password
        ]);

        
        // Admin
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => $testPassword, // Explicitly set password
        ]);


        // Create 10 random members (their password will be whatever the factory defines)
        User::factory()->count(10)->create();

        // Create 10 random instructors (their password will be whatever the factory defines)
        User::factory()->count(10)->create([
            'role' => 'instructor'
        ]);

        
    }
}
