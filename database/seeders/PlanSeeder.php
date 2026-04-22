<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // Check if plans already exist
        if (Plan::count() > 0) {
            $this->command->info('Plans already exist. Skipping seeder.');
            return;
        }

        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Perfect for beginners starting their fitness journey',
                'price' => 50000,
                'currency' => 'UGX',
                'billing_period' => 'monthly',
                'duration_days' => 30,
                'features' => json_encode([
                    '🏋️ Access to gym facilities (6 AM - 6 PM)',
                    '📅 2 Group classes per week',
                    '📱 Basic workout tracking',
                    '📧 Email support (48hr response)',
                    '🔒 Locker room access',
                    '💧 Free drinking water',
                    '📊 Monthly progress check-in'
                ]),
                'max_classes_per_week' => 2,
                'has_personal_trainer' => false,
                'is_popular' => false,
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Standard',
                'slug' => 'standard',
                'description' => 'Most popular choice for regular gym-goers who want more flexibility',
                'price' => 100000,
                'currency' => 'UGX',
                'billing_period' => 'monthly',
                'duration_days' => 30,
                'features' => json_encode([
                    '🏋️ 24/7 Access to gym facilities',
                    '📅 5 Group classes per week (Zumba, Yoga, HIIT)',
                    '📱 Advanced workout tracking with analytics',
                    '💬 Priority email & WhatsApp support (12hr response)',
                    '🔒 Premium locker room access',
                    '💧 Free water & protein shake after workout',
                    '📊 Bi-weekly progress reports',
                    '🥗 Basic nutrition guidance',
                    '👥 Access to member community events',
                    '🎯 Goal setting workshop monthly'
                ]),
                'max_classes_per_week' => 5,
                'has_personal_trainer' => false,
                'is_popular' => true,
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Ultimate fitness experience with dedicated personal trainer',
                'price' => 200000,
                'currency' => 'UGX',
                'billing_period' => 'monthly',
                'duration_days' => 30,
                'features' => json_encode([
                    '🏋️ 24/7 VIP Access to gym facilities',
                    '🎓 Dedicated personal trainer (1-on-1 sessions)',
                    '🏆 Unlimited group classes',
                    '📱 Premium workout tracking with AI insights',
                    '💬 24/7 Priority support (2hr response)',
                    '🔒 VIP Locker room with sauna access',
                    '💧 Free water, protein shakes & pre-workout',
                    '📊 Weekly personalized progress reports',
                    '🥗 Custom nutrition plan by expert',
                    '👑 Access to exclusive member events',
                    '🎯 Monthly goal review with trainer',
                    '🧘 Free yoga & meditation classes',
                    '💪 Body composition analysis monthly',
                    '🎁 Free MyGym merchandise pack',
                    '👥 Bring a friend free every Friday'
                ]),
                'max_classes_per_week' => null,
                'has_personal_trainer' => true,
                'is_popular' => false,
                'sort_order' => 3,
                'is_active' => true
            ]
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        $this->command->info('Plans seeded successfully with distinct features!');
    }
}
