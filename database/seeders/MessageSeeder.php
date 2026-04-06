<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Message;
use Carbon\Carbon;

class MessageSeeder extends Seeder
{
    public function run()
    {
        $member = User::where('role', 'member')->first();
        $instructor = User::where('role', 'instructor')->first();

        if (!$member || !$instructor) {
            $this->command->error('No member or instructor found!');
            return;
        }

        $messages = [
            ['sender_id' => $member->id, 'receiver_id' => $instructor->id, 'message' => 'Hi Instructor, I need help with my workout plan', 'hours_ago' => 48, 'read' => true],
            ['sender_id' => $instructor->id, 'receiver_id' => $member->id, 'message' => 'Sure! Let me review your progress and get back to you.', 'hours_ago' => 24, 'read' => true],
            ['sender_id' => $member->id, 'receiver_id' => $instructor->id, 'message' => 'Thanks! I want to focus on upper body strength.', 'hours_ago' => 12, 'read' => true],
            ['sender_id' => $instructor->id, 'receiver_id' => $member->id, 'message' => 'Great! I will send you a customized plan tomorrow.', 'hours_ago' => 6, 'read' => false],
        ];

        foreach ($messages as $message) {
            Message::updateOrCreate(
                [
                    'sender_id' => $message['sender_id'],
                    'receiver_id' => $message['receiver_id'],
                    'message' => $message['message'],
                ],
                [
                    'read' => $message['read'],
                    'read_at' => $message['read'] ? Carbon::now()->subHours($message['hours_ago'] - 1) : null,
                    'created_at' => Carbon::now()->subHours($message['hours_ago']),
                ]
            );
        }

        $this->command->info('Messages seeded successfully!');
    }
}
