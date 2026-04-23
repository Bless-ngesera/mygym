<?php
// app/Providers/EventServiceProvider.php

namespace App\Providers;

use App\Events\BookingCreated;
use App\Events\BookingCancelled;
use App\Events\ClassCancelled;
use App\Events\GoalAchieved;
use App\Events\InstructorAssigned;
use App\Events\MemberCheckedIn;
use App\Events\MemberRegistered;
use App\Events\NewMessageSent;
use App\Events\PaymentProcessed;
use App\Events\SubscriptionExpiring;
use App\Events\WorkoutCompleted;
use App\Listeners\SendBookingNotifications;
use App\Listeners\SendBookingCancellationNotifications;
use App\Listeners\SendClassCancelledNotifications;
use App\Listeners\SendGoalAchievedNotifications;
use App\Listeners\SendInstructorAssignmentNotifications;
use App\Listeners\SendCheckInNotifications;
use App\Listeners\SendMemberRegisteredNotifications;
use App\Listeners\SendNewMessageNotifications;
use App\Listeners\SendPaymentNotifications;
use App\Listeners\SendSubscriptionNotifications;
use App\Listeners\SendWorkoutNotifications;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Custom Events
        MemberCheckedIn::class => [
            SendCheckInNotifications::class,
        ],

        WorkoutCompleted::class => [
            SendWorkoutNotifications::class,
        ],

        BookingCreated::class => [
            SendBookingNotifications::class,
        ],

        BookingCancelled::class => [
            SendBookingCancellationNotifications::class,
        ],

        GoalAchieved::class => [
            SendGoalAchievedNotifications::class,
        ],

        SubscriptionExpiring::class => [
            SendSubscriptionNotifications::class,
        ],

        PaymentProcessed::class => [
            SendPaymentNotifications::class,
        ],

        MemberRegistered::class => [
            SendMemberRegisteredNotifications::class,
        ],

        InstructorAssigned::class => [
            SendInstructorAssignmentNotifications::class,
        ],

        ClassCancelled::class => [
            SendClassCancelledNotifications::class,
        ],

        NewMessageSent::class => [
            SendNewMessageNotifications::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
