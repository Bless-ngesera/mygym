<?php
// app/Models/NotificationSettings.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSettings extends Model
{
    protected $table = 'notification_settings';

    protected $fillable = [
        'user_id', 'push_enabled', 'email_enabled', 'in_app_enabled', 'preferences'
    ];

    protected $casts = [
        'push_enabled' => 'boolean',
        'email_enabled' => 'boolean',
        'in_app_enabled' => 'boolean',
        'preferences' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getDefaults(): array
    {
        return [
            'workout_reminders' => true,
            'booking_updates' => true,
            'payment_alerts' => true,
            'promotions' => false,
            'achievements' => true,
            'streak_alerts' => true,
            'weekly_reports' => true,
            'daily_motivation' => true,
        ];
    }

    // Check if user wants specific notification type
    public function wantsNotification(string $type): bool
    {
        $preferences = $this->preferences ?? self::getDefaults();

        $typeMapping = [
            'workout_reminder' => 'workout_reminders',
            'workout_completed' => 'workout_reminders',
            'booking_confirmed' => 'booking_updates',
            'booking_cancelled' => 'booking_updates',
            'new_booking' => 'booking_updates',
            'payment_success' => 'payment_alerts',
            'payment_failed' => 'payment_alerts',
            'subscription_expiring' => 'payment_alerts',
            'achievement_unlocked' => 'achievements',
            'goal_achieved' => 'achievements',
            'streak_milestone' => 'streak_alerts',
            'weekly_report' => 'weekly_reports',
            'daily_motivation' => 'daily_motivation',
        ];

        $prefKey = $typeMapping[$type] ?? 'all';

        return $preferences[$prefKey] ?? true;
    }

    // Get channel settings as array
    public function getEnabledChannels(): array
    {
        $channels = [];

        if ($this->in_app_enabled) {
            $channels[] = 'in_app';
        }
        if ($this->push_enabled) {
            $channels[] = 'push';
        }
        if ($this->email_enabled) {
            $channels[] = 'email';
        }

        return $channels;
    }
}
