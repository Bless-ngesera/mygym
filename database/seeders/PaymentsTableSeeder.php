<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\User;
use App\Models\Instructor;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PaymentsTableSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks so truncate works
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Payment::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get only users with role = 'member'
        $members = User::where('role', 'member')->get();
        $instructors = Instructor::all();

        if ($members->isEmpty() || $instructors->isEmpty()) {
            return;
        }

        // Create payments across the last 12 months
        for ($m = 0; $m < 12; $m++) {
            foreach ($members as $member) {
                $amount = rand(100000, 500000);

                Payment::create([
                    'member_id'     => $member->id, // valid user with role=member
                    'instructor_id' => $instructors->random()->id,
                    'amount'        => $amount,
                    'paid_at'       => Carbon::now()->subMonths(rand(0, 11))->subDays(rand(0, 28)),
                    'reference'     => 'PAY-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                    'status'        => 'pending',
                ]);
            }
        }
    }
}
