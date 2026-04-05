<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TomorrowClassReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⏰ Reminder: Your Class is Tomorrow - MyGym',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tomorrow-class-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
