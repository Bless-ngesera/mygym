<?php

namespace App\Mail;

use App\Models\ScheduledClass;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancellation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $class;

    public function __construct(User $user, ScheduledClass $class)
    {
        $this->user = $user;
        $this->class = $class;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '❌ Booking Cancelled - MyGym',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-cancellation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
