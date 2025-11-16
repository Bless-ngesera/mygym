<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\Plan;

class MembersTableSeeder extends Seeder
{
    public function run()
    {
        Member::truncate();
        $plans = Plan::pluck('id')->all();
        $names = ['Sam','Lilian','Peter','Mary','David','Esther','Kevin'];
        foreach ($names as $i => $n) {
            Member::create([
                'name' => $n,
                'email' => strtolower($n).'@example.com',
                'plan_id' => $plans[$i % count($plans)] ?? null,
                'status' => 'active',
            ]);
        }
    }
}
