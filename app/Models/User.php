<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\ScheduledClass;
use App\Models\Booking;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        // User preferences from migration
        'notification_email',
        'email_frequency',
        'language',
        'timezone',
        'theme',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_email' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'notification_email' => true,
        'email_frequency' => 'daily',
        'language' => 'en',
        'timezone' => 'UTC',
        'theme' => 'system',
    ];

    /**
     * Get the classes that this user (as an instructor) teaches.
     */
    public function scheduledClasses()
    {
        return $this->hasMany(ScheduledClass::class, 'instructor_id');
    }

    /**
     * Alias for scheduledClasses() - classes this instructor teaches.
     */
    public function instructedClasses()
    {
        return $this->hasMany(ScheduledClass::class, 'instructor_id');
    }

    /**
     * Get the classes that this user (as a member) has booked.
     */
    public function bookings()
    {
        return $this->belongsToMany(ScheduledClass::class, 'bookings', 'user_id', 'scheduled_class_id')
                    ->withTimestamps()
                    ->withPivot('created_at');
    }

    /**
     * Get the receipts for this user.
     */
    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is an instructor.
     */
    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    /**
     * Check if user is a member.
     */
    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    /**
     * Get user's role in a human-readable format.
     */
    public function getRoleDisplayAttribute(): string
    {
        return ucfirst($this->role);
    }

    /**
     * Get user's theme with fallback.
     */
    public function getThemeAttribute($value): string
    {
        return $value ?? 'system';
    }

    /**
     * Get user's preferred language.
     */
    public function getLanguageAttribute($value): string
    {
        return $value ?? 'en';
    }

    /**
     * Get user's timezone with fallback.
     */
    public function getTimezoneAttribute($value): string
    {
        return $value ?? 'UTC';
    }

    /**
     * Get email frequency with fallback.
     */
    public function getEmailFrequencyAttribute($value): string
    {
        return $value ?? 'daily';
    }

    /**
     * Get notification preference with boolean cast.
     */
    public function wantsEmailNotifications(): bool
    {
        return (bool) $this->notification_email;
    }

    /**
     * Update last login information.
     */
    public function updateLastLogin(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    /**
     * Scope a query to only include admins.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope a query to only include instructors.
     */
    public function scopeInstructors($query)
    {
        return $query->where('role', 'instructor');
    }

    /**
     * Scope a query to only include members.
     */
    public function scopeMembers($query)
    {
        return $query->where('role', 'member');
    }

    /**
     * Get the user's upcoming booked classes.
     */
    public function upcomingBookings()
    {
        return $this->bookings()
                    ->where('date_time', '>', now())
                    ->orderBy('date_time');
    }

    /**
     * Get the user's past booked classes.
     */
    public function pastBookings()
    {
        return $this->bookings()
                    ->where('date_time', '<=', now())
                    ->orderByDesc('date_time');
    }

    /**
     * Get the instructor's upcoming classes.
     */
    public function upcomingClasses()
    {
        return $this->instructedClasses()
                    ->where('date_time', '>', now())
                    ->orderBy('date_time');
    }

    /**
     * Get the instructor's past classes.
     */
    public function pastClasses()
    {
        return $this->instructedClasses()
                    ->where('date_time', '<=', now())
                    ->orderByDesc('date_time');
    }
}
