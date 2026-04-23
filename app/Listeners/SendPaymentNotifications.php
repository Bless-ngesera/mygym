<?php
// app/Listeners/SendPaymentNotifications.php

namespace App\Listeners;

use App\Events\PaymentProcessed;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(PaymentProcessed $event): void
    {
        if ($event->status === 'success') {
            // Send success notification to user
            $this->notificationService->sendToUser($event->user, [
                'type' => 'payment_success',
                'title' => '✅ Payment Successful',
                'message' => "Your payment of UGX " . number_format($event->amount, 0) . " has been processed successfully.",
                'priority' => 'medium',
                'action_url' => route('receipts.show', $event->receiptId),
                'data' => [
                    'amount' => $event->amount,
                    'reference' => $event->reference,
                    'receipt_id' => $event->receiptId
                ]
            ]);

            // Notify admins for large payments
            if ($event->amount > 500000) {
                $this->notificationService->sendToAdmins([
                    'type' => 'large_payment',
                    'title' => '💰 Large Payment Received',
                    'message' => "{$event->user->name} made a payment of UGX " . number_format($event->amount, 0),
                    'priority' => 'medium',
                    'action_url' => route('admin.reports.financial'),
                    'data' => [
                        'user_id' => $event->user->id,
                        'amount' => $event->amount,
                        'reference' => $event->reference
                    ]
                ]);
            }
        } else {
            // Send failure notification to user
            $this->notificationService->paymentFailed($event->user, $event->amount);

            // Notify admins about payment failure
            $this->notificationService->sendToAdmins([
                'type' => 'payment_failed',
                'title' => '⚠️ Payment Failed',
                'message' => "{$event->user->name}'s payment of UGX " . number_format($event->amount, 0) . " failed. Reference: {$event->reference}",
                'priority' => 'high',
                'action_url' => route('admin.members.edit', $event->user->id),
                'data' => [
                    'user_id' => $event->user->id,
                    'amount' => $event->amount,
                    'reference' => $event->reference,
                    'error' => $event->error ?? 'Unknown error'
                ]
            ]);
        }
    }
}
