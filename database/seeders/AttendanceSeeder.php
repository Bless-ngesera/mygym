<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        $member = User::where('role', 'member')->first();

        if (!$member) {
            $this->command->error('No member found!');
            return;
        }

        // Last 14 days attendance (70% attendance rate)
        for ($i = 0; $i < 14; $i++) {
            $date = Carbon::today()->subDays($i);

            // Skip some days
            if (rand(1, 100) > 70) continue;

            $checkIn = $date->setTime(rand(8, 11), rand(0, 59));
            $checkOut = (clone $checkIn)->addMinutes(rand(45, 90));

            Attendance::updateOrCreate(
                [
                    'user_id' => $member->id,
                    'check_in' => $checkIn,
                ],
                [
                    'check_out' => $checkOut,
                    'duration_minutes' => $checkIn->diffInMinutes($checkOut),
                    'status' => 'checked_out',
                ]
            );
        }

        // Today's active check-in
        Attendance::updateOrCreate(
            [
                'user_id' => $member->id,
                'check_in' => Carbon::today()->setTime(9, 0),
            ],
            [
                'check_out' => null,
                'duration_minutes' => null,
                'status' => 'checked_in',
            ]
        );

        $this->command->info('Attendance records seeded successfully!');
    }
}
