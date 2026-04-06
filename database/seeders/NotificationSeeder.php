<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notification;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $member = User::where('role', 'member')->first();

        if (!$member) {
            $this->command->error('No member found!');
            return;
        }

        $notifications = [
            ['type' => 'workout', 'title' => 'Workout Completed!', 'message' => 'Great job completing your upper body workout!', 'hours_ago' => 2],
            ['type' => 'attendance', 'title' => 'Checked In', 'message' => 'You have successfully checked in to the gym.', 'hours_ago' => 5],
            ['type' => 'payment', 'title' => 'Payment Received', 'message' => 'Your subscription payment has been processed.', 'hours_ago' => 24],
            ['type' => 'goal', 'title' => 'Goal Progress', 'message' => "You're 60% towards your weight loss goal!", 'hours_ago' => 48],
            ['type' => 'message', 'title' => 'New Message', 'message' => 'Your instructor sent you a message.', 'hours_ago' => 12],
            ['type' => 'workout', 'title' => 'Upcoming Workout', 'message' => 'You have a leg day workout tomorrow at 9 AM!', 'hours_ago' => 36],
            ['type' => 'attendance', 'title' => 'Check-in Reminder', 'message' => 'Don\'t forget to check in for your workout today!', 'hours_ago' => 8],
        ];

        foreach ($notifications as $notif) {
            Notification::updateOrCreate(
                [
                    'user_id' => $member->id,
                    'title' => $notif['title'],
                ],
                [
                    'type' => $notif['type'],
                    'message' => $notif['message'],
                    'read' => $notif['hours_ago'] > 10 ? true : false,
                    'created_at' => Carbon::now()->subHours($notif['hours_ago']),
                ]
            );
        }

        $this->command->info('Notifications seeded successfully!');
    }
}
