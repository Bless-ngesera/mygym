<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ScheduledClass extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date_time' => 'datetime',
        'price'     => 'decimal:2',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * Instructor relationship - points directly to User model
     * Since instructors are stored in the users table with role = 'instructor'
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Class type relationship
     */
    public function classType()
    {
        return $this->belongsTo(ClassType::class);
    }

    /**
     * Members who booked this class
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'bookings', 'scheduled_class_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Receipts linked to this class
     */
    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    /**
     * Payments linked to this class (if using Payment model)
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'scheduled_class_id');
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    /**
     * Scope: upcoming classes only
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('date_time', '>', now());
    }

    /**
     * Scope: past classes only
     */
    public function scopePast(Builder $query): Builder
    {
        return $query->where('date_time', '<', now());
    }

    /**
     * Scope: today's classes
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('date_time', today());
    }

    /**
     * Scope: this week's classes
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('date_time', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope: exclude classes already booked by the current user
     */
    public function scopeNotBookedByUser(Builder $query): Builder
    {
        if (Auth::check()) {
            return $query->whereDoesntHave('members', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        return $query;
    }

    /**
     * Scope: order by soonest date_time
     * Named scopeSoonest to avoid overriding Laravel's built-in oldest() scope
     */
    public function scopeSoonest(Builder $query): Builder
    {
        return $query->orderBy('date_time', 'asc');
    }

    /**
     * Scope: order by latest date_time
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('date_time', 'desc');
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Check if the class is in the past
     */
    public function isPast(): bool
    {
        return $this->date_time->isPast();
    }

    /**
     * Check if the class is in the future
     */
    public function isUpcoming(): bool
    {
        return $this->date_time->isFuture();
    }

    /**
     * Check if the class is today
     */
    public function isToday(): bool
    {
        return $this->date_time->isToday();
    }

    /**
     * Check if the class is fully booked
     */
    public function isFull(): bool
    {
        $capacity = $this->classType ? $this->classType->capacity : 999;
        return $this->members()->count() >= $capacity;
    }

    /**
     * Get the number of available spots
     */
    public function availableSpots(): int
    {
        $capacity = $this->classType ? $this->classType->capacity : 999;
        $booked = $this->members()->count();
        return max(0, $capacity - $booked);
    }

    /**
     * Check if a specific user has booked this class
     */
    public function isBookedBy(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the booking record for a specific user
     */
    public function getBookingFor(User $user): ?Booking
    {
        return Booking::where('scheduled_class_id', $this->id)
            ->where('user_id', $user->id)
            ->first();
    }

    /**
     * Get total revenue from this class
     */
    public function getTotalRevenueAttribute(): float
    {
        return (float) $this->receipts()->sum('amount');
    }

    /**
     * Get the number of members booked
     */
    public function getBookingsCountAttribute(): int
    {
        return $this->members()->count();
    }

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'UGX ' . number_format($this->price, 0);
    }

    /**
     * Get formatted date and time
     */
    public function getFormattedDateTimeAttribute(): string
    {
        return $this->date_time->format('l, F j, Y \a\t g:i A');
    }

    /**
     * Get short formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date_time->format('M d, Y');
    }

    /**
     * Get formatted time only
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->date_time->format('g:i A');
    }

    /**
     * Get day of week
     */
    public function getDayOfWeekAttribute(): string
    {
        return $this->date_time->format('l');
    }

    // =========================================================================
    // Static Methods
    // =========================================================================

    /**
     * Get upcoming classes with eager loading
     */
    public static function getUpcomingWithDetails()
    {
        return self::upcoming()
            ->with(['classType', 'instructor'])
            ->soonest()
            ->get();
    }

    /**
     * Get classes available for booking by current user
     */
    public static function getAvailableForBooking()
    {
        return self::upcoming()
            ->with(['classType', 'instructor'])
            ->notBookedByUser()
            ->soonest()
            ->paginate(12);
    }
}
