<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassType extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'minutes' => 'integer',
        'capacity' => 'integer',
        'price' => 'decimal:2',
    ];

    protected $attributes = [
        'is_active' => true,
        'capacity' => 20,
        'minutes' => 60,
        'difficulty_level' => 'beginner',
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

    // =========================================================================
    // Scopes
    // =========================================================================

    /**
     * Scope: only active class types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: class types with capacity greater than minimum
     */
    public function scopeMinCapacity($query, $min)
    {
        return $query->where('capacity', '>=', $min);
    }

    /**
     * Scope: class types by difficulty level
     */
    public function scopeDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
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
        return ucfirst($this->difficulty_level ?? 'beginner');
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
        $total = 0;
        foreach ($this->scheduledClasses as $class) {
            $total += $class->members()->count();
        }
        return $total;
    }

    /**
     * Get total revenue from this class type
     */
    public function getTotalRevenueAttribute(): float
    {
        $total = 0;
        foreach ($this->scheduledClasses as $class) {
            $total += $class->receipts()->sum('amount');
        }
        return (float) $total;
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

        $totalBookings = 0;
        foreach ($this->scheduledClasses as $class) {
            $totalBookings += $class->members()->count();
        }

        return round(($totalBookings / ($totalClasses * $this->capacity)) * 100, 2);
    }

    /**
     * Get popular classes (most booked)
     */
    public static function getPopular($limit = 4)
    {
        return self::active()
            ->withCount('scheduledClasses')
            ->orderBy('scheduled_classes_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Activate the class type
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the class type
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}
