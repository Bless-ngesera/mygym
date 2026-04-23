<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationSettings;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Send notification to a single user
     */
    public function sendToUser($user, array $data): ?Notification
    {
        // Get user's notification settings
        $settings = NotificationSettings::firstOrCreate(
            ['user_id' => $user->id],
            ['preferences' => NotificationSettings::getDefaults()]
        );

        // Check if user wants this type of notification
        if (!$settings->wantsNotification($data['type'])) {
            return null;
        }

        // Determine delivery channels based on priority
        $channels = $this->determineChannels($data['priority'], $settings);

        // Create notification record
        $notification = Notification::create([
            'user_id' => $user->id,
            'role' => $user->role ?? 'member',
            'type' => $data['type'],
            'title' => $data['title'],
            'message' => $data['message'],
            'priority' => $data['priority'],
            'data' => $data['data'] ?? null,
            'action_url' => $data['action_url'] ?? null,
            'read' => false,
            'expires_at' => $data['expires_at'] ?? $this->getExpiryDate($data['type']),
        ]);

        // Send through selected channels (async for better performance)
        if (!empty($channels)) {
            dispatch(function () use ($notification, $user, $channels) {
                $this->dispatchThroughChannels($notification, $user, $channels);
            })->afterResponse();
        }

        return $notification;
    }

    /**
     * Send notification to multiple users
     */
    public function sendToMany($users, array $data): array
    {
        $notifications = [];

        foreach ($users as $user) {
            $notif = $this->sendToUser($user, $data);
            if ($notif) {
                $notifications[] = $notif;
            }
        }

        return $notifications;
    }

    /**
     * Send notification to all users with a specific role
     */
    public function sendToRole(string $role, array $data): array
    {
        $users = User::where('role', $role)->get();
        return $this->sendToMany($users, $data);
    }

    /**
     * Send notification to all admins
     */
    public function sendToAdmins(array $data): array
    {
        return $this->sendToRole('admin', $data);
    }

    /**
     * Send notification to all instructors
     */
    public function sendToInstructors(array $data): array
    {
        return $this->sendToRole('instructor', $data);
    }

    /**
     * Send notification to all members
     */
    public function sendToMembers(array $data): array
    {
        return $this->sendToRole('member', $data);
    }

    /**
     * Send notification to members of a specific instructor
     */
    public function sendToInstructorMembers($instructorId, array $data): array
    {
        $members = User::where('role', 'member')
            ->where('instructor_id', $instructorId)
            ->get();

        return $this->sendToMany($members, $data);
    }

    /**
     * Send workout reminder notification
     */
    public function workoutReminder($user, $workout): ?Notification
    {
        return $this->sendToUser($user, [
            'type' => 'workout_reminder',
            'title' => '🏋️ Workout Reminder',
            'message' => "Your workout '{$workout->title}' is scheduled in 2 hours. Get ready!",
            'priority' => 'high',
            'action_url' => route('member.workouts.details', $workout->id),
            'data' => ['workout_id' => $workout->id]
        ]);
    }

    /**
     * Send booking confirmation notification
     */
    public function bookingConfirmed($user, $booking): ?Notification
    {
        $class = $booking->class;

        return $this->sendToUser($user, [
            'type' => 'booking_confirmed',
            'title' => '📅 Booking Confirmed!',
            'message' => "You're booked for {$class->name} on " . Carbon::parse($class->date_time)->format('M d, h:i A'),
            'priority' => 'medium',
            'action_url' => route('member.bookings.show', $booking->id),
            'data' => ['booking_id' => $booking->id, 'class_id' => $class->id]
        ]);
    }

    /**
     * Send new booking notification to instructor
     */
    public function newBookingForInstructor($instructor, $member, $class): ?Notification
    {
        return $this->sendToUser($instructor, [
            'type' => 'new_booking',
            'title' => '🎯 New Class Booking!',
            'message' => "{$member->name} just booked your {$class->name} class",
            'priority' => 'high',
            'action_url' => route('instructor.schedule.show', $class->id),
            'data' => ['class_id' => $class->id, 'member_id' => $member->id]
        ]);
    }

    /**
     * Send subscription expiring notification
     */
    public function subscriptionExpiring($user, $daysLeft): ?Notification
    {
        $priority = $daysLeft <= 3 ? 'critical' : ($daysLeft <= 7 ? 'high' : 'medium');

        return $this->sendToUser($user, [
            'type' => 'subscription_expiring',
            'title' => "⚠️ Membership Expires in {$daysLeft} Days",
            'message' => "Your plan expires in {$daysLeft} days. Renew now to keep your benefits!",
            'priority' => $priority,
            'action_url' => route('plans.index'),
            'data' => ['days_left' => $daysLeft],
            'expires_at' => now()->addDays($daysLeft)
        ]);
    }

    /**
     * Send payment failed notification
     */
    public function paymentFailed($user, $amount): ?Notification
    {
        return $this->sendToUser($user, [
            'type' => 'payment_failed',
            'title' => '❌ Payment Failed',
            'message' => "Your payment of UGX " . number_format($amount, 0) . " failed. Please contact support.",
            'priority' => 'critical',
            'action_url' => route('plans.index'),
            'data' => ['amount' => $amount]
        ]);
    }

    /**
     * Send achievement unlocked notification
     */
    public function achievementUnlocked($user, $achievement): ?Notification
    {
        return $this->sendToUser($user, [
            'type' => 'achievement_unlocked',
            'title' => '🏆 Achievement Unlocked!',
            'message' => "Congratulations! You've earned the '{$achievement->title}' achievement.",
            'priority' => 'high',
            'action_url' => route('member.dashboard'),
            'data' => ['achievement_id' => $achievement->id]
        ]);
    }

    /**
     * Send streak milestone notification
     */
    public function streakMilestone($user, $streakCount): ?Notification
    {
        return $this->sendToUser($user, [
            'type' => 'streak_milestone',
            'title' => "🔥 {$streakCount}-Day Streak!",
            'message' => "Incredible consistency! You've maintained a {$streakCount}-day check-in streak.",
            'priority' => 'medium',
            'action_url' => route('member.dashboard'),
            'data' => ['streak_count' => $streakCount]
        ]);
    }

    /**
     * Send class reminder
     */
    public function classReminder($user, $class, $hoursBefore): ?Notification
    {
        $timeText = $hoursBefore == 24 ? 'tomorrow' : "in {$hoursBefore} hours";

        return $this->sendToUser($user, [
            'type' => 'class_reminder',
            'title' => $hoursBefore == 24 ? '📅 Class Tomorrow!' : '⏰ Class Starting Soon!',
            'message' => "Reminder: {$class->name} with {$class->instructor->name} starts {$timeText} at " . Carbon::parse($class->date_time)->format('h:i A'),
            'priority' => $hoursBefore == 1 ? 'critical' : 'high',
            'action_url' => route('member.bookings.show', $class->id),
            'data' => ['class_id' => $class->id, 'hours_before' => $hoursBefore]
        ]);
    }

    /**
     * Send weekly progress report
     */
    public function weeklyReport($user, $workoutsCompleted, $checkins): ?Notification
    {
        return $this->sendToUser($user, [
            'type' => 'weekly_report',
            'title' => '📊 Your Weekly Progress',
            'message' => "This week: {$workoutsCompleted} workouts completed, {$checkins} check-ins. Keep going!",
            'priority' => 'low',
            'action_url' => route('member.workouts.history'),
            'data' => [
                'workouts_completed' => $workoutsCompleted,
                'checkins' => $checkins
            ]
        ]);
    }

    /**
     * Send daily motivation
     */
    public function dailyMotivation($user): ?Notification
    {
        $quotes = [
            "The only bad workout is the one that didn't happen.",
            "Your body can stand almost anything. It's your mind you have to convince.",
            "Don't limit your challenges. Challenge your limits.",
            "Success starts with self-discipline.",
            "Make your sweat your masterpiece.",
            "Small progress is still progress.",
            "You didn't come this far to only come this far.",
        ];

        $quote = $quotes[array_rand($quotes)];

        return $this->sendToUser($user, [
            'type' => 'daily_motivation',
            'title' => '💪 Daily Motivation',
            'message' => $quote,
            'priority' => 'low',
            'action_url' => route('member.dashboard'),
        ]);
    }

    /**
     * Send new member notification to admins
     */
    public function newMemberRegistered($member): array
    {
        return $this->sendToAdmins([
            'type' => 'new_member',
            'title' => '🎉 New Member Joined!',
            'message' => "{$member->name} has joined as a new member",
            'priority' => 'high',
            'action_url' => route('admin.members.edit', $member->id),
            'data' => ['member_id' => $member->id]
        ]);
    }

    /**
     * Send class capacity alert to admins
     */
    public function classCapacityAlert($class, $currentBookings, $capacity): array
    {
        $percentage = round(($currentBookings / $capacity) * 100);

        return $this->sendToAdmins([
            'type' => 'class_capacity_alert',
            'title' => '⚠️ Class Almost Full!',
            'message' => "{$class->name} is at {$percentage}% capacity ({$currentBookings}/{$capacity})",
            'priority' => 'medium',
            'action_url' => route('admin.reports.classes'),
            'data' => [
                'class_id' => $class->id,
                'current_bookings' => $currentBookings,
                'capacity' => $capacity,
                'percentage' => $percentage
            ]
        ]);
    }

    /**
     * Send workout completed notification
     */
    public function workoutCompleted($user, $workout): ?Notification
    {
        return $this->sendToUser($user, [
            'type' => 'workout_completed',
            'title' => '💪 Workout Complete!',
            'message' => "Great job completing '{$workout->title}'! Keep crushing your goals.",
            'priority' => 'low',
            'action_url' => route('member.workouts.history'),
            'data' => ['workout_id' => $workout->id]
        ]);
    }

    /**
     * Send goal achieved notification
     */
    public function goalAchieved($user, $goal): ?Notification
    {
        return $this->sendToUser($user, [
            'type' => 'goal_achieved',
            'title' => '🎯 Goal Achieved!',
            'message' => "Congratulations! You've achieved your goal: {$goal->title}",
            'priority' => 'high',
            'action_url' => route('member.dashboard'),
            'data' => ['goal_id' => $goal->id]
        ]);
    }

    /**
     * Send check-in confirmation
     */
    public function checkInConfirmation($user, $streakCount): ?Notification
    {
        return $this->sendToUser($user, [
            'type' => 'checkin_success',
            'title' => '✅ Checked In!',
            'message' => "You've checked in for today! Current streak: {$streakCount} days",
            'priority' => 'low',
            'action_url' => route('member.dashboard'),
            'data' => ['streak_count' => $streakCount]
        ]);
    }

    /**
     * Send new message notification - FIXED VERSION
     */
    public function newMessage($user, $sender, $message): ?Notification
    {
        // Determine the correct route based on user role
        $actionUrl = null;

        if ($user->role === 'instructor') {
            // Instructor route from your routes file
            $actionUrl = route('instructor.messages.conversation', $sender->id);
        } elseif ($user->role === 'member') {
            // Member route from your routes file
            $actionUrl = route('member.messages.get', $sender->id);
        } else {
            $actionUrl = route('member.dashboard');
        }

        return $this->sendToUser($user, [
            'type' => 'new_message',
            'title' => '💬 New Message',
            'message' => "{$sender->name} sent you a message: " . substr($message, 0, 100),
            'priority' => 'high',
            'action_url' => $actionUrl,
            'data' => ['sender_id' => $sender->id, 'message_preview' => substr($message, 0, 100)]
        ]);
    }

    /**
     * Send instructor assigned notification to member
     */
    public function instructorAssigned($member, $instructor): ?Notification
    {
        return $this->sendToUser($member, [
            'type' => 'instructor_assigned',
            'title' => '👨‍🏫 Instructor Assigned',
            'message' => "You have been assigned to instructor {$instructor->name}. They will guide your fitness journey!",
            'priority' => 'high',
            'action_url' => route('member.messages.get', $instructor->id),
            'data' => ['instructor_id' => $instructor->id]
        ]);
    }

    /**
     * Send new member assigned notification to instructor
     */
    public function memberAssignedToInstructor($instructor, $member): ?Notification
    {
        return $this->sendToUser($instructor, [
            'type' => 'member_assigned',
            'title' => '👤 New Member Assigned',
            'message' => "{$member->name} has been assigned to you as a new member.",
            'priority' => 'high',
            'action_url' => route('instructor.members.index'),
            'data' => ['member_id' => $member->id]
        ]);
    }

    /**
     * Send class cancellation notification
     */
    public function classCancelled($user, $class, $cancelledBy): ?Notification
    {
        return $this->sendToUser($user, [
            'type' => 'class_cancelled',
            'title' => '❌ Class Cancelled',
            'message' => "The {$class->name} class scheduled for " . Carbon::parse($class->date_time)->format('M d, h:i A') . " has been cancelled by {$cancelledBy}.",
            'priority' => 'high',
            'action_url' => route('member.classes.index'),
            'data' => ['class_id' => $class->id]
        ]);
    }

    /**
     * Send system maintenance notification to all users
     */
    public function systemMaintenance($message, $startTime, $endTime): array
    {
        $allUsers = User::all();
        $notifications = [];

        foreach ($allUsers as $user) {
            $notif = $this->sendToUser($user, [
                'type' => 'system_maintenance',
                'title' => '🔧 System Maintenance',
                'message' => $message . " from " . Carbon::parse($startTime)->format('M d, h:i A') . " to " . Carbon::parse($endTime)->format('M d, h:i A'),
                'priority' => 'critical',
                'action_url' => route('member.dashboard'),
                'data' => ['start_time' => $startTime, 'end_time' => $endTime],
                'expires_at' => Carbon::parse($endTime)->addDays(1)
            ]);

            if ($notif) {
                $notifications[] = $notif;
            }
        }

        return $notifications;
    }

    /**
     * Determine delivery channels based on priority
     */
    private function determineChannels(string $priority, NotificationSettings $settings): array
    {
        $channels = ['in_app']; // Always store in database

        // For critical and high priority, also send push and email
        if ($priority === 'critical' || $priority === 'high') {
            if ($settings->push_enabled) {
                $channels[] = 'push';
            }
            if ($settings->email_enabled) {
                $channels[] = 'email';
            }
        } elseif ($priority === 'medium' && $settings->push_enabled) {
            $channels[] = 'push';
        }

        return $channels;
    }

    /**
     * Dispatch notification through selected channels
     */
    private function dispatchThroughChannels(Notification $notification, $user, array $channels): void
    {
        foreach ($channels as $channel) {
            switch ($channel) {
                case 'push':
                    $this->sendPushNotification($user, $notification);
                    break;
                case 'email':
                    $this->sendEmailNotification($user, $notification);
                    break;
                case 'in_app':
                    // Already saved in database
                    break;
            }
        }

        // Mark as delivered if any external channel was used
        if (count(array_diff($channels, ['in_app'])) > 0) {
            $notification->markAsDelivered();
        }
    }

    /**
     * Send push notification (placeholder - implement with your push service)
     */
    private function sendPushNotification($user, Notification $notification): void
    {
        // TODO: Implement with OneSignal, Pusher, or Laravel WebSockets
        Log::info("Push notification to user {$user->id}: {$notification->title}");

        // Example with Laravel WebSockets:
        // broadcast(new \App\Events\NewNotification($user->id, $notification));
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification($user, Notification $notification): void
    {
        try {
            Mail::send('emails.notification', [
                'user' => $user,
                'notification' => $notification
            ], function ($message) use ($user, $notification) {
                $message->to($user->email)
                        ->subject($notification->title);
            });
        } catch (\Exception $e) {
            Log::error("Failed to send email notification: " . $e->getMessage());
        }
    }

    /**
     * Get expiry date based on notification type
     */
    private function getExpiryDate(string $type): ?Carbon
    {
        $expiryMap = [
            'workout_reminder' => now()->addDays(1),
            'class_reminder' => now()->addDays(1),
            'payment_failed' => now()->addDays(7),
            'subscription_expiring' => now()->addDays(30),
            'booking_confirmed' => now()->addDays(60),
            'checkin_success' => now()->addDays(1),
            'workout_completed' => now()->addDays(7),
            'goal_achieved' => now()->addDays(90),
            'achievement_unlocked' => now()->addDays(90),
            'new_message' => now()->addDays(7),
            'instructor_assigned' => now()->addDays(30),
            'member_assigned' => now()->addDays(30),
            'class_cancelled' => now()->addDays(7),
            'system_maintenance' => now()->addDays(1),
        ];

        return $expiryMap[$type] ?? now()->addDays(90);
    }
}
