<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Member;
use App\Models\Instructor;
use Carbon\Carbon;

class PaymentsTableSeeder extends Seeder
{
    public function run()
    {
        Payment::truncate();
        $members = Member::all();
        $instructors = Instructor::all();

        if ($members->isEmpty()) return;

        // Create a few payments across the last 12 months
        for ($m = 0; $m < 12; $m++) {
            foreach ($members as $member) {
                $amount = rand(100000, 500000);
                Payment::create([
                    'member_id' => $member->id,
                    'instructor_id' => $instructors->random()->id ?? null,
                    'amount' => $amount,
                    'paid_at' => Carbon::now()->subMonths(rand(0,11))->subDays(rand(0,28)),
                ]);
            }
        }
    }
}
