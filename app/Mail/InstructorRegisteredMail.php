<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class InstructorRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    public $instructor;

    public function __construct(User $instructor)
    {
        $this->instructor = $instructor;
    }

    public function build()
    {
        return $this->subject('Welcome to MyGym as an Instructor')
                    ->view('emails.instructor_registered');
    }
}
