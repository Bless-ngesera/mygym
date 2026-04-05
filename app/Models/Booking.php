<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'scheduled_class_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $timestamps = true;

    /**
     * Relationship to User (the member who made the booking)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to ScheduledClass (the class that was booked)
     */
    public function scheduledClass()
    {
        return $this->belongsTo(ScheduledClass::class, 'scheduled_class_id');
    }

    /**
     * Accessor to get class type through scheduled class
     */
    public function getClassTypeAttribute()
    {
        return $this->scheduledClass ? $this->scheduledClass->classType : null;
    }

    /**
     * Accessor to get instructor through scheduled class
     */
    public function getInstructorAttribute()
    {
        return $this->scheduledClass ? $this->scheduledClass->instructor : null;
    }

    /**
     * Accessor to get date time through scheduled class
     */
    public function getDateTimeAttribute()
    {
        return $this->scheduledClass ? $this->scheduledClass->date_time : null;
    }

    /**
     * Accessor to get price through scheduled class
     */
    public function getPriceAttribute()
    {
        return $this->scheduledClass ? $this->scheduledClass->price : null;
    }

    /**
     * Check if the booking is for a past class
     */
    public function isPast()
    {
        return $this->scheduledClass && $this->scheduledClass->date_time->isPast();
    }

    /**
     * Check if the booking is for an upcoming class
     */
    public function isUpcoming()
    {
        return $this->scheduledClass && $this->scheduledClass->date_time->isFuture();
    }

    /**
     * Scope for upcoming bookings
     */
    public function scopeUpcoming($query)
    {
        return $query->whereHas('scheduledClass', function($q) {
            $q->where('date_time', '>', now());
        });
    }

    /**
     * Scope for past bookings
     */
    public function scopePast($query)
    {
        return $query->whereHas('scheduledClass', function($q) {
            $q->where('date_time', '<', now());
        });
    }

    /**
     * Get the receipt for this booking
     */
    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'scheduled_class_id', 'scheduled_class_id')
            ->where('user_id', $this->user_id);
    }

    /**
     * Cancel the booking
     */
    public function cancel()
    {
        return $this->delete();
    }
}
