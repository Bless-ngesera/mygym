<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'instructor_id',
        'notification_email',
        'email_frequency',
        'language',
        'timezone',
        'theme',
        'last_login_at',
        'last_login_ip',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'emergency_contact_name',
        'emergency_contact_phone',
        'medical_conditions',
        'profile_photo_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'notification_email' => 'boolean',
            'last_login_at'      => 'datetime',
            'date_of_birth'      => 'date',
        ];
    }

    protected $attributes = [
        'notification_email' => true,
        'email_frequency'    => 'daily',
        'language'           => 'en',
        'timezone'           => 'UTC',
        'theme'              => 'system',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * Classes taught by this instructor (direct relationship)
     * This is the main relationship for instructor classes
     */
    public function scheduledClasses()
    {
        return $this->hasMany(ScheduledClass::class, 'instructor_id');
    }

    /**
     * Classes this user has booked (role = member)
     * Pivot table: bookings
     */
    public function bookings()
    {
        return $this->belongsToMany(
                ScheduledClass::class,
                'bookings',
                'user_id',
                'scheduled_class_id'
            )
            ->withTimestamps()
            ->withPivot('created_at');
    }

    /**
     * Receipts belonging to this user.
     */
    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    /**
     * Messages sent by the user
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Messages received by the user
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * All messages (sent and received)
     */
    public function allMessages()
    {
        return Message::where('sender_id', $this->id)
            ->orWhere('receiver_id', $this->id);
    }

    /**
     * Workouts assigned to this user
     */
    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }

    /**
     * Attendance records for this user
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Progress logs for this user
     */
    public function progressLogs()
    {
        return $this->hasMany(ProgressLog::class);
    }

    /**
     * Goals set by this user
     */
    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    /**
     * Nutrition logs for this user
     */
    public function nutritionLogs()
    {
        return $this->hasMany(NutritionLog::class);
    }

    /**
     * Notifications for this user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Payments made by this user
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'member_id');
    }

    /**
     * Subscription for this user
     */
    public function subscription()
    {
        return $this->hasOne(MemberSubscription::class, 'member_id');
    }

    /**
     * Active subscription
     */
    public function activeSubscription()
    {
        return $this->hasOne(MemberSubscription::class, 'member_id')
            ->where('status', 'active')
            ->where('end_date', '>=', now());
    }

    /**
     * The instructor assigned to this member
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Members assigned to this instructor
     */
    public function members()
    {
        return $this->hasMany(User::class, 'instructor_id');
    }

    // =========================================================================
    // Instructor class helpers (direct - no Instructor model needed)
    // =========================================================================

    /**
     * Upcoming classes taught by this instructor.
     * Returns empty collection if user is not an instructor.
     */
    public function upcomingClasses()
    {
        if ($this->role !== 'instructor') {
            return collect();
        }

        return $this->scheduledClasses()
            ->where('date_time', '>', now())
            ->orderBy('date_time');
    }

    /**
     * Past classes taught by this instructor.
     * Returns empty collection if user is not an instructor.
     */
    public function pastClasses()
    {
        if ($this->role !== 'instructor') {
            return collect();
        }

        return $this->scheduledClasses()
            ->where('date_time', '<=', now())
            ->orderByDesc('date_time');
    }

    // =========================================================================
    // Member booking helpers
    // =========================================================================

    /**
     * Upcoming classes this member has booked.
     */
    public function upcomingBookings()
    {
        return $this->bookings()
            ->where('date_time', '>', now())
            ->orderBy('date_time');
    }

    /**
     * Past classes this member has booked.
     */
    public function pastBookings()
    {
        return $this->bookings()
            ->where('date_time', '<=', now())
            ->orderByDesc('date_time');
    }

    /**
     * Check if user has booked a specific class
     */
    public function hasBooked(ScheduledClass $class): bool
    {
        return $this->bookings()->where('scheduled_class_id', $class->id)->exists();
    }

    /**
     * Get the booking record for a specific class
     */
    public function getBookingFor(ScheduledClass $class): ?Booking
    {
        return $this->bookings()->where('scheduled_class_id', $class->id)->first();
    }

    // =========================================================================
    // Message helpers
    // =========================================================================

    /**
     * Get unread messages count
     */
    public function getUnreadMessagesCountAttribute()
    {
        return Message::where('receiver_id', $this->id)
            ->where('read', false)
            ->count();
    }

    /**
     * Get all conversations for this user
     */
    public function getConversationsAttribute()
    {
        return Message::getConversationsForUser($this->id);
    }

    /**
     * Send a message to another user
     */
    public function sendMessageTo($receiverId, $message)
    {
        return Message::send($this->id, $receiverId, $message);
    }

    /**
     * Get messages with a specific user
     */
    public function messagesWith($userId)
    {
        return Message::between($this->id, $userId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Mark all messages from a user as read
     */
    public function markMessagesAsReadFrom($senderId)
    {
        return Message::markAllAsRead($this->id, $senderId);
    }

    // =========================================================================
    // Role checks
    // =========================================================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    public function isInstructor(): bool
    {
        return $this->role === 'instructor' || $this->hasRole('instructor');
    }

    public function isMember(): bool
    {
        return $this->role === 'member' || $this->hasRole('member');
    }

    // =========================================================================
    // Attribute accessors
    // =========================================================================

    public function getRoleDisplayAttribute(): string
    {
        return ucfirst($this->role);
    }

    public function getThemeAttribute($value): string
    {
        return $value ?? 'system';
    }

    public function getLanguageAttribute($value): string
    {
        return $value ?? 'en';
    }

    public function getTimezoneAttribute($value): string
    {
        return $value ?? 'UTC';
    }

    public function getEmailFrequencyAttribute($value): string
    {
        return $value ?? 'daily';
    }

    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return $initials;
    }

    public function getProfilePhotoUrlAttribute($value): string
    {
        if ($value) {
            return asset('storage/' . $value);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=7c3aed&color=fff&bold=true';
    }

    public function wantsEmailNotifications(): bool
    {
        return (bool) $this->notification_email;
    }

    public function updateLastLogin(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeInstructors($query)
    {
        return $query->where('role', 'instructor');
    }

    public function scopeMembers($query)
    {
        return $query->where('role', 'member');
    }

    public function scopeActive($query)
    {
        return $query->whereHas('activeSubscription');
    }

    public function scopeWithUnreadMessages($query)
    {
        return $query->whereHas('receivedMessages', function($q) {
            $q->where('read', false);
        });
    }

    // =========================================================================
    // Statistics
    // =========================================================================

    /**
     * Get total amount spent by this user
     */
    public function getTotalSpentAttribute(): float
    {
        return (float) $this->receipts()->sum('amount');
    }

    /**
     * Get total number of bookings
     */
    public function getTotalBookingsAttribute(): int
    {
        return $this->bookings()->count();
    }

    /**
     * Get attendance rate percentage
     */
    public function getAttendanceRateAttribute(): int
    {
        $total = $this->bookings()->count();
        if ($total === 0) {
            return 0;
        }

        $completed = $this->bookings()
            ->where('date_time', '<', now())
            ->count();

        return (int) round(($completed / $total) * 100);
    }

    /**
     * Get total workout count
     */
    public function getTotalWorkoutsAttribute(): int
    {
        return $this->workouts()->count();
    }

    /**
     * Get completed workout count
     */
    public function getCompletedWorkoutsAttribute(): int
    {
        return $this->workouts()->where('status', 'completed')->count();
    }

    /**
     * Get total hours spent at gym
     */
    public function getTotalHoursAttribute(): float
    {
        return round($this->attendances()->sum('duration_minutes') / 60, 1);
    }

    /**
     * Get current streak
     */
    public function getCurrentStreakAttribute(): int
    {
        $streak = 0;
        $currentDate = now()->startOfDay();

        while (true) {
            $hasWorkout = $this->workouts()
                ->whereDate('date', $currentDate)
                ->where('status', 'completed')
                ->exists();

            if (!$hasWorkout) {
                break;
            }

            $streak++;
            $currentDate->subDay();
        }

        return $streak;
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStatsAttribute(): array
    {
        return [
            'total_workouts' => $this->total_workouts,
            'completed_workouts' => $this->completed_workouts,
            'total_hours' => $this->total_hours,
            'current_streak' => $this->current_streak,
            'attendance_rate' => $this->attendance_rate,
            'unread_messages' => $this->unread_messages_count,
            'total_spent' => $this->total_spent,
        ];
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Check if user has a specific role
     */
    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    /**
     * Assign a role to the user
     */
    public function assignRole($role): self
    {
        $this->update(['role' => $role]);
        return $this;
    }

    /**
     * Get all users by role
     */
    public static function getByRole($role)
    {
        return self::where('role', $role)->get();
    }

    /**
     * Get all instructors
     */
    public static function getInstructors()
    {
        return self::where('role', 'instructor')->get();
    }

    /**
     * Get all members
     */
    public static function getMembers()
    {
        return self::where('role', 'member')->get();
    }

    /**
     * Get all admins
     */
    public static function getAdmins()
    {
        return self::where('role', 'admin')->get();
    }

    // Add these relationships

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('read', false);
    }

    public function notificationSettings()
    {
        return $this->hasOne(NotificationSettings::class);
    }

    // Method to get notification settings (creates default if doesn't exist)
    public function getNotificationSettingsAttribute()
    {
        return $this->notificationSettings()->firstOrCreate(
            [],
            NotificationSettings::getDefaults()
        );
    }
}
