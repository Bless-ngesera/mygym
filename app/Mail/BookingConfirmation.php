<?php

namespace App\Mail;

use App\Models\ScheduledClass;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $class;
    public $receipt;
    public $bookingDate;

    public function __construct(User $user, ScheduledClass $class, Receipt $receipt)
    {
        $this->user = $user;
        $this->class = $class;
        $this->receipt = $receipt;
        $this->bookingDate = now();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Booking Confirmed - MyGym',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
