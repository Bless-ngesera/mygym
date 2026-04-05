<?php

namespace App\Console\Commands;

use App\Mail\TomorrowClassReminder;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class SendTomorrowReminders extends Command
{
    protected $signature = 'reminders:tomorrow';
    protected $description = 'Send reminders for classes happening tomorrow';

    public function handle(): int
    {
        $this->info('Sending tomorrow class reminders...');

        $startOfTomorrow = Carbon::tomorrow()->startOfDay();
        $endOfTomorrow = Carbon::tomorrow()->endOfDay();

        $bookings = Booking::with(['scheduledClass.classType', 'scheduledClass.instructor', 'user'])
            ->whereHas('scheduledClass', function($query) use ($startOfTomorrow, $endOfTomorrow) {
                $query->whereBetween('date_time', [$startOfTomorrow, $endOfTomorrow]);
            })
            ->get();

        $count = 0;
        $failed = 0;

        foreach ($bookings as $booking) {
            try {
                Mail::to($booking->user->email)->send(new TomorrowClassReminder($booking));
                $count++;
                $this->info("✓ Reminder sent to: {$booking->user->email}");
            } catch (\Exception $e) {
                $failed++;
                $this->error("✗ Failed to send to: {$booking->user->email} - {$e->getMessage()}");
            }
        }

        $this->info("✅ Sent {$count} reminders for tomorrow's classes.");

        if ($failed > 0) {
            $this->warn("⚠️ Failed to send {$failed} reminders.");
        }

        return SymfonyCommand::SUCCESS;
    }
}
