<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'notification_email',
        'email_frequency',
        'language',
        'timezone',
        'theme',
        'last_login_at',
        'last_login_ip',
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

    // =========================================================================
    // Role checks
    // =========================================================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
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
}
