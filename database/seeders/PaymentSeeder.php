<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MemberSubscription;
use App\Models\Payment;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    public function run()
    {
        $member = User::where('role', 'member')->first();
        $subscription = MemberSubscription::where('member_id', $member?->id)->first();

        if (!$member || !$subscription) {
            $this->command->error('No member or subscription found!');
            return;
        }

        $payments = [
            ['amount' => 150000, 'payment_method' => 'MTN Mobile Money', 'status' => 'completed', 'months_ago' => 2],
            ['amount' => 150000, 'payment_method' => 'Airtel Money', 'status' => 'completed', 'months_ago' => 1],
            ['amount' => 150000, 'payment_method' => 'Card', 'status' => 'completed', 'months_ago' => 0],
        ];

        foreach ($payments as $payment) {
            Payment::updateOrCreate(
                [
                    'member_id' => $member->id,
                    'transaction_id' => 'TXN-' . $payment['payment_method'] . '-' . $payment['months_ago'],
                ],
                [
                    'subscription_id' => $subscription->id,
                    'amount' => $payment['amount'],
                    'payment_method' => $payment['payment_method'],
                    'status' => $payment['status'],
                    'payment_date' => Carbon::today()->subMonths($payment['months_ago']),
                ]
            );
        }

        $this->command->info('Payments seeded successfully!');
    }
}
