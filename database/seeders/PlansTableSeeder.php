<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlansTableSeeder extends Seeder
{
    public function run()
    {
        Plan::truncate();
        Plan::insert([
            ['name'=>'Basic','price'=>200000,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Standard','price'=>350000,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Premium','price'=>500000,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }
}
