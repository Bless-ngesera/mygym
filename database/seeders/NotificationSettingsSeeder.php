<?php
// database/seeders/NotificationSettingsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\NotificationSettings;

class NotificationSettingsSeeder extends Seeder
{
    public function run()
    {
        // Create default notification settings for all users who don't have them
        $users = User::all();

        foreach ($users as $user) {
            NotificationSettings::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'push_enabled' => true,
                    'email_enabled' => true,
                    'in_app_enabled' => true,
                    'preferences' => NotificationSettings::getDefaults(),
                ]
            );
        }

        $this->command->info('Notification settings created for ' . $users->count() . ' users');
    }
}
