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
     * Payments linked to this class
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'scheduled_class_id');
    }

    /**
     * Scope: upcoming classes only
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('date_time', '>', now());
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
}
