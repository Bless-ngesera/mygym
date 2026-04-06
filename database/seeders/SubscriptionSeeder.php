<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MemberSubscription;
use Carbon\Carbon;

class SubscriptionSeeder extends Seeder
{
    public function run()
    {
        $member = User::where('role', 'member')->first();

        if (!$member) {
            $this->command->error('No member found!');
            return;
        }

        MemberSubscription::updateOrCreate(
            [
                'member_id' => $member->id,
            ],
            [
                'plan_name' => 'Premium Monthly',
                'description' => 'Access to all classes and facilities',
                'price' => 150000,
                'start_date' => Carbon::today()->subDays(15),
                'end_date' => Carbon::today()->addDays(15),
                'status' => 'active',
                'billing_cycle' => 'monthly',
                'payment_method' => 'MTN Mobile Money',
            ]
        );

        $this->command->info('Subscription seeded successfully!');
    }
}
