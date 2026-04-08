<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ClassType extends Model
{
    use HasFactory;

    protected $table = 'class_types';

    protected $fillable = [
        'name',
        'description',
        'minutes',
        'capacity',
        'image',
        'color',
        'icon',
        'is_active',
        'difficulty_level',
        'price',
        'instructor_commission_percentage',
        'requires_equipment',
        'equipment_list',
        'benefits',
        'meetup_point',
        'what_to_bring'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'minutes' => 'integer',
        'capacity' => 'integer',
        'price' => 'decimal:2',
        'instructor_commission_percentage' => 'decimal:2',
        'requires_equipment' => 'boolean',
        'equipment_list' => 'array',
        'benefits' => 'array',
    ];

    protected $attributes = [
        'is_active' => true,
        'capacity' => 20,
        'minutes' => 60,
        'difficulty_level' => 'beginner',
        'instructor_commission_percentage' => 70.00,
        'requires_equipment' => false,
    ];

    // Difficulty levels constants
    const DIFFICULTY_BEGINNER = 'beginner';
    const DIFFICULTY_INTERMEDIATE = 'intermediate';
    const DIFFICULTY_ADVANCED = 'advanced';
    const DIFFICULTY_ALL = 'all';

    const DIFFICULTY_LEVELS = [
        self::DIFFICULTY_BEGINNER => 'Beginner',
        self::DIFFICULTY_INTERMEDIATE => 'Intermediate',
        self::DIFFICULTY_ADVANCED => 'Advanced',
        self::DIFFICULTY_ALL => 'All Levels',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * Get the scheduled classes for this class type
     */
    public function scheduledClasses()
    {
        return $this->hasMany(ScheduledClass::class);
    }

    /**
     * Get upcoming scheduled classes for this class type
     */
    public function upcomingClasses()
    {
        return $this->scheduledClasses()
            ->where('date_time', '>', now())
            ->orderBy('date_time', 'asc');
    }

    /**
     * Get past scheduled classes for this class type
     */
    public function pastClasses()
    {
        return $this->scheduledClasses()
            ->where('date_time', '<', now())
            ->orderBy('date_time', 'desc');
    }

    /**
     * Get bookings through scheduled classes
     */
    public function bookings()
    {
        return $this->hasManyThrough(
            Booking::class,
            ScheduledClass::class,
            'class_type_id',
            'scheduled_class_id',
            'id',
            'id'
        );
    }

    /**
     * Get receipts through scheduled classes
     */
    public function receipts()
    {
        return $this->hasManyThrough(
            Receipt::class,
            ScheduledClass::class,
            'class_type_id',
            'scheduled_class_id',
            'id',
            'id'
        );
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    /**
     * Scope: only active class types
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: only inactive class types
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope: class types with capacity greater than minimum
     */
    public function scopeMinCapacity(Builder $query, int $min): Builder
    {
        return $query->where('capacity', '>=', $min);
    }

    /**
     * Scope: class types with capacity less than maximum
     */
    public function scopeMaxCapacity(Builder $query, int $max): Builder
    {
        return $query->where('capacity', '<=', $max);
    }

    /**
     * Scope: class types by difficulty level
     */
    public function scopeDifficulty(Builder $query, string $level): Builder
    {
        if ($level === self::DIFFICULTY_ALL) {
            return $query;
        }
        return $query->where('difficulty_level', $level);
    }

    /**
     * Scope: class types by price range
     */
    public function scopePriceBetween(Builder $query, float $min, float $max): Builder
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Scope: class types with duration less than minutes
     */
    public function scopeDurationLessThan(Builder $query, int $minutes): Builder
    {
        return $query->where('minutes', '<=', $minutes);
    }

    /**
     * Scope: class types with duration greater than minutes
     */
    public function scopeDurationGreaterThan(Builder $query, int $minutes): Builder
    {
        return $query->where('minutes', '>=', $minutes);
    }

    /**
     * Scope: search by name or description
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Check if class type is active
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'UGX ' . number_format($this->price ?? 0, 0);
    }

    /**
     * Get difficulty level badge color
     */
    public function getDifficultyBadgeColorAttribute(): string
    {
        return match($this->difficulty_level) {
            'beginner' => 'green',
            'intermediate' => 'yellow',
            'advanced' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get difficulty level display name
     */
    public function getDifficultyDisplayAttribute(): string
    {
        return self::DIFFICULTY_LEVELS[$this->difficulty_level] ?? ucfirst($this->difficulty_level ?? 'beginner');
    }

    /**
     * Get total number of classes scheduled for this type
     */
    public function getTotalClassesCountAttribute(): int
    {
        return $this->scheduledClasses()->count();
    }

    /**
     * Get total number of bookings across all classes of this type
     */
    public function getTotalBookingsCountAttribute(): int
    {
        return $this->bookings()->count();
    }

    /**
     * Get total revenue from this class type
     */
    public function getTotalRevenueAttribute(): float
    {
        return (float) $this->receipts()->sum('amount');
    }

    /**
     * Get instructor earnings from this class type
     */
    public function getInstructorEarningsAttribute(): float
    {
        $commissionRate = ($this->instructor_commission_percentage ?? 70) / 100;
        return $this->total_revenue * $commissionRate;
    }

    /**
     * Get platform earnings from this class type
     */
    public function getPlatformEarningsAttribute(): float
    {
        $platformRate = 100 - ($this->instructor_commission_percentage ?? 70);
        return $this->total_revenue * ($platformRate / 100);
    }

    /**
     * Get average attendance rate
     */
    public function getAverageAttendanceRateAttribute(): float
    {
        $totalClasses = $this->scheduledClasses()->count();
        if ($totalClasses === 0) {
            return 0;
        }

        $totalBookings = $this->total_bookings_count;
        $totalCapacity = $totalClasses * $this->capacity;

        return $totalCapacity > 0 ? round(($totalBookings / $totalCapacity) * 100, 2) : 0;
    }

    /**
     * Get upcoming classes count
     */
    public function getUpcomingClassesCountAttribute(): int
    {
        return $this->upcomingClasses()->count();
    }

    /**
     * Get next class datetime
     */
    public function getNextClassDateTimeAttribute(): ?string
    {
        $nextClass = $this->upcomingClasses()->first();
        return $nextClass ? $nextClass->formatted_date_time : null;
    }

    /**
     * Get duration in hours
     */
    public function getDurationHoursAttribute(): float
    {
        return round($this->minutes / 60, 1);
    }

    /**
     * Get duration display text
     */
    public function getDurationDisplayAttribute(): string
    {
        if ($this->minutes >= 60) {
            $hours = floor($this->minutes / 60);
            $mins = $this->minutes % 60;
            return $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '');
        }
        return $this->minutes . ' minutes';
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image && file_exists(public_path('storage/' . $this->image))) {
            return asset('storage/' . $this->image);
        }
        return asset('images/default-class.jpg');
    }

    /**
     * Get icon HTML
     */
    public function getIconHtmlAttribute(): string
    {
        $icons = [
            'yoga' => '🧘',
            'pilates' => '💪',
            'cardio' => '🏃',
            'strength' => '🏋️',
            'dance' => '💃',
            'boxing' => '🥊',
            'meditation' => '🧠',
            'hiit' => '⚡',
        ];

        return $icons[strtolower($this->icon ?? '')] ?? '🏋️';
    }

    // =========================================================================
    // Static Methods
    // =========================================================================

    /**
     * Get popular class types (most booked)
     */
    public static function getPopular($limit = 4)
    {
        return self::active()
            ->withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get class types by popularity with cache
     */
    public static function getPopularCached($limit = 4)
    {
        $cacheKey = 'popular_class_types_' . $limit;

        return Cache::remember($cacheKey, 3600, function() use ($limit) {
            return self::getPopular($limit);
        });
    }

    /**
     * Get all active class types with upcoming class count
     */
    public static function getActiveWithUpcomingCount()
    {
        return self::active()
            ->withCount('upcomingClasses')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get class types grouped by difficulty
     */
    public static function getGroupedByDifficulty()
    {
        $grouped = [];

        foreach (self::DIFFICULTY_LEVELS as $key => $label) {
            $grouped[$key] = [
                'label' => $label,
                'class_types' => self::active()
                    ->difficulty($key)
                    ->orderBy('name')
                    ->get()
            ];
        }

        return $grouped;
    }

    /**
     * Get revenue report for all class types
     */
    public static function getRevenueReport($startDate = null, $endDate = null)
    {
        $query = self::active();

        if ($startDate && $endDate) {
            $query->whereHas('scheduledClasses', function($q) use ($startDate, $endDate) {
                $q->whereBetween('date_time', [$startDate, $endDate]);
            });
        }

        $classTypes = $query->get();

        $report = [];
        $totalRevenue = 0;
        $totalInstructorEarnings = 0;
        $totalPlatformEarnings = 0;

        foreach ($classTypes as $classType) {
            $revenue = $classType->total_revenue;
            $instructorEarnings = $classType->instructor_earnings;
            $platformEarnings = $classType->platform_earnings;

            $report[] = [
                'id' => $classType->id,
                'name' => $classType->name,
                'total_classes' => $classType->total_classes_count,
                'total_bookings' => $classType->total_bookings_count,
                'total_revenue' => $revenue,
                'instructor_earnings' => $instructorEarnings,
                'platform_earnings' => $platformEarnings,
                'average_attendance' => $classType->average_attendance_rate,
            ];

            $totalRevenue += $revenue;
            $totalInstructorEarnings += $instructorEarnings;
            $totalPlatformEarnings += $platformEarnings;
        }

        return [
            'class_types' => $report,
            'summary' => [
                'total_class_types' => $classTypes->count(),
                'total_revenue' => $totalRevenue,
                'total_instructor_earnings' => $totalInstructorEarnings,
                'total_platform_earnings' => $totalPlatformEarnings,
            ]
        ];
    }

    /**
     * Activate the class type
     */
    public function activate(): self
    {
        $this->update(['is_active' => true]);
        $this->clearCache();
        return $this;
    }

    /**
     * Deactivate the class type
     */
    public function deactivate(): self
    {
        $this->update(['is_active' => false]);
        $this->clearCache();
        return $this;
    }

    /**
     * Clear cache for this class type
     */
    public function clearCache(): void
    {
        Cache::forget('popular_class_types_4');
        Cache::forget('popular_class_types_6');
        Cache::forget('class_types_list');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(): self
    {
        return $this->is_active ? $this->deactivate() : $this->activate();
    }
}
