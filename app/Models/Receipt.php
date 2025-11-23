<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'scheduled_class_id',
        'payment_method',
        'amount',
        'reference_number',
        'status',        // ✅ add status field (completed, pending, failed)
        'paid_at',       // ✅ add paid_at timestamp if you track payment time
    ];

    protected $casts = [
        'amount'   => 'decimal:2',   // ✅ ensures amount is always numeric with 2 decimals
        'paid_at'  => 'datetime',    // ✅ cast paid_at to Carbon instance
    ];

    public $timestamps = true; // created_at and updated_at

    /**
     * Relationship: Receipt belongs to a user (member who paid)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Receipt belongs to a scheduled class
     */
    public function scheduledClass()
    {
        return $this->belongsTo(ScheduledClass::class, 'scheduled_class_id');
    }
    /**
     * Scope: only completed receipts
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: receipts in the current month
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

}
