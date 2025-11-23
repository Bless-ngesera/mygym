<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ScheduledClass extends Model
{
    use HasFactory;

    // Allow mass assignment for all attributes
    protected $guarded = [];

    protected $casts = [
        'date_time' => 'datetime',
        'price'     => 'decimal:2', // ✅ ensures price is always treated as decimal
    ];

    /**
     * Instructor relationship (points to users table where role = instructor)
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Class type relationship
     */
public function classType() {
    return $this->belongsTo(ClassType::class);
}


    /**
     * Members relationship (users who booked this class)
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'bookings', 'scheduled_class_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Payments relationship (all payments linked to this class)
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'scheduled_class_id');
    }

    /**
     * Scope: upcoming classes
     */
    public function scopeUpcoming(Builder $query)
    {
        return $query->where('date_time', '>', now());
    }

    /**
     * Scope: classes not booked by the current user
     */
    public function scopeNotBooked(Builder $query)
    {
        if (Auth::check()) {
            return $query->whereDoesntHave('members', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }
        return $query;
    }

    /**
     * Scope: order by soonest date
     */
    public function scopeOldest(Builder $query)
    {
        return $query->orderBy('date_time', 'asc');
    }
}
