<?php

namespace App\Console\Commands;

use App\Mail\ClassReminder;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class SendClassReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send class reminders to members for classes starting in 2 hours';

    public function handle(): int
    {
        $this->info('Sending class reminders...');

        // Get bookings for classes starting in 2 hours (± 30 minutes)
        $startTime = Carbon::now()->addHours(2);
        $endTime = Carbon::now()->addHours(3);

        $bookings = Booking::with(['scheduledClass.classType', 'scheduledClass.instructor', 'user'])
            ->whereHas('scheduledClass', function($query) use ($startTime, $endTime) {
                $query->whereBetween('date_time', [$startTime, $endTime]);
            })
            ->get();

        $count = 0;
        $failed = 0;

        if ($bookings->isEmpty()) {
            $this->info('No classes found starting in the next 2-3 hours.');
        }

        foreach ($bookings as $booking) {
            try {
                Mail::to($booking->user->email)->send(new ClassReminder($booking));
                $count++;
                $this->info("✓ Reminder sent to: {$booking->user->email} for class at {$booking->scheduledClass->date_time->format('g:i A')}");
            } catch (\Exception $e) {
                $failed++;
                $this->error("✗ Failed to send to: {$booking->user->email} - {$e->getMessage()}");
            }
        }

        $this->info("✅ Sent {$count} reminders.");

        if ($failed > 0) {
            $this->warn("⚠️ Failed to send {$failed} reminders.");
        }

        return SymfonyCommand::SUCCESS;
    }
}
