<?php
// app/Traits/DispatchesNotifications.php

namespace App\Traits;

use App\Events\BookingCreated;
use App\Events\BookingCancelled;
use App\Events\GoalAchieved;
use App\Events\MemberCheckedIn;
use App\Events\NewMessageSent;
use App\Events\WorkoutCompleted;
use App\Models\Booking;
use App\Models\Goal;
use App\Models\Message;
use App\Models\User;
use App\Models\Workout;

trait DispatchesNotifications
{
    /**
     * Dispatch check-in notification
     */
    protected function dispatchCheckIn(User $member, int $streakCount): void
    {
        event(new MemberCheckedIn($member, $streakCount));
    }

    /**
     * Dispatch workout completion notification
     */
    protected function dispatchWorkoutCompleted(User $user, Workout $workout): void
    {
        event(new WorkoutCompleted($user, $workout));
    }

    /**
     * Dispatch booking created notification
     */
    protected function dispatchBookingCreated(Booking $booking): void
    {
        event(new BookingCreated($booking));
    }

    /**
     * Dispatch booking cancelled notification
     */
    protected function dispatchBookingCancelled(Booking $booking, string $cancelledBy): void
    {
        event(new BookingCancelled($booking, $cancelledBy));
    }

    /**
     * Dispatch goal achieved notification
     */
    protected function dispatchGoalAchieved(User $user, Goal $goal): void
    {
        event(new GoalAchieved($user, $goal));
    }

    /**
     * Dispatch new message notification
     */
    protected function dispatchNewMessage(Message $message, User $sender, User $receiver): void
    {
        event(new NewMessageSent($message, $sender, $receiver));
    }
}
