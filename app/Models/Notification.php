<?php
// app/Models/Notification.php

namespace App\Models;

use App\Events\NotificationRead;
use App\Events\NotificationSent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'role',
        'type',
        'title',
        'message',
        'priority',
        'data',
        'action_url',
        'read',
        'read_at',
        'expires_at',
        'delivered_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
        'delivered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'priority_color',
        'priority_badge',
        'icon',
        'time_ago',
        'is_expired'
    ];

    // ==================== SCOPES ====================

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('read', true);
    }

    /**
     * Scope by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for critical notifications
     */
    public function scopeCritical($query)
    {
        return $query->where('priority', 'critical');
    }

    /**
     * Scope for high priority notifications
     */
    public function scopeHigh($query)
    {
        return $query->where('priority', 'high');
    }

    /**
     * Scope for not expired notifications
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope for expired notifications
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')->where('expires_at', '<=', now());
    }

    /**
     * Scope for today's notifications
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for this week's notifications
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope by role
     */
    public function scopeForRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // ==================== CORE METHODS ====================

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $wasUnread = !$this->read;

        $this->update([
            'read' => true,
            'read_at' => now()
        ]);

        // Dispatch event only if it was previously unread
        if ($wasUnread) {
            event(new NotificationRead($this));
        }
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): void
    {
        $this->update([
            'read' => false,
            'read_at' => null
        ]);
    }

    /**
     * Mark as delivered (for push/email tracking)
     */
    public function markAsDelivered(): void
    {
        $this->update(['delivered_at' => now()]);
    }

    /**
     * Dispatch notification sent event
     */
    public function dispatchSentEvent(): void
    {
        event(new NotificationSent($this));
    }

    /**
     * Check if notification is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && now()->gt($this->expires_at);
    }

    /**
     * Check if notification is read
     */
    public function isRead(): bool
    {
        return (bool) $this->read;
    }

    /**
     * Check if notification is delivered
     */
    public function isDelivered(): bool
    {
        return (bool) $this->delivered_at;
    }

    /**
     * Get time to read in seconds
     */
    public function getTimeToRead(): ?int
    {
        if (!$this->read_at || !$this->created_at) {
            return null;
        }

        return $this->created_at->diffInSeconds($this->read_at);
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Relationship with user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ==================== ACCESSORS & MUTATORS ====================

    /**
     * Get priority color for UI
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'critical' => 'red',
            'high' => 'orange',
            'medium' => 'blue',
            'low' => 'gray',
            default => 'indigo'
        };
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'critical' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'high' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            'medium' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'low' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            default => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200'
        };
    }

    /**
     * Get priority level text
     */
    public function getPriorityLevelAttribute(): string
    {
        return match($this->priority) {
            'critical' => 'Critical',
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low',
            default => 'Normal'
        };
    }

    /**
     * Get icon based on notification type
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'workout_reminder', 'workout_completed' => '💪',
            'booking_confirmed', 'new_booking' => '📅',
            'booking_cancelled' => '❌',
            'payment_success' => '💰',
            'payment_failed' => '⚠️',
            'subscription_expiring', 'subscription_expired' => '⏰',
            'achievement_unlocked', 'goal_achieved' => '🏆',
            'streak_milestone' => '🔥',
            'class_reminder' => '⏰',
            'daily_motivation' => '💡',
            'weekly_report' => '📊',
            'new_message' => '💬',
            'new_member' => '🎉',
            'class_cancelled' => '🚫',
            'instructor_assigned' => '👨‍🏫',
            'member_assigned' => '👥',
            'system_maintenance' => '🔧',
            'welcome' => '👋',
            'test' => '🧪',
            default => '🔔'
        };
    }

    /**
     * Get icon color class
     */
    public function getIconColorAttribute(): string
    {
        return match($this->priority) {
            'critical' => 'text-red-500',
            'high' => 'text-orange-500',
            'medium' => 'text-blue-500',
            default => 'text-indigo-500'
        };
    }

    /**
     * Get time ago string
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get formatted created date
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('M d, Y \a\t h:i A');
    }

    /**
     * Get formatted read date
     */
    public function getFormattedReadAtAttribute(): ?string
    {
        return $this->read_at?->format('M d, Y \a\t h:i A');
    }

    /**
     * Get is expired flag
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->isExpired();
    }

    /**
     * Get short message (for previews)
     */
    public function getShortMessageAttribute(): string
    {
        return strlen($this->message) > 100
            ? substr($this->message, 0, 100) . '...'
            : $this->message;
    }

    /**
     * Get action button text based on type
     */
    public function getActionButtonTextAttribute(): ?string
    {
        return match($this->type) {
            'workout_reminder', 'workout_completed' => 'View Workout',
            'booking_confirmed', 'booking_cancelled' => 'View Booking',
            'payment_success', 'payment_failed' => 'View Receipt',
            'subscription_expiring' => 'Renew Now',
            'achievement_unlocked', 'goal_achieved' => 'View Achievement',
            'class_reminder' => 'View Class',
            'weekly_report' => 'View Report',
            'new_message' => 'Read Message',
            default => 'View Details'
        };
    }

    // ==================== STATIC METHODS ====================

    /**
     * Get unread count for a user
     */
    public static function getUnreadCount(int $userId): int
    {
        return static::where('user_id', $userId)
            ->where('read', false)
            ->count();
    }

    /**
     * Get counts by priority for a user
     */
    public static function getPriorityCounts(int $userId): array
    {
        return [
            'critical' => static::where('user_id', $userId)->where('priority', 'critical')->where('read', false)->count(),
            'high' => static::where('user_id', $userId)->where('priority', 'high')->where('read', false)->count(),
            'medium' => static::where('user_id', $userId)->where('priority', 'medium')->where('read', false)->count(),
            'low' => static::where('user_id', $userId)->where('priority', 'low')->where('read', false)->count(),
        ];
    }

    /**
     * Get counts by type for a user
     */
    public static function getTypeCounts(int $userId): array
    {
        return static::where('user_id', $userId)
            ->where('read', false)
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }

    /**
     * Delete old notifications
     */
    public static function deleteOld(int $days = 30): int
    {
        return static::where('created_at', '<', now()->subDays($days))
            ->where('read', true)
            ->delete();
    }

    /**
     * Delete expired notifications
     */
    public static function deleteExpired(): int
    {
        return static::expired()->delete();
    }

    /**
     * Mark all as read for a user
     */
    public static function markAllAsRead(int $userId): int
    {
        $count = static::where('user_id', $userId)
            ->where('read', false)
            ->count();

        static::where('user_id', $userId)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now()
            ]);

        return $count;
    }

    /**
     * Get recent notifications for a user
     */
    public static function getRecent(int $userId, int $limit = 10)
    {
        return static::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    // ==================== BOOT METHODS ====================

    /**
     * Boot method to auto-set role based on user
     */
    protected static function booted()
    {
        static::creating(function ($notification) {
            if (!$notification->role && $notification->user) {
                $notification->role = $notification->user->role;
            }

            // Auto-set expiry if not set for certain types
            if (!$notification->expires_at) {
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
                ];

                $notification->expires_at = $expiryMap[$notification->type] ?? now()->addDays(90);
            }
        });

        static::created(function ($notification) {
            // Dispatch notification sent event
            $notification->dispatchSentEvent();
        });
    }
}
