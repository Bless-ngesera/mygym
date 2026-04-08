<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScheduledClass extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date_time' => 'datetime',
        'price'     => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
                    ->withTimestamps()
                    ->withPivot('created_at', 'status');
    }

    /**
     * Receipts linked to this class
     */
    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'scheduled_class_id');
    }

    /**
     * Payments linked to this class (if using Payment model)
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'scheduled_class_id');
    }

    /**
     * Bookings relationship
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'scheduled_class_id');
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
     * Scope: this month's classes
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereYear('date_time', now()->year)
                     ->whereMonth('date_time', now()->month);
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
     * Scope: classes for a specific instructor
     */
    public function scopeForInstructor(Builder $query, $instructorId): Builder
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * Scope: classes with available spots
     */
    public function scopeWithAvailableSpots(Builder $query): Builder
    {
        return $query->whereHas('classType', function($q) {
            $q->whereRaw('capacity > (SELECT COUNT(*) FROM bookings WHERE bookings.scheduled_class_id = scheduled_classes.id)');
        });
    }

    /**
     * Scope: order by soonest date_time
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

    /**
     * Scope: by date range
     */
    public function scopeDateBetween(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('date_time', [$startDate, $endDate]);
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
     * Get the booking percentage
     */
    public function getBookingPercentageAttribute(): float
    {
        $capacity = $this->classType ? $this->classType->capacity : 999;
        if ($capacity === 0) return 0;
        return round(($this->members()->count() / $capacity) * 100, 2);
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
     * Get total revenue for instructor from this class
     */
    public function getInstructorRevenueAttribute(): float
    {
        // Assuming instructor gets 70% of the revenue (adjust as needed)
        $commissionRate = 0.70;
        return $this->total_revenue * $commissionRate;
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

    /**
     * Get duration in hours
     */
    public function getDurationHoursAttribute(): float
    {
        // Assuming duration is stored in minutes, adjust as needed
        return $this->duration_minutes ?? 60 / 60;
    }

    /**
     * Check if class can be cancelled
     */
    public function getCanCancelAttribute(): bool
    {
        // Can cancel if more than 2 hours before class starts
        return $this->date_time->diffInHours(now()) >= 2;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->isPast()) {
            return 'bg-gray-100 text-gray-800';
        }
        if ($this->isFull()) {
            return 'bg-red-100 text-red-800';
        }
        if ($this->isToday()) {
            return 'bg-green-100 text-green-800';
        }
        return 'bg-blue-100 text-blue-800';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->isPast()) {
            return 'Completed';
        }
        if ($this->isFull()) {
            return 'Full';
        }
        if ($this->isToday()) {
            return 'Today';
        }
        return 'Available';
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
            ->withAvailableSpots()
            ->soonest()
            ->paginate(12);
    }

    /**
     * Get instructor's classes for a specific month
     */
    public static function getInstructorClassesForMonth($instructorId, $month, $year)
    {
        return self::forInstructor($instructorId)
            ->whereYear('date_time', $year)
            ->whereMonth('date_time', $month)
            ->with(['classType', 'members'])
            ->orderBy('date_time', 'asc')
            ->get();
    }

    /**
     * Get earnings report for instructor
     */
    public static function getInstructorEarningsReport($instructorId, $startDate = null, $endDate = null)
    {
        $query = self::forInstructor($instructorId)
            ->past()
            ->with(['receipts', 'classType']);

        if ($startDate && $endDate) {
            $query->dateBetween($startDate, $endDate);
        }

        $classes = $query->get();

        $totalRevenue = $classes->sum(function($class) {
            return $class->total_revenue;
        });

        $instructorEarnings = $classes->sum(function($class) {
            return $class->instructor_revenue;
        });

        $totalBookings = $classes->sum(function($class) {
            return $class->bookings_count;
        });

        return [
            'total_classes' => $classes->count(),
            'total_revenue' => $totalRevenue,
            'instructor_earnings' => $instructorEarnings,
            'total_bookings' => $totalBookings,
            'average_class_revenue' => $classes->count() > 0 ? $totalRevenue / $classes->count() : 0,
            'classes' => $classes
        ];
    }

    /**
     * Get upcoming classes count for instructor
     */
    public static function getUpcomingCountForInstructor($instructorId)
    {
        return self::forInstructor($instructorId)
            ->upcoming()
            ->count();
    }

    /**
     * Get popular classes (most booked)
     */
    public static function getPopularClasses($limit = 5)
    {
        return self::withCount('members')
            ->orderByDesc('members_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get monthly class statistics
     */
    public static function getMonthlyStatistics($year = null, $month = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $classes = self::whereYear('date_time', $year)
            ->whereMonth('date_time', $month)
            ->get();

        return [
            'total_classes' => $classes->count(),
            'total_bookings' => $classes->sum(function($class) {
                return $class->bookings_count;
            }),
            'total_revenue' => $classes->sum(function($class) {
                return $class->total_revenue;
            }),
            'average_attendance' => $classes->avg(function($class) {
                return $class->booking_percentage;
            }),
        ];
    }
}
