<?php

namespace App\Listeners;

use App\Mail\MemberWelcomeMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{
    public function handle(Registered $event)
    {
        $user = $event->user;

        Mail::to($user->email)->send(new MemberWelcomeMail($user));
    }
}
