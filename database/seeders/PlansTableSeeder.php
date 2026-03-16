<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Plan;

class PlansTableSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks so truncate works
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Plan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert seed data
        Plan::insert([
            [
                'name'       => 'Basic',
                'price'      => 200000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Standard',
                'price'      => 350000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Premium',
                'price'      => 500000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
