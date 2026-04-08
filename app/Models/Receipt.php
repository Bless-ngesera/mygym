<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Receipt extends Model
{
    use HasFactory;

    protected $table = 'receipts';

    protected $fillable = [
        'user_id',
        'scheduled_class_id',
        'instructor_id',
        'payment_method',
        'amount',
        'reference_number',
        'status',
        'paid_at',
        'notes',
        'transaction_id',
        'payment_channel',
        'currency'
    ];

    protected $casts = [
        'amount'   => 'decimal:2',
        'paid_at'  => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'pending',
        'currency' => 'UGX',
        'payment_method' => 'mobile_money',
    ];

    public $timestamps = true;

    /**
     * Relationship: Receipt belongs to a user (member who paid)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Receipt belongs to a scheduled class
     */
    public function scheduledClass()
    {
        return $this->belongsTo(ScheduledClass::class, 'scheduled_class_id');
    }

    /**
     * Relationship: Receipt belongs to an instructor
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Accessor: Get formatted amount with currency
     */
    public function getFormattedAmountAttribute()
    {
        return $this->currency . ' ' . number_format($this->amount, 0);
    }

    /**
     * Accessor: Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('M d, Y h:i A');
    }

    /**
     * Accessor: Get short date
     */
    public function getShortDateAttribute()
    {
        return $this->created_at->format('M d, Y');
    }

    /**
     * Accessor: Get time only
     */
    public function getTimeOnlyAttribute()
    {
        return $this->created_at->format('h:i A');
    }

    /**
     * Accessor: Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'completed', 'paid' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'failed', 'cancelled' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Accessor: Get status label
     */
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Accessor: Check if receipt is completed
     */
    public function getIsCompletedAttribute()
    {
        return in_array($this->status, ['completed', 'paid']);
    }

    /**
     * Scope: only completed receipts
     */
    public function scopeCompleted(Builder $query)
    {
        return $query->whereIn('status', ['completed', 'paid']);
    }

    /**
     * Scope: only pending receipts
     */
    public function scopePending(Builder $query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: only failed receipts
     */
    public function scopeFailed(Builder $query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope: receipts in the current month
     */
    public function scopeThisMonth(Builder $query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    /**
     * Scope: receipts in the current year
     */
    public function scopeThisYear(Builder $query)
    {
        return $query->whereYear('created_at', now()->year);
    }

    /**
     * Scope: receipts for a specific instructor
     */
    public function scopeForInstructor(Builder $query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId)
            ->orWhereHas('scheduledClass', function($q) use ($instructorId) {
                $q->where('instructor_id', $instructorId);
            });
    }

    /**
     * Scope: receipts for a specific member
     */
    public function scopeForMember(Builder $query, $memberId)
    {
        return $query->where('user_id', $memberId);
    }

    /**
     * Scope: receipts by date range
     */
    public function scopeDateBetween(Builder $query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: receipts for a specific month and year
     */
    public function scopeForMonth(Builder $query, $month, $year)
    {
        return $query->whereMonth('created_at', $month)
                     ->whereYear('created_at', $year);
    }

    /**
     * Scope: receipts with amount greater than
     */
    public function scopeAmountGreaterThan(Builder $query, $amount)
    {
        return $query->where('amount', '>', $amount);
    }

    /**
     * Scope: receipts with amount less than
     */
    public function scopeAmountLessThan(Builder $query, $amount)
    {
        return $query->where('amount', '<', $amount);
    }

    /**
     * Get total earnings for a specific instructor
     */
    public static function getTotalEarningsForInstructor($instructorId)
    {
        return static::forInstructor($instructorId)
            ->completed()
            ->sum('amount');
    }

    /**
     * Get monthly earnings for a specific instructor
     */
    public static function getMonthlyEarningsForInstructor($instructorId, $month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        return static::forInstructor($instructorId)
            ->completed()
            ->forMonth($month, $year)
            ->sum('amount');
    }

    /**
     * Get earnings by payment method
     */
    public static function getEarningsByPaymentMethod($instructorId)
    {
        return static::forInstructor($instructorId)
            ->completed()
            ->select('payment_method', \DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();
    }

    /**
     * Get monthly earnings trend for charts
     */
    public static function getMonthlyTrend($instructorId, $months = 12)
    {
        $trend = [];
        $currentDate = now()->subMonths($months - 1);

        for ($i = 0; $i < $months; $i++) {
            $date = $currentDate->copy()->addMonths($i);
            $month = $date->month;
            $year = $date->year;

            $earnings = static::forInstructor($instructorId)
                ->completed()
                ->forMonth($month, $year)
                ->sum('amount');

            $trend[] = [
                'month' => $date->format('M Y'),
                'earnings' => $earnings,
                'month_num' => $month,
                'year' => $year
            ];
        }

        return $trend;
    }

    /**
     * Generate unique reference number
     */
    public static function generateReferenceNumber()
    {
        $prefix = 'RCP';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -6));

        $reference = $prefix . $date . $random;

        // Ensure uniqueness
        while (static::where('reference_number', $reference)->exists()) {
            $random = strtoupper(substr(uniqid(), -6));
            $reference = $prefix . $date . $random;
        }

        return $reference;
    }

    /**
     * Mark receipt as completed
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now()
        ]);

        return $this;
    }

    /**
     * Mark receipt as pending
     */
    public function markAsPending()
    {
        $this->update([
            'status' => 'pending',
            'paid_at' => null
        ]);

        return $this;
    }

    /**
     * Mark receipt as failed
     */
    public function markAsFailed()
    {
        $this->update([
            'status' => 'failed',
            'paid_at' => null
        ]);

        return $this;
    }

    /**
     * Mark receipt as refunded
     */
    public function markAsRefunded()
    {
        $this->update([
            'status' => 'refunded'
        ]);

        return $this;
    }

    /**
     * Check if receipt can be refunded
     */
    public function getCanRefundAttribute()
    {
        return $this->is_completed && $this->created_at->diffInDays(now()) <= 30;
    }

    /**
     * Get earnings summary for dashboard
     */
    public static function getEarningsSummary($instructorId)
    {
        $total = static::forInstructor($instructorId)->completed()->sum('amount');
        $currentMonth = static::getMonthlyEarningsForInstructor($instructorId);
        $previousMonth = static::getMonthlyEarningsForInstructor(
            $instructorId,
            now()->subMonth()->month,
            now()->subMonth()->year
        );

        $growth = $previousMonth > 0
            ? (($currentMonth - $previousMonth) / $previousMonth) * 100
            : ($currentMonth > 0 ? 100 : 0);

        $totalClasses = ScheduledClass::where('instructor_id', $instructorId)->count();
        $averagePerClass = $totalClasses > 0 ? $total / $totalClasses : 0;

        return [
            'total_earnings' => $total,
            'current_month' => $currentMonth,
            'previous_month' => $previousMonth,
            'growth_percentage' => round($growth, 2),
            'total_classes' => $totalClasses,
            'average_per_class' => round($averagePerClass, 2),
            'pending_payments' => static::forInstructor($instructorId)->pending()->sum('amount'),
        ];
    }
}
